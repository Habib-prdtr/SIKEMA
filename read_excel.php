<?php
require __DIR__ . '/vendor/autoload.php';
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile('prd/SMART EXCEL SPP (Edit) 3.0 (1).xlsm');
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load('prd/SMART EXCEL SPP (Edit) 3.0 (1).xlsm');

$sheets = $spreadsheet->getSheetNames();
echo "SHEETS: \n";
print_r($sheets);

foreach ($sheets as $idx => $sheetName) {
    if ($idx > 3) break; // just check first few sheets
    echo "\n=== SHEET: $sheetName ===\n";
    $worksheet = $spreadsheet->getSheetByName($sheetName);
    $rows = $worksheet->toArray();
    for ($i=0; $i<min(10, count($rows)); $i++) {
        echo implode(" | ", array_map(fn($v) => substr((string)$v, 0, 30), $rows[$i])) . "\n";
    }
}
