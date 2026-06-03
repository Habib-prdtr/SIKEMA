<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Services\LaporanService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanPenerimaanController extends Controller
{
    protected LaporanService $laporanService;

    public function __construct(LaporanService $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    /**
     * Laporan rekapitulasi penerimaan.
     *
     * Bisa difilter berdasarkan bulan, tahun ajaran, atau siswa.
     */
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $data = $this->laporanService->getLaporanPenerimaan($request, $tahunAktif);

        return view('laporan.penerimaan', $data);
    }
}
