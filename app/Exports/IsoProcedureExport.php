<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\MaintenanceLog;
use App\Models\Item;
use App\Models\BorrowingRecord;
use App\Models\AssetLifecycleLog;

class IsoProcedureExport implements WithEvents
{
    use RegistersEventListeners;

    protected $type;
    protected $procedure;

    public function __construct($type = null, $procedure = null)
    {
        $this->type = $type;
        $this->procedure = $procedure;
    }

    public function registerEvents(): array
    {
        return [];
    }

    // A more direct approach using PhpSpreadsheet for maximum control over the template
    public static function download($type, $procedureData)
    {
        $templatePath = public_path('FORMISOPROSEDUR.xlsx');
        $spreadsheet = IOFactory::load($templatePath);

        $sheetName = $procedureData['sheet'] ?? null;
        if ($sheetName && $spreadsheet->sheetNameExists($sheetName)) {
            $sheet = $spreadsheet->getSheetByName($sheetName);

            // Fill data based on model type
            $model = $procedureData['model'];
            $query = $model::query();

            if (isset($procedureData['type'])) {
                $query->where('type', $procedureData['type']);
            }
            if (isset($procedureData['category'])) {
                $query->where('category', $procedureData['category']);
            }

            $data = $query->latest()->get();

            // LOGIC TO MAP DATA TO TEMPLATE
            // This varies by sheet. For now, let's start with a generic row filling starting at a common row (e.g., row 10)
            $startRow = 10;

            foreach ($data as $index => $item) {
                $currentRow = $startRow + $index;
                $sheet->setCellValue('A' . $currentRow, $index + 1);

                if ($model === MaintenanceLog::class) {
                    if (str_contains($sheetName, 'PEMELIHARAAN GEDUNG')) {
                        // ... (keep existing gedun logic)
                        $sheet->setCellValue('B' . $currentRow, $item->title);
                        $sheet->setCellValue('C' . $currentRow, $item->check_standard);
                        $sheet->setCellValue('D' . $currentRow, $item->check_method);
                        $sheet->setCellValue('E' . $currentRow, $item->check_frequency);

                        $months = ['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun'];
                        $cols = ['F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'];
                        foreach ($months as $i => $m) {
                            $sheet->setCellValue($cols[$i] . $currentRow, $item->$m);
                        }

                        if ($index === 0) {
                            $sheet->setCellValue('H3', $item->subcategory); // Gedung
                            $sheet->setCellValue('H4', $item->location);    // Lokasi
                            $sheet->setCellValue('H5', $item->year);        // Tahun
                        }
                    } elseif (str_contains($sheetName, 'PEMELIHARAAN KAMAR MANDI')) {
                        if ($index === 0) {
                            $sheet->setCellValue('B3', $item->subcategory); // AREA
                            $sheet->setCellValue('G3', $item->completed_at); // Tgl Kegiatan
                        }
                        // B: Tgl, C: Lokasi, D-L: Components, M: Petugas, N: Tindakan, O: Ket
                        $sheet->setCellValue('B' . $currentRow, $item->completed_at);
                        $sheet->setCellValue('C' . $currentRow, $item->location);
                        $sheet->setCellValue('D' . $currentRow, $item->kran_air);
                        $sheet->setCellValue('E' . $currentRow, $item->lampu);
                        $sheet->setCellValue('F' . $currentRow, $item->fiting_lampu);
                        $sheet->setCellValue('G' . $currentRow, $item->saklar_lampu);
                        $sheet->setCellValue('H' . $currentRow, $item->ember);
                        $sheet->setCellValue('I' . $currentRow, $item->gayung);
                        $sheet->setCellValue('J' . $currentRow, $item->closet);
                        $sheet->setCellValue('K' . $currentRow, $item->pintu);
                        $sheet->setCellValue('L' . $currentRow, $item->grendel_pintu);
                        $sheet->setCellValue('M' . $currentRow, $item->performer?->name ?? 'Staff URT');
                        $sheet->setCellValue('N' . $currentRow, $item->action_taken);
                        $sheet->setCellValue('O' . $currentRow, $item->description);
                    } elseif (str_contains($sheetName, 'PEMELIHARAAN POMPA') || str_contains($sheetName, 'PEMELIHARAAN AIR BERSI') || str_contains($sheetName, 'PEMELIHARAAN AIR MINUM') || str_contains($sheetName, 'PEMELIHARAAN GENSET')) {
                        $sheet->setCellValue('B' . $currentRow, $item->title);
                        $sheet->setCellValue('C' . $currentRow, $item->check_standard);
                        $sheet->setCellValue('D' . $currentRow, $item->check_method);
                        $sheet->setCellValue('E' . $currentRow, $item->check_frequency);

                        $months = ['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun'];

                        // Decision on areas based on type
                        if (str_contains($sheetName, 'PEMELIHARAAN AIR MINUM') || str_contains($sheetName, 'PEMELIHARAAN GENSET')) {
                            $areas = ['putra', 'putri']; // Mapping to Pkn1/Pkn2 for Genset
                        } elseif (str_contains($sheetName, 'PEMELIHARAAN POMPA')) {
                            $areas = ['putra', 'putri', 'lawata'];
                        } else {
                            $areas = ['lawata', 'putra', 'putri']; // For Air Bersih
                        }

                        $colIdx = 5; // Starting Cell F is index 5 (0-based)

                        foreach ($months as $m) {
                            foreach ($areas as $a) {
                                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                                $sheet->setCellValue($colLetter . $currentRow, $item->{$m . '_' . $a});
                                $colIdx++;
                            }
                        }

                        if ($index === 0) {
                            if (str_contains($sheetName, 'PEMELIHARAAN GENSET')) {
                                $sheet->setCellValue('B4', $item->subcategory); // Kegiatan
                                $sheet->setCellValue('B5', $item->serial_number); // No Mesin
                                $sheet->setCellValue('B6', $item->location);
                                $sheet->setCellValue('H7', $item->year);
                            } else {
                                $sheet->setCellValue('B7', $item->location);
                                $sheet->setCellValue('H7', $item->year);
                            }
                        }
                    } elseif (str_contains($sheetName, 'PEMELIHARAAN AC')) {
                        // B: Ruangan, C-H: AC Checks, I: Petugas
                        $sheet->setCellValue('B' . $currentRow, $item->room?->name ?? $item->location);
                        $sheet->setCellValue('C' . $currentRow, $item->ac_indoor_pc);
                        $sheet->setCellValue('D' . $currentRow, $item->ac_indoor_sw);
                        $sheet->setCellValue('E' . $currentRow, $item->ac_outdoor_freon);
                        $sheet->setCellValue('F' . $currentRow, $item->ac_outdoor_amp);
                        $sheet->setCellValue('G' . $currentRow, $item->ac_kelistrikan_jaringan);
                        $sheet->setCellValue('H' . $currentRow, $item->ac_kelistrikan_tegangan);
                        $sheet->setCellValue('I' . $currentRow, $item->performer?->name ?? 'Staff URT');

                        if ($index === 0) {
                            $sheet->setCellValue('G4', $item->ac_divisi);
                            $sheet->setCellValue('G5', $item->ac_tgl_bln);
                        }
                    } elseif (str_contains($sheetName, 'PEMELIHARAAN KIPAS ANG')) {
                        $sheet->setCellValue('B' . $currentRow, $item->completed_at);
                        $sheet->setCellValue('C' . $currentRow, $item->room?->name ?? $item->location);
                        $sheet->setCellValue('D' . $currentRow, $item->fan_type);
                        $sheet->setCellValue('E' . $currentRow, $item->subcategory);
                        $sheet->setCellValue('F' . $currentRow, $item->before_condition === 'B' ? 'BAGUS' : 'RUSAK');
                        $sheet->setCellValue('G' . $currentRow, $item->action_taken);
                        $sheet->setCellValue('H' . $currentRow, $item->performer?->name ?? 'Staff URT');
                    } elseif (str_contains($sheetName, 'PEMELIHARAAN SEPTIK TA')) {
                        $sheet->setCellValue('B' . $currentRow, $item->completed_at);
                        $sheet->setCellValue('C' . $currentRow, $item->room?->name ?? $item->location);
                        $sheet->setCellValue('D' . $currentRow, $item->st_baik);
                        $sheet->setCellValue('E' . $currentRow, $item->st_penuh);
                        $sheet->setCellValue('F' . $currentRow, $item->st_bocor);
                        $sheet->setCellValue('G' . $currentRow, $item->st_bau);
                        $sheet->setCellValue('H' . $currentRow, $item->action_taken);
                        $sheet->setCellValue('I' . $currentRow, $item->after_condition);
                        $sheet->setCellValue('J' . $currentRow, $item->performer?->name ?? 'Staff URT');
                    } elseif (str_contains($sheetName, 'LAPORAN PEMELIHARAAN SARPRAS')) {
                        $sheet->setCellValue('B' . $currentRow, $item->title);
                        $sheet->setCellValue('C' . $currentRow, $item->check_standard); // Jenis Pekerjaan
                        $sheet->setCellValue('D' . $currentRow, $item->room?->name ?? $item->location);
                        $sheet->setCellValue('E' . $currentRow, $item->before_condition);
                        $sheet->setCellValue('F' . $currentRow, $item->action_taken);
                        $sheet->setCellValue('G' . $currentRow, $item->after_condition);
                        $sheet->setCellValue('H' . $currentRow, $item->performer?->name ?? 'Staff URT');
                        $sheet->setCellValue('I' . $currentRow, $item->description);

                        if ($index === 0) {
                            $sheet->setCellValue('B3', $item->subcategory); // Pemeliharaan : ...
                        }
                    } elseif (str_contains($sheetName, 'JADWAL AGENDA PERBAIKAN SARPRAS')) {
                        $sheet->setCellValue('B' . $currentRow, $item->title);
                        $sheet->setCellValue('C' . $currentRow, strtoupper($item->status ?? 'PENDING'));
                        $sheet->setCellValue('D' . $currentRow, $item->performer?->name ?? 'Staff URT');
                        $sheet->setCellValue('E' . $currentRow, $item->description);

                        if ($index === 0) {
                            $sheet->setCellValue('B3', $item->room?->name ?? $item->location);
                            $sheet->setCellValue('B4', $item->scheduled_at);
                        }
                    } else {
                        // Mapping for Maintenance Forms (Sheet 6-15, 31-37, 39)
                        $sheet->setCellValue('B' . $currentRow, $item->title);
                        $sheet->setCellValue('C' . $currentRow, $item->subcategory ?? $item->type);
                        $sheet->setCellValue('D' . $currentRow, $item->location);
                        $sheet->setCellValue('E' . $currentRow, $item->before_condition);
                        $sheet->setCellValue('F' . $currentRow, $item->action_taken);
                        $sheet->setCellValue('G' . $currentRow, $item->after_condition);
                        $sheet->setCellValue('H' . $currentRow, $item->performer?->name ?? 'Staff URT');
                        $sheet->setCellValue('I' . $currentRow, $item->description);
                    }
                } elseif ($model === Item::class) {
                    if (str_contains($sheetName, 'BUKU INDUK')) {
                        // NO | SATKER | RUANGAN | JENIS | MERK | MERK | TGL | NO URUT | KODE | NO PONDOK | QTY | SATUAN | SUMBER | TGL SERAH | KONDISI | HARGA | PENYUSUTAN | PJ | KET
                        $sheet->setCellValue('B' . $currentRow, $item->institution?->name);
                        $sheet->setCellValue('C' . $currentRow, $item->room?->name);
                        $sheet->setCellValue('D' . $currentRow, $item->name);
                        $sheet->setCellValue('E' . $currentRow, $item->brand);
                        $sheet->setCellValue('F' . $currentRow, $item->specification);
                        $sheet->setCellValue('G' . $currentRow, $item->purchased_at);
                        $sheet->setCellValue('H' . $currentRow, $item->no_urut_satker);
                        $sheet->setCellValue('I' . $currentRow, $item->code);
                        $sheet->setCellValue('J' . $currentRow, $item->no_urut_pondok);
                        $sheet->setCellValue('K' . $currentRow, $item->stock);
                        $sheet->setCellValue('L' . $currentRow, $item->unit);
                        $sheet->setCellValue('M' . $currentRow, $item->source);
                        $sheet->setCellValue('N' . $currentRow, $item->received_at);
                        $sheet->setCellValue('O' . $currentRow, $item->condition);
                        $sheet->setCellValue('P' . $currentRow, $item->price);
                        $sheet->setCellValue('Q' . $currentRow, $item->depreciation_price);
                        $sheet->setCellValue('R' . $currentRow, $item->responsible_person);
                        $sheet->setCellValue('S' . $currentRow, $item->note);
                    } elseif (str_contains($sheetName, 'KARTU INVENTARIS RUANGAN')) {
                        // A: No, B: Nama, C: Merk/Kode, D: No. Seri, E: Ukuran, F: Bahan, G: Tahun, H: No Kode, I: Jumlah, J: Baik, K: KB, L: RB, M: Ket
                        $sheet->setCellValue('B' . $currentRow, $item->name);
                        $sheet->setCellValue('C' . $currentRow, $item->brand);
                        $sheet->setCellValue('D' . $currentRow, $item->serial_number);
                        $sheet->setCellValue('E' . $currentRow, $item->size);
                        $sheet->setCellValue('F' . $currentRow, $item->material);
                        $sheet->setCellValue('G' . $currentRow, $item->purchased_at);
                        $sheet->setCellValue('H' . $currentRow, $item->code);
                        $sheet->setCellValue('I' . $currentRow, $item->stock);

                        // Condition mapping (J, K, L)
                        if ($item->condition === 'B')
                            $sheet->setCellValue('J' . $currentRow, 'V');
                        if ($item->condition === 'KB')
                            $sheet->setCellValue('K' . $currentRow, 'V');
                        if ($item->condition === 'RB')
                            $sheet->setCellValue('L' . $currentRow, 'V');

                        $sheet->setCellValue('M' . $currentRow, $item->note);

                        if ($index === 0) {
                            $sheet->setCellValue('D3', 'NUSA TENGGARA BARAT');
                            $sheet->setCellValue('D4', 'KOTA MATARAM');
                            $sheet->setCellValue('D5', 'PAH-MTR-' . ($item->institution?->code ?? 'GEN'));
                            $sheet->setCellValue('D6', $item->institution?->name);
                            $sheet->setCellValue('D7', $item->room?->name);
                        }
                    } else {
                        // FORMULIR LAPORAN ASET PAH MATARAM
                        // No | SATUAN KERJA | JUMLAH ASET | SATUAN | Kondisi | Tgl Cek | Petugas | NILAI | KET
                        $sheet->setCellValue('B' . $currentRow, $item->institution?->name);
                        $sheet->setCellValue('C' . $currentRow, $item->stock);
                        $sheet->setCellValue('D' . $currentRow, $item->unit);

                        // Condition mapping for Laporan Aset (E, F, G)
                        if ($item->condition === 'B')
                            $sheet->setCellValue('E' . $currentRow, 'V');
                        if ($item->condition === 'KB')
                            $sheet->setCellValue('F' . $currentRow, 'V');
                        if ($item->condition === 'RB')
                            $sheet->setCellValue('G' . $currentRow, 'V');

                        $sheet->setCellValue('H' . $currentRow, $item->updated_at?->format('Y-m-d'));
                        $sheet->setCellValue('I' . $currentRow, $item->responsible_person);
                        $sheet->setCellValue('J' . $currentRow, $item->price);
                        $sheet->setCellValue('K' . $currentRow, $item->note);
                    }
                }
            }

            // Remove other sheets if only exporting one module
            foreach ($spreadsheet->getSheetNames() as $name) {
                if ($name !== $sheetName) {
                    $spreadsheet->removeSheetByIndex($spreadsheet->getIndex($spreadsheet->getSheetByName($name)));
                }
            }
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = "ISO-{$type}-" . date('YmdHis') . ".xlsx";
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public static function downloadAll()
    {
        $templatePath = public_path('FORMISOPROSEDUR.xlsx');
        $spreadsheet = IOFactory::load($templatePath);

        // Use the UrtProcedureController's procedures mapping
        $controller = new \App\Http\Controllers\Admin\UrtProcedureController();
        $reflector = new \ReflectionClass($controller);
        $property = $reflector->getProperty('procedures');
        $property->setAccessible(true);
        $procedures = $property->getValue($controller);

        foreach ($procedures as $type => $procedureData) {
            $sheetName = $procedureData['sheet'] ?? null;
            if ($sheetName && $spreadsheet->sheetNameExists($sheetName)) {
                $sheet = $spreadsheet->getSheetByName($sheetName);

                $model = $procedureData['model'];
                $query = $model::query();

                if (isset($procedureData['type']))
                    $query->where('type', $procedureData['type']);
                if (isset($procedureData['category']))
                    $query->where('category', $procedureData['category']);

                $data = $query->latest()->limit(100)->get(); // Limit to 100 per sheet for safety

                $startRow = 10;
                foreach ($data as $index => $item) {
                    $currentRow = $startRow + $index;
                    $sheet->setCellValue('A' . $currentRow, $index + 1);

                    if ($model === MaintenanceLog::class) {
                        if (str_contains($sheetName, 'PEMELIHARAAN GEDUNG')) {
                            // ...
                            $sheet->setCellValue('B' . $currentRow, $item->title);
                            $sheet->setCellValue('C' . $currentRow, $item->check_standard);
                            $sheet->setCellValue('D' . $currentRow, $item->check_method);
                            $sheet->setCellValue('E' . $currentRow, $item->check_frequency);

                            $months = ['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun'];
                            $cols = ['F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'];
                            foreach ($months as $i => $m) {
                                $sheet->setCellValue($cols[$i] . $currentRow, $item->$m);
                            }

                            if ($index === 0) {
                                $sheet->setCellValue('H4', $item->location);
                                $sheet->setCellValue('H5', $item->year);
                            }
                        } elseif (str_contains($sheetName, 'PEMELIHARAAN KAMAR MANDI')) {
                            $sheet->setCellValue('B' . $currentRow, $item->completed_at);
                            $sheet->setCellValue('C' . $currentRow, $item->location);
                            $sheet->setCellValue('D' . $currentRow, $item->kran_air);
                            $sheet->setCellValue('E' . $currentRow, $item->lampu);
                            $sheet->setCellValue('F' . $currentRow, $item->fiting_lampu);
                            $sheet->setCellValue('G' . $currentRow, $item->saklar_lampu);
                            $sheet->setCellValue('H' . $currentRow, $item->ember);
                            $sheet->setCellValue('I' . $currentRow, $item->gayung);
                            $sheet->setCellValue('J' . $currentRow, $item->closet);
                            $sheet->setCellValue('K' . $currentRow, $item->pintu);
                            $sheet->setCellValue('L' . $currentRow, $item->grendel_pintu);
                            $sheet->setCellValue('M' . $currentRow, $item->performer?->name ?? 'Staff URT');
                            $sheet->setCellValue('N' . $currentRow, $item->action_taken);
                            $sheet->setCellValue('O' . $currentRow, $item->description);
                        } elseif (str_contains($sheetName, 'PEMELIHARAAN AC')) {
                            $sheet->setCellValue('B' . $currentRow, $item->room?->name ?? $item->location);
                            $sheet->setCellValue('C' . $currentRow, $item->ac_indoor_pc);
                            $sheet->setCellValue('D' . $currentRow, $item->ac_indoor_sw);
                            $sheet->setCellValue('E' . $currentRow, $item->ac_outdoor_freon);
                            $sheet->setCellValue('F' . $currentRow, $item->ac_outdoor_amp);
                            $sheet->setCellValue('G' . $currentRow, $item->ac_kelistrikan_jaringan);
                            $sheet->setCellValue('H' . $currentRow, $item->ac_kelistrikan_tegangan);
                            $sheet->setCellValue('I' . $currentRow, $item->performer?->name ?? 'Staff URT');

                            if ($index === 0) {
                                $sheet->setCellValue('G4', $item->ac_divisi);
                                $sheet->setCellValue('G5', $item->ac_tgl_bln);
                            }
                        } elseif (str_contains($sheetName, 'LAPORAN PEMELIHARAAN SARPRAS')) {
                            $sheet->setCellValue('B' . $currentRow, $item->title);
                            $sheet->setCellValue('C' . $currentRow, $item->check_standard); // Jenis Pekerjaan
                            $sheet->setCellValue('D' . $currentRow, $item->room?->name ?? $item->location);
                            $sheet->setCellValue('E' . $currentRow, $item->before_condition);
                            $sheet->setCellValue('F' . $currentRow, $item->action_taken);
                            $sheet->setCellValue('G' . $currentRow, $item->after_condition);
                            $sheet->setCellValue('H' . $currentRow, $item->performer?->name ?? 'Staff URT');
                            $sheet->setCellValue('I' . $currentRow, $item->description);

                            if ($index === 0) {
                                $sheet->setCellValue('B3', $item->subcategory);
                            }
                        } elseif (str_contains($sheetName, 'JADWAL AGENDA PERBAIKAN SARPRAS')) {
                            $sheet->setCellValue('B' . $currentRow, $item->title);
                            $sheet->setCellValue('C' . $currentRow, strtoupper($item->status ?? 'PENDING'));
                            $sheet->setCellValue('D' . $currentRow, $item->performer?->name ?? 'Staff URT');
                            $sheet->setCellValue('E' . $currentRow, $item->description);

                            if ($index === 0) {
                                $sheet->setCellValue('B3', $item->room?->name ?? $item->location);
                                $sheet->setCellValue('B4', $item->scheduled_at);
                            }
                        } else {
                            $sheet->setCellValue('B' . $currentRow, $item->title);
                            $sheet->setCellValue('C' . $currentRow, $item->subcategory ?? $item->type);
                            $sheet->setCellValue('D' . $currentRow, $item->location);
                            $sheet->setCellValue('E' . $currentRow, $item->before_condition);
                            $sheet->setCellValue('F' . $currentRow, $item->action_taken);
                            $sheet->setCellValue('G' . $currentRow, $item->after_condition);
                            $sheet->setCellValue('H' . $currentRow, $item->performer?->name ?? 'Staff URT');
                            $sheet->setCellValue('I' . $currentRow, $item->description);
                        }
                    } elseif ($model === Item::class) {
                        if (str_contains($sheetName, 'BUKU INDUK')) {
                            // A: No (handled by index)
                            $sheet->setCellValue('B' . $currentRow, $item->institution?->name);
                            $sheet->setCellValue('C' . $currentRow, $item->room?->name);
                            $sheet->setCellValue('D' . $currentRow, $item->name);
                            $sheet->setCellValue('E' . $currentRow, $item->brand);
                            $sheet->setCellValue('F' . $currentRow, $item->specification);
                            $sheet->setCellValue('G' . $currentRow, $item->purchased_at);
                            $sheet->setCellValue('H' . $currentRow, $item->no_urut_satker);
                            $sheet->setCellValue('I' . $currentRow, $item->code);
                            $sheet->setCellValue('J' . $currentRow, $item->no_urut_pondok);
                            $sheet->setCellValue('K' . $currentRow, $item->stock);
                            $sheet->setCellValue('L' . $currentRow, $item->unit);
                            $sheet->setCellValue('M' . $currentRow, $item->source);
                            $sheet->setCellValue('N' . $currentRow, $item->received_at);
                            $sheet->setCellValue('O' . $currentRow, $item->condition);
                            $sheet->setCellValue('P' . $currentRow, $item->price);
                            $sheet->setCellValue('Q' . $currentRow, $item->depreciation_price);
                            $sheet->setCellValue('R' . $currentRow, $item->responsible_person);
                            $sheet->setCellValue('S' . $currentRow, $item->note);
                        } elseif (str_contains($sheetName, 'KARTU INVENTARIS RUANGAN')) {
                            // A: No, B: Nama, C: Merk/Kode, D: No. Seri, E: Ukuran, F: Bahan, G: Tahun, H: No Kode, I: Jumlah, J: Baik, K: KB, L: RB, M: Ket
                            $sheet->setCellValue('B' . $currentRow, $item->name);
                            $sheet->setCellValue('C' . $currentRow, $item->brand);
                            $sheet->setCellValue('D' . $currentRow, $item->serial_number);
                            $sheet->setCellValue('E' . $currentRow, $item->size);
                            $sheet->setCellValue('F' . $currentRow, $item->material);
                            $sheet->setCellValue('G' . $currentRow, $item->purchased_at);
                            $sheet->setCellValue('H' . $currentRow, $item->code);
                            $sheet->setCellValue('I' . $currentRow, $item->stock);

                            // Condition mapping (J, K, L)
                            if ($item->condition === 'B')
                                $sheet->setCellValue('J' . $currentRow, 'V');
                            if ($item->condition === 'KB')
                                $sheet->setCellValue('K' . $currentRow, 'V');
                            if ($item->condition === 'RB')
                                $sheet->setCellValue('L' . $currentRow, 'V');

                            $sheet->setCellValue('M' . $currentRow, $item->note);

                            if ($index === 0) {
                                $sheet->setCellValue('D3', 'NUSA TENGGARA BARAT');
                                $sheet->setCellValue('D4', 'KOTA MATARAM');
                                $sheet->setCellValue('D5', 'PAH-MTR-' . ($item->institution?->code ?? 'GEN'));
                                $sheet->setCellValue('D6', $item->institution?->name);
                                $sheet->setCellValue('D7', $item->room?->name);
                            }
                        } else {
                            // FORMULIR LAPORAN ASET PAH MATARAM
                            // A: No, B: Satker, C: Jumlah, D: Satuan, E: Baik, F: KB, G: Rusak, H: Tgl, I: Petugas, J: Nilai, K: Ket
                            $sheet->setCellValue('B' . $currentRow, $item->institution?->name);
                            $sheet->setCellValue('C' . $currentRow, $item->stock);
                            $sheet->setCellValue('D' . $currentRow, $item->unit);

                            // Condition mapping for Laporan Aset (E, F, G)
                            if ($item->condition === 'B')
                                $sheet->setCellValue('E' . $currentRow, 'V');
                            if ($item->condition === 'KB')
                                $sheet->setCellValue('F' . $currentRow, 'V');
                            if ($item->condition === 'RB')
                                $sheet->setCellValue('G' . $currentRow, 'V');

                            $sheet->setCellValue('H' . $currentRow, $item->updated_at?->format('Y-m-d'));
                            $sheet->setCellValue('I' . $currentRow, $item->responsible_person);
                            $sheet->setCellValue('J' . $currentRow, $item->price);
                            $sheet->setCellValue('K' . $currentRow, $item->note);
                        }
                    }
                }
            }
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = "ISO-FULL-REKAP-" . date('YmdHis') . ".xlsx";
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
