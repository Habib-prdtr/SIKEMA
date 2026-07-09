<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Dispensasi;
use App\Models\JenisPenerimaan;
use App\Models\SiswaTahunAjaran;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapitulasiController extends Controller
{
    /**
     * Tampilkan halaman rekapitulasi dengan tab SPP, Iuran, dan Gabungan.
     */
    public function index(Request $request): View
    {
        $tahunList = TahunAjaran::orderByDesc('nama')->get();
        $tahunAktif = TahunAjaran::aktif();
        $selectedTahunId = $request->get('tahun_ajaran_id', $tahunAktif?->id);
        $tahunFilter = $selectedTahunId ? TahunAjaran::find($selectedTahunId) : $tahunAktif;

        $kelasFilter = $request->get('kelas');
        $cari = $request->get('cari');
        $bulanFilter = $request->get('bulan');
        $iuranFilterId = $request->get('jenis_penerimaan_id');
        $tab = $request->get('tab', 'spp'); // spp, iuran, gabungan

        // Fetch distinct classes
        $daftarKelas = \App\Models\Siswa::distinct()->orderBy('kelas')->pluck('kelas')->filter();
        
        // Fetch all active iurans for dropdown
        $jenisPenerimaanList = JenisPenerimaan::where('tahun_ajaran_id', $selectedTahunId)->orderBy('urutan')->get();

        // Base query
        $query = SiswaTahunAjaran::with(['siswa', 'dispensasi', 'tagihanSpp', 'tagihanIuran.jenisPenerimaan'])
            ->where('tahun_ajaran_id', $selectedTahunId);

        if ($kelasFilter) {
            $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            if (in_array($kelasFilter, ['7', '8', '9'])) {
                $query->whereHas('siswa', fn($q) => $q->where('kelas', $like, "{$kelasFilter}%"));
            } else {
                $query->whereHas('siswa', fn($q) => $q->where('kelas', $kelasFilter));
            }
        }

        if ($cari) {
            $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->whereHas('siswa', function($q) use ($cari, $like) {
                $q->where('nama', $like, "%{$cari}%")
                  ->orWhere('no_induk', $like, "%{$cari}%");
            });
        }

        // Fetch paginated data (use simple get for printing / exporting to bypass pagination limits)
        $isPrint = $request->has('print');
        $students = $isPrint ? $query->get() : $query->paginate(25)->withQueryString();

        return view('laporan.rekapitulasi.index', compact(
            'students',
            'tahunList',
            'tahunAktif',
            'selectedTahunId',
            'tahunFilter',
            'daftarKelas',
            'jenisPenerimaanList',
            'kelasFilter',
            'cari',
            'bulanFilter',
            'iuranFilterId',
            'tab'
        ));
    }

    /**
     * Ekspor data rekapitulasi ke Excel.
     */
    public function export(Request $request): StreamedResponse
    {
        $tahunAktif = TahunAjaran::aktif();
        $selectedTahunId = $request->get('tahun_ajaran_id', $tahunAktif?->id);
        $tahunFilter = $selectedTahunId ? TahunAjaran::find($selectedTahunId) : $tahunAktif;

        $kelasFilter = $request->get('kelas');
        $cari = $request->get('cari');
        $bulanFilter = $request->get('bulan');
        $iuranFilterId = $request->get('jenis_penerimaan_id');
        $tab = $request->get('tab', 'spp');

        // Base query
        $query = SiswaTahunAjaran::with(['siswa', 'dispensasi', 'tagihanSpp', 'tagihanIuran.jenisPenerimaan'])
            ->where('tahun_ajaran_id', $selectedTahunId);

        if ($kelasFilter) {
            $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            if (in_array($kelasFilter, ['7', '8', '9'])) {
                $query->whereHas('siswa', fn($q) => $q->where('kelas', $like, "{$kelasFilter}%"));
            } else {
                $query->whereHas('siswa', fn($q) => $q->where('kelas', $kelasFilter));
            }
        }

        if ($cari) {
            $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->whereHas('siswa', function($q) use ($cari, $like) {
                $q->where('nama', $like, "%{$cari}%")
                  ->orWhere('no_induk', $like, "%{$cari}%");
            });
        }

        $students = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Style helper
        $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI PEMBAYARAN - ' . strtoupper($tab));
        $sheet->setCellValue('A2', 'Tahun Ajaran: ' . ($tahunFilter?->nama ?? '-'));
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);

        if ($tab === 'spp') {
            $sheet->setCellValue('A4', 'No');
            $sheet->setCellValue('B4', 'No. Induk');
            $sheet->setCellValue('C4', 'Nama Siswa');
            $sheet->setCellValue('D4', 'Kelas');
            $sheet->setCellValue('E4', 'Total Tagihan SPP');
            $sheet->setCellValue('F4', 'Total Terbayar');
            $sheet->setCellValue('G4', 'Sisa Tagihan');
            $sheet->setCellValue('H4', 'Status');

            $rowIdx = 5;
            foreach ($students as $index => $sta) {
                $tagihan = 0;
                $terbayar = 0;

                if ($bulanFilter) {
                    $bill = $sta->tagihanSpp->where('bulan', $bulanFilter)->first();
                    $tagihan = $bill ? $bill->tagihan : 0;
                    $terbayar = $bill ? $bill->terbayar : 0;
                    $status = $bill ? ucfirst($bill->status) : '-';
                } else {
                    $tagihan = $sta->tagihanSpp->sum('tagihan');
                    $terbayar = $sta->tagihanSpp->sum('terbayar');
                    $sisa = $tagihan - $terbayar;
                    $status = $sisa <= 0 ? 'Lunas' : ($terbayar > 0 ? 'Cicilan' : 'Belum Bayar');
                }

                $sheet->setCellValue('A' . $rowIdx, $index + 1);
                $sheet->setCellValue('B' . $rowIdx, $sta->siswa->no_induk);
                $sheet->setCellValue('C' . $rowIdx, $sta->siswa->nama);
                $sheet->setCellValue('D' . $rowIdx, $sta->siswa->kelas);
                $sheet->setCellValue('E' . $rowIdx, $tagihan);
                $sheet->setCellValue('F' . $rowIdx, $terbayar);
                $sheet->setCellValue('G' . $rowIdx, $tagihan - $terbayar);
                $sheet->setCellValue('H' . $rowIdx, $status);
                $rowIdx++;
            }
        } elseif ($tab === 'iuran') {
            $sheet->setCellValue('A4', 'No');
            $sheet->setCellValue('B4', 'No. Induk');
            $sheet->setCellValue('C4', 'Nama Siswa');
            $sheet->setCellValue('D4', 'Kelas');
            $sheet->setCellValue('E4', 'Nama Iuran');
            $sheet->setCellValue('F4', 'Tagihan');
            $sheet->setCellValue('G4', 'Terbayar');
            $sheet->setCellValue('H4', 'Sisa');
            $sheet->setCellValue('I4', 'Status');

            $rowIdx = 5;
            $counter = 1;
            foreach ($students as $sta) {
                $iurans = $sta->tagihanIuran;
                if ($iuranFilterId) {
                    $iurans = $iurans->where('jenis_penerimaan_id', $iuranFilterId);
                }

                foreach ($iurans as $iur) {
                    $sheet->setCellValue('A' . $rowIdx, $counter++);
                    $sheet->setCellValue('B' . $rowIdx, $sta->siswa->no_induk);
                    $sheet->setCellValue('C' . $rowIdx, $sta->siswa->nama);
                    $sheet->setCellValue('D' . $rowIdx, $sta->siswa->kelas);
                    $sheet->setCellValue('E' . $rowIdx, $iur->jenisPenerimaan->nama ?? '-');
                    $sheet->setCellValue('F' . $rowIdx, $iur->tagihan);
                    $sheet->setCellValue('G' . $rowIdx, $iur->terbayar);
                    $sheet->setCellValue('H' . $rowIdx, $iur->tagihan - $iur->terbayar);
                    $sheet->setCellValue('I' . $rowIdx, ucfirst($iur->status));
                    $rowIdx++;
                }
            }
        } else { // gabungan
            $sheet->setCellValue('A4', 'No');
            $sheet->setCellValue('B4', 'No. Induk');
            $sheet->setCellValue('C4', 'Nama Siswa');
            $sheet->setCellValue('D4', 'Kelas');
            $sheet->setCellValue('E4', 'Tagihan SPP');
            $sheet->setCellValue('F4', 'Terbayar SPP');
            $sheet->setCellValue('G4', 'Tagihan Iuran');
            $sheet->setCellValue('H4', 'Terbayar Iuran');
            $sheet->setCellValue('I4', 'Total Tagihan');
            $sheet->setCellValue('J4', 'Total Terbayar');
            $sheet->setCellValue('K4', 'Grand Sisa');

            $rowIdx = 5;
            foreach ($students as $index => $sta) {
                $sppTagihan = $sta->tagihanSpp->sum('tagihan');
                $sppTerbayar = $sta->tagihanSpp->sum('terbayar');
                $iurTagihan = $sta->tagihanIuran->sum('tagihan');
                $iurTerbayar = $sta->tagihanIuran->sum('terbayar');

                $totalTagihan = $sppTagihan + $iurTagihan;
                $totalTerbayar = $sppTerbayar + $iurTerbayar;

                $sheet->setCellValue('A' . $rowIdx, $index + 1);
                $sheet->setCellValue('B' . $rowIdx, $sta->siswa->no_induk);
                $sheet->setCellValue('C' . $rowIdx, $sta->siswa->nama);
                $sheet->setCellValue('D' . $rowIdx, $sta->siswa->kelas);
                $sheet->setCellValue('E' . $rowIdx, $sppTagihan);
                $sheet->setCellValue('F' . $rowIdx, $sppTerbayar);
                $sheet->setCellValue('G' . $rowIdx, $iurTagihan);
                $sheet->setCellValue('H' . $rowIdx, $iurTerbayar);
                $sheet->setCellValue('I' . $rowIdx, $totalTagihan);
                $sheet->setCellValue('J' . $rowIdx, $totalTerbayar);
                $sheet->setCellValue('K' . $rowIdx, $totalTagihan - $totalTerbayar);
                $rowIdx++;
            }
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_Rekapitulasi_' . $tab . '_' . date('Y-m-d_His') . '.xlsx';

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
