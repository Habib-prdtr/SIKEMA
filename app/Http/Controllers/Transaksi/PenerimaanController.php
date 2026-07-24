<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\SimpanPenerimaanRequest;
use App\Models\Sekolah;
use App\Models\TahunAjaran;
use App\Models\Transaksi;
use App\Services\TransaksiService;
use App\Services\TunggakanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenerimaanController extends Controller
{
    public function __construct(
        private readonly TransaksiService $transaksiService,
        private readonly TunggakanService $tunggakanService,
    ) {}

    /**
     * Daftar transaksi penerimaan (riwayat).
     */
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $transaksi = $this->transaksiService->getDaftarPenerimaan($request, $tahunAktif);

        // Load statistics summaries from LaporanService
        $laporanData = app(\App\Services\LaporanService::class)->getLaporanPenerimaan($request, $tahunAktif);
        $totalPenerimaan = $laporanData['totalPenerimaan'];
        $totalSpp = $laporanData['totalSpp'];
        $totalIuran = $laporanData['totalIuran'];
        $totalTunggakan = $laporanData['totalTunggakan'];

        return view('transaksi.penerimaan.index', compact(
            'transaksi',
            'tahunAktif',
            'totalPenerimaan',
            'totalSpp',
            'totalIuran',
            'totalTunggakan'
        ));
    }

    /**
     * Form catat penerimaan: cari siswa, tampilkan tagihan aktif.
     */
    public function create(Request $request): View|\Illuminate\Http\JsonResponse
    {
        $tahunAktif = TahunAjaran::aktif();
        $siswa = null;
        $tagihanSpp = collect();
        $tagihanIuran = collect();
        $sisaTunggakan = 0;

        if ($request->filled('no_induk')) {
            $sta = $this->transaksiService->getSiswaUntukTransaksi($request->no_induk, $tahunAktif);

            if ($sta) {
                $siswa = $sta;
                $tagihanSpp = $sta->tagihanSpp;
                $tagihanIuran = $sta->tagihanIuran;
                $sisaTunggakan = $this->tunggakanService->hitungSisa($sta);
            }
        }

        if ($request->wantsJson() || $request->ajax() || $request->has('ajax')) {
            if (!$siswa) {
                return response()->json(['error' => 'Siswa dengan No. Induk tersebut tidak ditemukan atau belum aktif.'], 404);
            }

            $siswa->loadMissing('dispensasi');

            return response()->json([
                'siswa' => [
                    'id' => $siswa->id,
                    'nama' => $siswa->siswa->nama,
                    'no_induk' => $siswa->siswa->no_induk,
                    'kelas' => $siswa->siswa->kelas,
                    'tahun_ajaran' => $siswa->tahunAjaran->nama,
                    'tarif_spp' => $siswa->tarif_spp,
                    'tunggakan_awal' => $siswa->tunggakan_awal,
                    'dispensasi_nama' => $siswa->dispensasi->nama ?? null,
                ],
                'sisaTunggakan' => $sisaTunggakan,
                'tagihanSpp' => $tagihanSpp->map(function ($spp) use ($siswa) {
                    $lunas = $spp->status === 'lunas';
                    $nama = \Carbon\Carbon::createFromDate($spp->tahun, $spp->bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
                    $nominal = $lunas ? $spp->tagihan : $spp->sisa();
                    $hasDispensasi = $spp->tagihan < $siswa->tarif_spp;
                    $potongan = $hasDispensasi ? ($siswa->tarif_spp - $spp->tagihan) : 0;
                    return [
                        'id' => $spp->id,
                        'bulan' => $spp->bulan,
                        'tahun' => $spp->tahun,
                        'nama' => $nama,
                        'status' => $spp->status,
                        'tagihan' => $spp->tagihan,
                        'nominal' => $nominal,
                        'lunas' => $lunas,
                        'hasDispensasi' => $hasDispensasi,
                        'potongan' => $potongan,
                        'namaDispensasi' => $siswa->dispensasi->nama ?? null,
                    ];
                }),
                'tagihanIuran' => $tagihanIuran->map(function ($iuran) {
                    $lunas = $iuran->status === 'lunas';
                    $nominal = $lunas ? $iuran->tagihan : $iuran->sisa();
                    return [
                        'id' => $iuran->id,
                        'nama' => $iuran->jenisPenerimaan->nama ?? 'Iuran',
                        'status' => $iuran->status,
                        'tagihan' => $iuran->tagihan,
                        'nominal' => $nominal,
                        'lunas' => $lunas,
                    ];
                }),
            ]);
        }

        $daftarSiswa = null;
        if ($tahunAktif) {
            $siswaQuery = \App\Models\SiswaTahunAjaran::with(['siswa'])
                ->join('siswa', 'siswa_tahun_ajaran.siswa_id', '=', 'siswa.id')
                ->select('siswa_tahun_ajaran.*')
                ->where('siswa_tahun_ajaran.tahun_ajaran_id', $tahunAktif->id)
                ->orderBy('siswa.nama');

            if ($request->filled('cari')) {
                $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
                $cari = $request->cari;
                $siswaQuery->where(function ($q) use ($like,$cari) {
                    $q->where('siswa.nama', $like, "%{$cari}%")
                      ->orWhere('siswa.no_induk', $like, "%{$cari}%")
                      ->orWhere('siswa.kelas', $like, "%{$cari}%");
                });
            }

            $daftarSiswa = $siswaQuery->paginate(5)->withQueryString();
        }

        return view('transaksi.penerimaan.create', compact(
            'tahunAktif',
            'siswa',
            'tagihanSpp',
            'tagihanIuran',
            'sisaTunggakan',
            'daftarSiswa',
        ));
    }

    /**
     * Simpan transaksi penerimaan (bulk payment).
     */
    public function store(SimpanPenerimaanRequest $request): RedirectResponse
    {
        $transaksi = $this->transaksiService->simpanPenerimaan(
            $request->validated(),
            $request->user(),
        );

        return redirect()->route('penerimaan.show', $transaksi)
            ->with('sukses', "Transaksi {$transaksi->no_transaksi} berhasil disimpan.");
    }

    /**
     * Detail transaksi + opsi cetak kwitansi.
     */
    public function show(Transaksi $transaksi): View
    {
        $transaksi->load([
            'siswaTahunAjaran.siswa',
            'siswaTahunAjaran.tahunAjaran',
            'siswaTahunAjaran.dispensasi',
            'details.jenisPenerimaan',
            'user',
        ]);

        $sekolah = Sekolah::getData();

        return view('transaksi.penerimaan.show', compact('transaksi', 'sekolah'));
    }
}
