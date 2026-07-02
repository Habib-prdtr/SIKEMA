<?php

namespace App\Utils;

use App\Models\Sekolah;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExcelExportUtil
{
    /**
     * Create a new spreadsheet with multiple sheets, fully formatted and ready for printing.
     *
     * @param array $sheetsData Array of configurations for each sheet.
     *                          Each sheet configuration should contain:
     *                          - 'title'    (string)   : Title for the sheet and header.
     *                          - 'periode'  (string)   : Period description (e.g., "2025/2026").
     *                          - 'columns'  (array)    : Column header labels.
     *                          - 'data'     (iterable) : Data rows to populate.
     *                          - 'mapper'   (callable) : Callback to map data to cells: 
     *                                                    function($sheet, $item, $rowNumber, $index).
     *                          - 'totalRow' (array|null): Optional total row config:
     *                                                     ['col' => 'C', 'label' => 'Total', 'value' => 1000].
     *
     * @return Spreadsheet
     */
    public static function createMultiSheetReport(array $sheetsData): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sekolah = Sekolah::getData();

        // Style definition
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        foreach ($sheetsData as $index => $sheetConfig) {
            // ... (sheet creation logic remains same)
            if ($index === 0) {
                $sheet = $spreadsheet->getActiveSheet();
            } else {
                $sheet = $spreadsheet->createSheet();
            }

            $sheet->setTitle($sheetConfig['title']);

            // Print Setup: A4, Fit to 1 page wide
            $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            $columns = $sheetConfig['columns'];
            $columnCount = count($columns);
            $lastColumnLetter = Coordinate::stringFromColumnIndex($columnCount);

            // Headers (rows 1-5)
            $sheet->setCellValue('A1', $sekolah->nama_yayasan ?? '');
            $sheet->mergeCells("A1:{$lastColumnLetter}1");
            $sheet->setCellValue('A2', $sekolah->nama_sekolah ?? '');
            $sheet->mergeCells("A2:{$lastColumnLetter}2");
            $sheet->setCellValue('A3', strtoupper($sheetConfig['title']));
            $sheet->mergeCells("A3:{$lastColumnLetter}3");
            $sheet->setCellValue('A4', 'Periode : ' . ($sheetConfig['periode'] ?? '-'));
            $sheet->mergeCells("A4:{$lastColumnLetter}4");
            $sheet->setCellValue('A5', 'Tgl Cetak : ' . date('d/m/Y'));
            $sheet->mergeCells("A5:{$lastColumnLetter}5");

            // Styling headers
            $headerStyle = [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'font' => ['bold' => true],
            ];
            $sheet->getStyle("A1:{$lastColumnLetter}5")->applyFromArray($headerStyle);

            // Table Headers (row 7)
            foreach ($columns as $cIndex => $columnName) {
                $colLetter = Coordinate::stringFromColumnIndex($cIndex + 1);
                $sheet->setCellValue($colLetter . '7', $columnName);
                $sheet->getStyle($colLetter . '7')->applyFromArray($headerStyle);
                $sheet->getStyle($colLetter . '7')->applyFromArray($borderStyle); // Add border
            }

            // Data (start from 8)
            $row = 8;
            foreach ($sheetConfig['data'] as $dIndex => $item) {
                $sheetConfig['mapper']($sheet, $item, $row, $dIndex + 1);

                // Add borders to data row
                $sheet->getStyle("A{$row}:{$lastColumnLetter}{$row}")->applyFromArray($borderStyle);
                $row++;
            }

            // Total Row
            if (isset($sheetConfig['totalRow'])) {
                $totalRow = $sheetConfig['totalRow'];
                $sheet->setCellValue('A' . $row, $totalRow['label']);
                $sheet->mergeCells("A{$row}:" . Coordinate::stringFromColumnIndex($columnCount - 1) . $row);
                $sheet->setCellValue($totalRow['col'] . $row, $totalRow['value']);
                $sheet->getStyle("A{$row}:" . $totalRow['col'] . $row)->applyFromArray($headerStyle);
                $sheet->getStyle("A{$row}:{$lastColumnLetter}{$row}")->applyFromArray($borderStyle); // Add border
            }

            // Auto width
            foreach (range(1, $columnCount) as $i) {
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        return $spreadsheet;
    }
}
