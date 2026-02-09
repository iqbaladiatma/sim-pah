<?php
ini_set('memory_limit', '512M');
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$templatePath = 'public/FORMISOPROSEDUR.xlsx';
echo "Loading $templatePath...\n";
$spreadsheet = IOFactory::load($templatePath);
$sheetNames = $spreadsheet->getSheetNames();
echo "Found " . count($sheetNames) . " sheets:\n";
foreach ($sheetNames as $index => $name) {
    echo "[$index] $name\n";
}
