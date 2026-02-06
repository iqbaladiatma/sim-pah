<?php
require 'vendor/autoload.php';

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('public/FORMISOPROSEDUR.xlsx');
$sheetNames = $spreadsheet->getSheetNames();

foreach ($sheetNames as $sheetName) {
    if (str_contains($sheetName, 'BUKU INDUK') || str_contains($sheetName, 'LAPORAN ASET')) {
        echo "\n--- Sheet: {$sheetName} ---\n";
        $sheet = $spreadsheet->getSheetByName($sheetName);
        $data = $sheet->toArray();
        for ($i = 0; $i < 40; $i++) {
            if (isset($data[$i])) {
                $row = array_filter($data[$i]);
                if (!empty($row)) {
                    echo "Row " . ($i + 1) . ": " . implode(' | ', $row) . "\n";
                }
            }
        }
    }
}
