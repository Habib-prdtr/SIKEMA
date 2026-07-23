<?php

namespace App\Services;

use App\Models\JenisPenerimaan;
use App\Models\Pengeluaran;
use App\Models\PosBiaya;
use App\Models\SaldoAwal;
use App\Models\SiswaTahunAjaran;
use App\Models\TagihanSpp;
use App\Models\TahunAjaran;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    protected TunggakanService $tunggakanService;

    public function __construct(TunggakanService $tunggakanService)
    {
        $this->tunggakanService = $tunggakanService;
    }

    public function getJumlahSiswa(?TahunAjaran $tahunAktif): int
    {
        if (!$tahunAktif) return 0;
        return SiswaTahunAjaran::where('tahun_ajaran_id', $tahunAktif->id)->count();
    }

    public function getTotalPenerimaanBulanIni(?TahunAjaran $tahunAktif): int
    {
        if (!$tahunAktif) return 0;
        return Transaksi::where('tahun_ajaran_id', $tahunAktif->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('total_bayar');
    }

    public function getTotalPengeluaranBulanIni(?TahunAjaran $tahunAktif): int
    {
        if (!$tahunAktif) return 0;
        return Pengeluaran::whereHas('posBiaya', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah');
    }

    public function getTunggakanData(?TahunAjaran $tahunAktif): array
    {
        if (!$tahunAktif) {
            return ['siswaAdaTunggakan' => 0, 'totalTunggakanAwal' => 0];
        }

        $siswaDenganTunggakan = SiswaTahunAjaran::where('tahun_ajaran_id', $tahunAktif->id)
            ->where('tunggakan_awal', '>', 0)
            ->with('transaksi.details')
            ->get();

        $siswaAdaTunggakan = 0;
        $totalTunggakanAwal = 0;

        foreach ($siswaDenganTunggakan as $sta) {
            $sisa = $this->tunggakanService->hitungSisa($sta);
            if ($sisa > 0) {
                $siswaAdaTunggakan++;
                $totalTunggakanAwal += $sisa;
            }
        }

        return compact('siswaAdaTunggakan', 'totalTunggakanAwal');
    }

    public function getSppBelumLunasBulanIni(?TahunAjaran $tahunAktif): int
    {
        if (!$tahunAktif) return 0;
        return TagihanSpp::whereHas('siswaTahunAjaran', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->where('bulan', now()->month)
            ->where('tahun', now()->year)
            ->whereIn('status', [TagihanSpp::STATUS_BELUM, TagihanSpp::STATUS_CICILAN])
            ->count();
    }

    public function getTotalSaldo(?TahunAjaran $tahunAktif): int
    {
        if (!$tahunAktif) return 0;

        $saldoAwal = SaldoAwal::where('tahun_ajaran_id', $tahunAktif->id)->value('jumlah') ?? 0;
        $totalAnggaran = (int) PosBiaya::where('tahun_ajaran_id', $tahunAktif->id)->sum('anggaran');
        $totalPenerimaan = Transaksi::whereHas('siswaTahunAjaran', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))->sum('total_bayar');
        $totalPengeluaran = Pengeluaran::whereHas('posBiaya', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))->sum('jumlah');

        return (int) ($saldoAwal + $totalAnggaran + $totalPenerimaan - $totalPengeluaran);
    }

    public function getGrafikBulanan(?TahunAjaran $tahunAktif): array
    {
        $bulanLabels = collect();
        $dataPenerimaan = collect();
        $dataPengeluaran = collect();

        if ($tahunAktif) {
            for ($i = 5; $i >= 0; $i--) {
                $bulan = now()->subMonths($i)->month;
                $tahun = now()->subMonths($i)->year;
                $label = now()->subMonths($i)->locale('id')->isoFormat('MMM');

                $pemasukan = Transaksi::whereHas('siswaTahunAjaran', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->sum('total_bayar');

                $keluar = Pengeluaran::whereHas('posBiaya', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->sum('jumlah');

                $bulanLabels->push($label);
                $dataPenerimaan->push((int) $pemasukan);
                $dataPengeluaran->push((int) $keluar);
            }
        }

        return compact('bulanLabels', 'dataPenerimaan', 'dataPengeluaran');
    }

    public function getGrafikPenerimaanPerJenis(?TahunAjaran $tahunAktif): array
    {
        $data = collect();

        if (!$tahunAktif) {
            return [
                'data' => $data,
                'maxVal' => 0,
            ];
        }

        // 1. SPP
        $totalSpp = (int) TransaksiDetail::where('jenis', 'spp')
            ->whereHas('transaksi.siswaTahunAjaran', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->sum('nominal');

        // 2. Tunggakan
        $totalTunggakan = (int) TransaksiDetail::where('jenis', 'tunggakan')
            ->whereHas('transaksi.siswaTahunAjaran', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->sum('nominal');

        // 3. Jenis Penerimaan (Iuran)
        $jenisPenerimaanList = JenisPenerimaan::where('tahun_ajaran_id', $tahunAktif->id)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        $iuranSums = TransaksiDetail::where('jenis', 'iuran')
            ->whereHas('transaksi.siswaTahunAjaran', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->groupBy('jenis_penerimaan_id')
            ->selectRaw('jenis_penerimaan_id, SUM(nominal) as total')
            ->pluck('total', 'jenis_penerimaan_id');

        $data->push([
            'nama' => 'SPP',
            'total' => $totalSpp,
        ]);

        $data->push([
            'nama' => 'Tunggakan',
            'total' => $totalTunggakan,
        ]);

        foreach ($jenisPenerimaanList as $jp) {
            $data->push([
                'nama' => $jp->nama,
                'total' => (int) ($iuranSums[$jp->id] ?? 0),
            ]);
        }

        $maxVal = (int) ($data->max('total') ?: 0);

        return [
            'data' => $data,
            'maxVal' => $maxVal,
        ];
    }
    public function getTransaksiTerbaru(?TahunAjaran $tahunAktif): Collection
    {
        if (!$tahunAktif) return collect();

        return Transaksi::with(['siswaTahunAjaran.siswa', 'user'])
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(5)
            ->get();
    }

    /**
     * Hitung jumlah siswa yang belum melunasi SPP bulan-bulan sebelumnya (terlewat).
     */
    public function getSppTerlewatBelumLunas(?TahunAjaran $tahunAktif): int
    {
        if (!$tahunAktif) return 0;

        return TagihanSpp::whereHas('siswaTahunAjaran', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->whereIn('status', [TagihanSpp::STATUS_BELUM, TagihanSpp::STATUS_CICILAN])
            ->where(function ($q) {
                $q->where('tahun', '<', now()->year)
                  ->orWhere(function ($sub) {
                      $sub->where('tahun', now()->year)
                          ->where('bulan', '<', now()->month);
                  });
            })
            ->distinct('siswa_tahun_ajaran_id')
            ->count('siswa_tahun_ajaran_id');
    }
}
