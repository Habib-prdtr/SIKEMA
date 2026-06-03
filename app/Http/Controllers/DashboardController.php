<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(): View
    {
        $tahunAktif = TahunAjaran::aktif();

        $jumlahSiswa = $this->dashboardService->getJumlahSiswa($tahunAktif);
        $totalPenerimaanBulanIni = $this->dashboardService->getTotalPenerimaanBulanIni($tahunAktif);
        $totalPengeluaranBulanIni = $this->dashboardService->getTotalPengeluaranBulanIni($tahunAktif);
        
        $tunggakanData = $this->dashboardService->getTunggakanData($tahunAktif);
        $siswaAdaTunggakan = $tunggakanData['siswaAdaTunggakan'];
        $totalTunggakanAwal = $tunggakanData['totalTunggakanAwal'];
        
        $sppBelumLunas = $this->dashboardService->getSppBelumLunasBulanIni($tahunAktif);
        $totalSaldo = $this->dashboardService->getTotalSaldo($tahunAktif);
        
        $grafikData = $this->dashboardService->getGrafikBulanan($tahunAktif);
        $bulanLabels = $grafikData['bulanLabels'];
        $dataPenerimaan = $grafikData['dataPenerimaan'];
        $dataPengeluaran = $grafikData['dataPengeluaran'];
        
        $transaksiTerbaru = $this->dashboardService->getTransaksiTerbaru($tahunAktif);

        return view('dashboard.index', compact(
            'tahunAktif',
            'jumlahSiswa',
            'totalPenerimaanBulanIni',
            'totalPengeluaranBulanIni',
            'siswaAdaTunggakan',
            'totalTunggakanAwal',
            'sppBelumLunas',
            'totalSaldo',
            'bulanLabels',
            'dataPenerimaan',
            'dataPengeluaran',
            'transaksiTerbaru',
        ));
    }
}
