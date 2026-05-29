<?php

namespace App\Services;

use App\Models\SiswaTahunAjaran;
use App\Models\TagihanIuran;
use App\Models\TagihanSpp;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransaksiService
{
    public function __construct(
        private readonly TunggakanService $tunggakanService,
    ) {}

    /**
     * Generate nomor transaksi unik.
     * Format: TRX-0001, TRX-0042, TRX-1234
     *
     * @return string
     */
    public function generateNoTransaksi(): string
    {
        $lastId = Transaksi::max('id') ?? 0;

        return 'TRX-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Simpan transaksi penerimaan (bulk payment) secara atomik.
     *
     * Seluruh proses dibungkus dalam DB::transaction() — jika ada satu item
     * yang gagal, semua perubahan di-rollback otomatis.
     *
     * @param  array  $data  Data tervalidasi dari SimpanPenerimaanRequest
     * @param  User   $user  Operator yang mencatat
     * @return Transaksi
     */
    public function simpanPenerimaan(array $data, User $user): Transaksi
    {
        return DB::transaction(function () use ($data, $user) {
            $sta = SiswaTahunAjaran::findOrFail($data['siswa_tahun_ajaran_id']);

            // Hitung total dari semua item
            $totalBayar = collect($data['items'])->sum('nominal');

            // Buat header transaksi
            $transaksi = Transaksi::create([
                'no_transaksi'          => $this->generateNoTransaksi(),
                'siswa_tahun_ajaran_id' => $sta->id,
                'user_id'               => $user->id,
                'tanggal'               => $data['tanggal'],
                'total_bayar'           => $totalBayar,
                'keterangan'            => $data['keterangan'] ?? null,
            ]);

            // Proses setiap item pembayaran
            foreach ($data['items'] as $item) {
                $jenis   = $item['jenis'];
                $nominal = (int) $item['nominal'];

                // Simpan detail transaksi
                TransaksiDetail::create([
                    'transaksi_id'        => $transaksi->id,
                    'jenis'               => $jenis,
                    'jenis_penerimaan_id' => $item['jenis_penerimaan_id'] ?? null,
                    'bulan'               => $item['bulan'] ?? null,
                    'tahun'               => $item['tahun'] ?? null,
                    'nominal'             => $nominal,
                ]);

                // Update tagihan yang bersangkutan
                match ($jenis) {
                    'spp'       => $this->updateTagihanSpp($sta, $item, $nominal),
                    'iuran'     => $this->updateTagihanIuran($item, $nominal),
                    'tunggakan' => null, // dihitung via TunggakanService, tidak ada tabel tagihan
                    default     => null,
                };
            }

            return $transaksi;
        });
    }

    /**
     * Update tagihan SPP berdasarkan bulan & tahun yang dibayar.
     */
    private function updateTagihanSpp(SiswaTahunAjaran $sta, array $item, int $nominal): void
    {
        $tagihan = TagihanSpp::where('siswa_tahun_ajaran_id', $sta->id)
            ->where('bulan', $item['bulan'])
            ->where('tahun', $item['tahun'])
            ->firstOrFail();

        $tagihan->bayar($nominal);
    }

    /**
     * Update tagihan iuran berdasarkan jenis_penerimaan.
     */
    private function updateTagihanIuran(array $item, int $nominal): void
    {
        $tagihan = TagihanIuran::findOrFail($item['tagihan_iuran_id']);
        $tagihan->bayar($nominal);
    }
}
