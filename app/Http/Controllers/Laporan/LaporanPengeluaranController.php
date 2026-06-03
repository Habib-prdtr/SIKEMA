<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Services\LaporanService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanPengeluaranController extends Controller
{
    protected LaporanService $laporanService;

    public function __construct(LaporanService $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    /**
     * Laporan rekapitulasi pengeluaran.
     *
     * Bisa difilter berdasarkan bulan, pos biaya, atau tahun ajaran.
     */
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $data = $this->laporanService->getLaporanPengeluaran($request, $tahunAktif);

        return view('laporan.pengeluaran', $data);
    }
}
