<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

require __DIR__.'/vendor/autoload.php';

ini_set('memory_limit', '1024M');

class ChunkReadFilter implements IReadFilter
{
    public function readCell($columnAddress, $row, $worksheetName = ''): bool
    {
        if ($worksheetName === 'Siswa') {
            // Read only columns A to H, and first 50 rows
            if ($row <= 50 && in_array($columnAddress, ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'])) {
                return true;
            }
        }
        return false;
    }
}

$reader = IOFactory::createReaderForFile('prd/SMART EXCEL SPP (Edit) 3.0 (1).xlsm');
$reader->setReadDataOnly(true);
$reader->setReadFilter(new ChunkReadFilter());
$spreadsheet = $reader->load('prd/SMART EXCEL SPP (Edit) 3.0 (1).xlsm');

$sheetName = 'Siswa';
echo "\n=== SHEET: $sheetName ===\n";
$worksheet = $spreadsheet->getSheetByName($sheetName);
if ($worksheet) {
    foreach ($worksheet->getRowIterator() as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $data = [];
        foreach ($cellIterator as $cell) {
            $data[] = (string)$cell->getValue();
        }
        if (empty(array_filter($data))) continue;
        echo implode(' | ', array_map(fn ($v) => substr($v, 0, 30), $data))."\n";
    }
} else {
    echo "Sheet not found!\n";
}
