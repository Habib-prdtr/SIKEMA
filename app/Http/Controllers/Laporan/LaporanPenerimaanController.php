<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Services\LaporanService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function export(Request $request): StreamedResponse
    {
        $tahunAktif = TahunAjaran::aktif();
        $spreadsheet = $this->laporanService->exportPenerimaanToExcel($request, $tahunAktif);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_Penerimaan_' . date('Y-m-d_His') . '.xlsx';

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
