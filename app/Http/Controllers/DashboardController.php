<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        $tahunAktif = TahunAjaran::aktif();

        $jumlahSiswa = $this->dashboardService->getJumlahSiswa($tahunAktif);
        $totalPenerimaanBulanIni = $this->dashboardService->getTotalPenerimaanBulanIni($tahunAktif);
        $totalPengeluaranBulanIni = $this->dashboardService->getTotalPengeluaranBulanIni($tahunAktif);

        $sppBelumLunas = $this->dashboardService->getSppBelumLunasBulanIni($tahunAktif);
        $tabunganWajibBelumLunas = $this->dashboardService->getTabunganWajibBelumLunasBulanIni($tahunAktif);
        $totalSaldo = $this->dashboardService->getTotalSaldo($tahunAktif);

        $grafikPenerimaan = $this->dashboardService->getGrafikPenerimaanPerJenis($tahunAktif);
        $penerimaanPerJenisData = $grafikPenerimaan['data'];
        $maxPenerimaanJenis = $grafikPenerimaan['maxVal'];
        $transaksiTerbaru = $this->dashboardService->getTransaksiTerbaru($tahunAktif);

        // Tambah daftar siswa untuk pencatatan cepat
        $daftarSiswa = null;
        if ($tahunAktif) {
            $siswaQuery = \App\Models\SiswaTahunAjaran::with(['siswa'])
                ->join('siswa', 'siswa_tahun_ajaran.siswa_id', '=', 'siswa.id')
                ->select('siswa_tahun_ajaran.*')
                ->where('siswa_tahun_ajaran.tahun_ajaran_id', $tahunAktif->id)
                ->orderBy('siswa.nama');

            $jenjang = \App\Models\Sekolah::getJenjangAktif();
            if ($jenjang !== 'semua' && in_array($jenjang, ['7', '8', '9'])) {
                $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
                $siswaQuery->where('siswa.kelas', $like, "{$jenjang}%");
            }

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

        $siswa = null;
        $tagihanSpp = collect();
        $tagihanTabunganWajib = collect();
        $tagihanIuran = collect();
        $sisaTunggakan = 0;
        $tarifTabunganWajib = 0;

        if ($request->filled('no_induk') && $tahunAktif) {
            $transaksiService = app(\App\Services\TransaksiService::class);
            $tunggakanService = app(\App\Services\TunggakanService::class);
            $masterDataService = app(\App\Services\MasterDataService::class);

            $sta = $transaksiService->getSiswaUntukTransaksi($request->no_induk, $tahunAktif);

            if ($sta) {
                $siswa = $sta;
                $tagihanSpp = $sta->tagihanSpp;
                $tagihanTabunganWajib = $sta->tagihanTabunganWajib;
                $tagihanIuran = $sta->tagihanIuran;
                $sisaTunggakan = $tunggakanService->hitungSisa($sta);
                $tarifTabunganWajib = $masterDataService->getTarifTabunganWajibSiswa($sta);
            }
        }

        if ($request->has('ajax') || ($request->has('no_induk') && ($request->wantsJson() || $request.ajax()))) {
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
                    'tarif_tabungan_wajib' => $tarifTabunganWajib,
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
                'tagihanTabunganWajib' => $tagihanTabunganWajib->map(function ($tw) {
                    $lunas = $tw->status === 'lunas';
                    $nama = \Carbon\Carbon::createFromDate($tw->tahun, $tw->bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
                    $nominal = $lunas ? $tw->tagihan : $tw->sisa();
                    return [
                        'id' => $tw->id,
                        'bulan' => $tw->bulan,
                        'tahun' => $tw->tahun,
                        'nama' => $nama,
                        'status' => $tw->status,
                        'tagihan' => $tw->tagihan,
                        'nominal' => $nominal,
                        'lunas' => $lunas,
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

        return view('dashboard.index', compact(
            'tahunAktif',
            'jumlahSiswa',
            'totalPenerimaanBulanIni',
            'totalPengeluaranBulanIni',
            'sppBelumLunas',
            'tabunganWajibBelumLunas',
            'totalSaldo',
            'penerimaanPerJenisData',
            'maxPenerimaanJenis',
            'transaksiTerbaru',
            'daftarSiswa',
            'siswa',
            'tagihanSpp',
            'tagihanTabunganWajib',
            'tagihanIuran',
            'tarifTabunganWajib',
            'sisaTunggakan',
        ));
    }
}
