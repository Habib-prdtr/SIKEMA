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

    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::aktif();

        $jumlahSiswa = $this->dashboardService->getJumlahSiswa($tahunAktif);
        $totalPenerimaanBulanIni = $this->dashboardService->getTotalPenerimaanBulanIni($tahunAktif);
        $totalPengeluaranBulanIni = $this->dashboardService->getTotalPengeluaranBulanIni($tahunAktif);

        $tunggakanData = $this->dashboardService->getTunggakanData($tahunAktif);
        $siswaAdaTunggakan = $tunggakanData['siswaAdaTunggakan'];
        $totalTunggakanAwal = $tunggakanData['totalTunggakanAwal'];

        $sppBelumLunas = $this->dashboardService->getSppBelumLunasBulanIni($tahunAktif);
        $sppTerlewatBelumLunas = $this->dashboardService->getSppTerlewatBelumLunas($tahunAktif);
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
        $tagihanIuran = collect();
        $sisaTunggakan = 0;

        if ($request->filled('no_induk') && $tahunAktif) {
            $transaksiService = app(\App\Services\TransaksiService::class);
            $tunggakanService = app(\App\Services\TunggakanService::class);

            $sta = $transaksiService->getSiswaUntukTransaksi($request->no_induk, $tahunAktif);

            if ($sta) {
                $siswa = $sta;
                $tagihanSpp = $sta->tagihanSpp;
                $tagihanIuran = $sta->tagihanIuran;
                $sisaTunggakan = $tunggakanService->hitungSisa($sta);
            }
        }

        return view('dashboard.index', compact(
            'tahunAktif',
            'jumlahSiswa',
            'totalPenerimaanBulanIni',
            'totalPengeluaranBulanIni',
            'siswaAdaTunggakan',
            'totalTunggakanAwal',
            'sppBelumLunas',
            'sppTerlewatBelumLunas',
            'totalSaldo',
            'penerimaanPerJenisData',
            'maxPenerimaanJenis',
            'transaksiTerbaru',
            'daftarSiswa',
            'siswa',
            'tagihanSpp',
            'tagihanIuran',
            'sisaTunggakan',
        ));
    }
}
