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

        // Fetch tunggakan terbayar map
        $tunggakanMap = \App\Models\TransaksiDetail::whereHas('transaksi', function ($q) use ($selectedTahunId) {
            $q->where('tahun_ajaran_id', $selectedTahunId);
        })
        ->where('jenis', 'tunggakan')
        ->selectRaw('transaksi.siswa_tahun_ajaran_id, SUM(transaksi_detail.nominal) as total')
        ->join('transaksi', 'transaksi_detail.transaksi_id', '=', 'transaksi.id')
        ->groupBy('transaksi.siswa_tahun_ajaran_id')
        ->pluck('total', 'siswa_tahun_ajaran_id');

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
            'tunggakanMap',
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
        $sekolah = \App\Models\Sekolah::getData();
        $tahunAktif = TahunAjaran::aktif();
        $selectedTahunId = $request->get('tahun_ajaran_id', $tahunAktif?->id);
        $tahunFilter = $selectedTahunId ? TahunAjaran::find($selectedTahunId) : $tahunAktif;

        $kelasFilter = $request->get('kelas');
        $cari = $request->get('cari');
        $bulanFilter = $request->get('bulan');
        $iuranFilterId = $request->get('jenis_penerimaan_id');
        $tab = $request->get('tab', 'gabungan');

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

        $students = $query->join('siswa', 'siswa_tahun_ajaran.siswa_id', '=', 'siswa.id')
            ->select('siswa_tahun_ajaran.*')
            ->orderBy('siswa.kelas')
            ->orderBy('siswa.nama')
            ->get();

        $jenisPenerimaanList = JenisPenerimaan::where('tahun_ajaran_id', $selectedTahunId)->orderBy('urutan')->get();

        $tunggakanMap = \App\Models\TransaksiDetail::whereHas('transaksi', function ($q) use ($selectedTahunId) {
            $q->where('tahun_ajaran_id', $selectedTahunId);
        })
        ->where('jenis', 'tunggakan')
        ->selectRaw('transaksi.siswa_tahun_ajaran_id, SUM(transaksi_detail.nominal) as total')
        ->join('transaksi', 'transaksi_detail.transaksi_id', '=', 'transaksi.id')
        ->groupBy('transaksi.siswa_tahun_ajaran_id')
        ->pluck('total', 'siswa_tahun_ajaran_id');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if ($tab === 'gabungan') {
            $sheet->setTitle('Rekap Gabungan');
            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

            $numIuran = count($jenisPenerimaanList);
            $totalCols = 8 + $numIuran; // 4 (Kelas,No,Nama,Tagihan) + 1 (SPP) + N (Iuran) + 1 (Tunggakan) + 1 (SudahBayar) + 1 (KurangBayar)
            $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);

            // Header Yayasan / Sekolah (Rows 1-4)
            $sheet->setCellValue('A1', $sekolah->nama_yayasan ?? 'YPI "IHYAUL ULUM"');
            $sheet->mergeCells("A1:{$lastColLetter}1");
            $sheet->setCellValue('A2', $sekolah->nama_sekolah ?? 'MTS IHYAUL ULUM');
            $sheet->mergeCells("A2:{$lastColLetter}2");
            $sheet->setCellValue('A3', $sekolah->alamat ?? 'Miru Banyuurip Kedamean Gresik');
            $sheet->mergeCells("A3:{$lastColLetter}3");
            $sheet->setCellValue('A4', 'TAHUN ' . ($tahunFilter?->nama ?? date('Y')));
            $sheet->mergeCells("A4:{$lastColLetter}4");

            $schoolHeaderStyle = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                    'size' => 11,
                ],
            ];
            $sheet->getStyle("A1:{$lastColLetter}4")->applyFromArray($schoolHeaderStyle);

            // Judul Laporan (Row 6)
            $sheet->setCellValue('A6', 'LAPORAN TAGIHAN DAN RINCIAN PEMBAYARAN UANG SEKOLAH');
            $sheet->mergeCells("A6:{$lastColLetter}6");
            $sheet->getStyle("A6:{$lastColLetter}6")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                    'size' => 13,
                ],
            ]);

            // Subtitle Tanggal (Row 7)
            $todayFormatted = \Carbon\Carbon::now()->locale('id')->isoFormat('dddd D MMMM YYYY');
            $sheet->setCellValue('A7', 'Sampai Dengan Hari ini : ' . $todayFormatted);
            $sheet->mergeCells("A7:{$lastColLetter}7");
            $sheet->getStyle("A7:{$lastColLetter}7")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['argb' => 'FF008000'],
                ],
            ]);

            // Header Tabel (Rows 9 & 10)
            $sheet->getRowDimension(9)->setRowHeight(26);
            $sheet->getRowDimension(10)->setRowHeight(20);

            $sheet->setCellValue('A9', 'Kelas');
            $sheet->mergeCells('A9:A10');

            $sheet->setCellValue('B9', "No\nInduk");
            $sheet->mergeCells('B9:B10');

            $sheet->setCellValue('C9', 'Nama');
            $sheet->mergeCells('C9:C10');

            $sheet->setCellValue('D9', "Tagihan\nSetahun");
            $sheet->mergeCells('D9:D10');

            // Rincian Yang Sudah Dibayar Group
            $tunggakanColIndex = 6 + $numIuran;
            $tunggakanColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($tunggakanColIndex);
            
            $sheet->setCellValue('E9', 'Rincian Yang Sudah Dibayar');
            $sheet->mergeCells("E9:{$tunggakanColLetter}9");

            // Row 10 Sub-headers
            $sheet->setCellValue('E10', 'SPP');

            foreach ($jenisPenerimaanList as $jpIndex => $jp) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(6 + $jpIndex);
                $sheet->setCellValue($colLetter . '10', $jp->nama);
            }

            $sheet->setCellValue($tunggakanColLetter . '10', 'Tunggakan');

            // Header Kolom Akhir
            $totalSudahBayarColIndex = 7 + $numIuran;
            $totalSudahBayarColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalSudahBayarColIndex);
            $sheet->setCellValue($totalSudahBayarColLetter . '9', "Total\nSudah\nBayar");
            $sheet->mergeCells("{$totalSudahBayarColLetter}9:{$totalSudahBayarColLetter}10");

            $totalKurangBayarColIndex = 8 + $numIuran;
            $totalKurangBayarColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalKurangBayarColIndex);
            $sheet->setCellValue($totalKurangBayarColLetter . '9', "Total\nKurang\nBayar");
            $sheet->mergeCells("{$totalKurangBayarColLetter}9:{$totalKurangBayarColLetter}10");

            $tableHeaderStyle = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'font' => [
                    'bold' => true,
                    'size' => 10,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            $sheet->getStyle("A9:{$lastColLetter}10")->applyFromArray($tableHeaderStyle);

            // Data Rows (Row 11 onwards)
            $startRow = 11;
            $currentRow = $startRow;

            foreach ($students as $sta) {
                $sheet->getRowDimension($currentRow)->setRowHeight(20);

                $sppTagihan = $sta->tagihanSpp->sum('tagihan');
                $sppTerbayar = $sta->tagihanSpp->sum('terbayar');
                $iurTagihan = $sta->tagihanIuran->sum('tagihan');
                $iurTerbayar = $sta->tagihanIuran->sum('terbayar');
                $tunggakanAwal = $sta->tunggakan_awal ?? 0;
                $tunggakanTerbayar = (int) ($tunggakanMap[$sta->id] ?? 0);

                $tagihanSetahun = $sppTagihan + $iurTagihan + $tunggakanAwal;
                $totalSudahBayar = $sppTerbayar + $iurTerbayar + $tunggakanTerbayar;
                $totalKurangBayar = max(0, $tagihanSetahun - $totalSudahBayar);

                $sheet->setCellValue('A' . $currentRow, $sta->siswa->kelas);
                $sheet->setCellValue('B' . $currentRow, $sta->siswa->no_induk);
                $sheet->setCellValue('C' . $currentRow, $sta->siswa->nama);
                $sheet->setCellValue('D' . $currentRow, $tagihanSetahun);
                $sheet->setCellValue('E' . $currentRow, $sppTerbayar);

                foreach ($jenisPenerimaanList as $jpIndex => $jp) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(6 + $jpIndex);
                    $bayarIur = $sta->tagihanIuran->where('jenis_penerimaan_id', $jp->id)->sum('terbayar');
                    $sheet->setCellValue($colLetter . $currentRow, $bayarIur);
                }

                $sheet->setCellValue($tunggakanColLetter . $currentRow, $tunggakanTerbayar);
                $sheet->setCellValue($totalSudahBayarColLetter . $currentRow, $totalSudahBayar);
                $sheet->setCellValue($totalKurangBayarColLetter . $currentRow, $totalKurangBayar);

                $currentRow++;
            }

            $lastDataRow = max($startRow, $currentRow - 1);

            // Footer / Grand Total Row
            $footerRow = $currentRow;
            $sheet->getRowDimension($footerRow)->setRowHeight(22);
            $sheet->setCellValue('A' . $footerRow, 'Grand Total');
            $sheet->mergeCells("A{$footerRow}:C{$footerRow}");

            $sheet->setCellValue('D' . $footerRow, "=SUM(D{$startRow}:D{$lastDataRow})");
            $sheet->setCellValue('E' . $footerRow, "=SUM(E{$startRow}:E{$lastDataRow})");

            foreach ($jenisPenerimaanList as $jpIndex => $jp) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(6 + $jpIndex);
                $sheet->setCellValue($colLetter . $footerRow, "=SUM({$colLetter}{$startRow}:{$colLetter}{$lastDataRow})");
            }

            $sheet->setCellValue($tunggakanColLetter . $footerRow, "=SUM({$tunggakanColLetter}{$startRow}:{$tunggakanColLetter}{$lastDataRow})");
            $sheet->setCellValue($totalSudahBayarColLetter . $footerRow, "=SUM({$totalSudahBayarColLetter}{$startRow}:{$totalSudahBayarColLetter}{$lastDataRow})");
            $sheet->setCellValue($totalKurangBayarColLetter . $footerRow, "=SUM({$totalKurangBayarColLetter}{$startRow}:{$totalKurangBayarColLetter}{$lastDataRow})");

            // Styling Data Rows & Footer
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];

            $sheet->getStyle("A{$startRow}:{$lastColLetter}{$footerRow}")->applyFromArray($borderStyle);

            $sheet->getStyle("A{$startRow}:B{$lastDataRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$startRow}:C{$lastDataRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("D{$startRow}:{$lastColLetter}{$footerRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $sheet->getStyle("A{$footerRow}:{$lastColLetter}{$footerRow}")->getFont()->setBold(true);

            // Format Angka Rupiah untuk seluruh kolom keuangan (D hingga Total Kurang Bayar)
            $sheet->getStyle("D{$startRow}:{$lastColLetter}{$footerRow}")
                ->getNumberFormat()
                ->setFormatCode('#,##0');

            // Auto Width Columns dengan Min Width untuk kerapian
            for ($col = 1; $col <= $totalCols; $col++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            }
            $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(10);
            $sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(12);
            $sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(26);
            $sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(18);
            $sheet->getColumnDimension($totalSudahBayarColLetter)->setAutoSize(false)->setWidth(18);
            $sheet->getColumnDimension($totalKurangBayarColLetter)->setAutoSize(false)->setWidth(18);
        } elseif ($tab === 'spp') {
            $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI PEMBAYARAN SPP');
            $sheet->setCellValue('A2', 'Tahun Ajaran: ' . ($tahunFilter?->nama ?? '-'));
            $sheet->mergeCells('A1:H1');
            $sheet->mergeCells('A2:H2');
            $sheet->getStyle('A1:A2')->getFont()->setBold(true);

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
        } else { // iuran
            $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI PEMBAYARAN IURAN');
            $sheet->setCellValue('A2', 'Tahun Ajaran: ' . ($tahunFilter?->nama ?? '-'));
            $sheet->mergeCells('A1:I1');
            $sheet->mergeCells('A2:I2');
            $sheet->getStyle('A1:A2')->getFont()->setBold(true);

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
