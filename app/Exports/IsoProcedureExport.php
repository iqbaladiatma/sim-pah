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
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use App\Models\ParkingLog;
use App\Models\IsoChecklist;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $templatePath = public_path('FORMISOPROSEDUR.xlsx');
        $spreadsheet = IOFactory::load($templatePath);

        $sheetName = $procedureData['sheet'] ?? null;
        if ($sheetName && $spreadsheet->sheetNameExists($sheetName)) {
            $sheet = $spreadsheet->getSheetByName($sheetName);

            // Fill data based on model type
            $modelClass = $procedureData['model'];
            $query = $modelClass::query();

            // Eager load common relations to avoid N+1
            $availableRelations = ['institution', 'room', 'vehicle', 'performer', 'user'];
            foreach ($availableRelations as $relation) {
                if (method_exists($modelClass, $relation)) {
                    $query->with($relation);
                }
            }

            if (isset($procedureData['type'])) {
                $query->where('type', $procedureData['type']);
            }
            if (isset($procedureData['category'])) {
                $query->where('category', $procedureData['category']);
            }

            try {
                $data = $query->latest()->limit(500)->get();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("ISO Export Query Error: " . $e->getMessage());
                $data = collect();
            }

            // LOGIC TO MAP DATA TO TEMPLATE
            // This varies by sheet. For now, let's start with a generic row filling starting at a common row (e.g., row 10)
            $startRow = 10;

            foreach ($data as $index => $item) {
                $currentRow = $startRow + $index;
                $sheet->setCellValue('A' . $currentRow, $index + 1);

                if ($modelClass === MaintenanceLog::class) {
                    if (str_contains($sheetName, 'PEMELIHARAAN GEDUNG')) {
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
                            $sheet->setCellValue('H3', $item->subcategory);
                            $sheet->setCellValue('H4', $item->location);
                            $sheet->setCellValue('H5', $item->year);
                        }
                    } elseif (str_contains($sheetName, 'PEMELIHARAAN POMPA') || str_contains($sheetName, 'PEMELIHARAAN AIR BERSI') || str_contains($sheetName, 'PEMELIHARAAN AIR MINUM') || str_contains($sheetName, 'PEMELIHARAAN GENSET')) {
                        $sheet->setCellValue('B' . $currentRow, $item->title);
                        $sheet->setCellValue('C' . $currentRow, $item->check_standard);
                        $sheet->setCellValue('D' . $currentRow, $item->check_method);
                        $sheet->setCellValue('E' . $currentRow, $item->check_frequency);

                        $months = ['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun'];
                        if (str_contains($sheetName, 'PEMELIHARAAN AIR MINUM') || str_contains($sheetName, 'PEMELIHARAAN GENSET')) {
                            $areas = ['putra', 'putri'];
                        } elseif (str_contains($sheetName, 'PEMELIHARAAN POMPA')) {
                            $areas = ['putra', 'putri', 'lawata'];
                        } else {
                            $areas = ['lawata', 'putra', 'putri'];
                        }

                        $colIdx = 5;
                        foreach ($months as $m) {
                            foreach ($areas as $a) {
                                $colLetter = Coordinate::stringFromColumnIndex($colIdx + 1);
                                $sheet->setCellValue($colLetter . $currentRow, $item->{$m . '_' . $a});
                                $colIdx++;
                            }
                        }

                        if ($index === 0) {
                            if (str_contains($sheetName, 'PEMELIHARAAN GENSET')) {
                                $sheet->setCellValue('B4', $item->subcategory);
                                $sheet->setCellValue('B5', $item->serial_number);
                                $sheet->setCellValue('B6', $item->location);
                                $sheet->setCellValue('H7', $item->year);
                            } else {
                                $sheet->setCellValue('B7', $item->location);
                                $sheet->setCellValue('H7', $item->year);
                            }
                        }
                    } elseif (str_contains($sheetName, 'PEMELIHARAAN KAMAR MANDI')) {
                        if ($index === 0) {
                            $sheet->setCellValue('B3', $item->subcategory); // AREA
                            $sheet->setCellValue('G3', $item->completed_at ? $item->completed_at->format('Y-m-d') : ''); // Tgl Kegiatan
                        }
                        $sheet->setCellValue('B' . $currentRow, $item->completed_at ? $item->completed_at->format('Y-m-d') : '');
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
                    } elseif (str_contains($sheetName, 'JADWAL AGENDA PERBAIKAN SARPRAS')) {
                        $sheet->setCellValue('B' . $currentRow, $item->title);
                        $sheet->setCellValue('C' . $currentRow, strtoupper($item->status ?? 'PENDING'));
                        $sheet->setCellValue('D' . $currentRow, $item->performer?->name ?? 'Staff URT');
                        $sheet->setCellValue('E' . $currentRow, $item->description);

                        if ($index === 0) {
                            $sheet->setCellValue('B3', $item->room?->name ?? $item->location);
                            $sheet->setCellValue('B4', $item->scheduled_at);
                        }
                    } elseif (str_contains($sheetName, 'FORM JADWAL PEMELIHARAAN KEBERS')) {
                        $sheet->setCellValue('B' . $currentRow, $item->subcategory);
                        $sheet->setCellValue('C' . $currentRow, $item->performer?->name);
                        $sheet->setCellValue('D' . $currentRow, $item->room?->name ?? $item->location);
                        $sheet->setCellValue('E' . $currentRow, $item->scheduled_at);
                        $sheet->setCellValue('F' . $currentRow, $item->title);
                        $sheet->setCellValue('G' . $currentRow, $item->description);
                    } elseif (str_contains($sheetName, 'FORM PEMELIHARAAN JARINGAN LIST')) {
                        // Electrical Maintenance
                        $sheet->setCellValue('B' . $currentRow, $item->title);
                        $sheet->setCellValue('C' . $currentRow, $item->check_standard);
                        $sheet->setCellValue('D' . $currentRow, $item->check_method);
                        $sheet->setCellValue('E' . $currentRow, $item->frequency);

                        foreach (['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun'] as $index => $month) {
                            $colIndex = 6 + $index; // F start
                            $status = $item->{$month . '_status'};
                            if ($status)
                                $sheet->setCellValueByColumnAndRow($colIndex, $currentRow, $status);
                        }
                    } else {
                        // Default Maintenance Logic
                        $sheet->setCellValue('B' . $currentRow, $item->title);
                        $sheet->setCellValue('C' . $currentRow, $item->subcategory ?? $item->type);
                        $sheet->setCellValue('D' . $currentRow, $item->location);
                        $sheet->setCellValue('E' . $currentRow, $item->before_condition);
                        $sheet->setCellValue('F' . $currentRow, $item->action_taken);
                        $sheet->setCellValue('G' . $currentRow, $item->after_condition);
                        $sheet->setCellValue('H' . $currentRow, $item->performer?->name ?? 'Staff URT');
                        $sheet->setCellValue('I' . $currentRow, $item->description);
                    }
                } elseif ($modelClass === Item::class) {
                    if (str_contains($sheetName, 'BUKU INDUK')) {
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
                        $sheet->setCellValue('B' . $currentRow, $item->name);
                        $sheet->setCellValue('C' . $currentRow, $item->brand);
                        $sheet->setCellValue('D' . $currentRow, $item->serial_number);
                        $sheet->setCellValue('E' . $currentRow, $item->size);
                        $sheet->setCellValue('F' . $currentRow, $item->material);
                        $sheet->setCellValue('G' . $currentRow, $item->purchased_at);
                        $sheet->setCellValue('H' . $currentRow, $item->code);
                        $sheet->setCellValue('I' . $currentRow, $item->stock);

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
                        $sheet->setCellValue('B' . $currentRow, $item->institution?->name);
                        $sheet->setCellValue('C' . $currentRow, $item->stock);
                        $sheet->setCellValue('D' . $currentRow, $item->unit);

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
                } elseif ($model === VehicleRequest::class) {
                     $sheet->setCellValue('B' . $currentRow, $item->request_date ? date('d/m/Y', strtotime($item->request_date)) : '');
                     $sheet->setCellValue('C' . $currentRow, $item->vehicle?->name);
                     $sheet->setCellValue('D' . $currentRow, $item->time_range);
                     $sheet->setCellValue('E' . $currentRow, $item->institution_name ?? $item->user?->institution?->name);
                     $sheet->setCellValue('F' . $currentRow, $item->destination);
                     $sheet->setCellValue('G' . $currentRow, $item->start_mileage);
                     $sheet->setCellValue('H' . $currentRow, $item->fuel_level_before);
                     $sheet->setCellValue('I' . $currentRow, $item->condition_before);
                     $sheet->setCellValue('J' . $currentRow, $item->responsible_person);
                } elseif ($model === BorrowingRecord::class) {
                     $sheet->setCellValue('B' . $currentRow, $item->borrow_date);
                     $sheet->setCellValue('C' . $currentRow, $item->borrower_name);
                     $sheet->setCellValue('D' . $currentRow, $item->item?->name);
                     $sheet->setCellValue('E' . $currentRow, $item->quantity);
                     $sheet->setCellValue('F' . $currentRow, $item->borrow_condition);
                     $sheet->setCellValue('H' . $currentRow, $item->actual_return_date);
                     $sheet->setCellValue('I' . $currentRow, $item->return_condition);
                     $sheet->setCellValue('K' . $currentRow, $item->reason);
                } elseif ($model === ParkingLog::class) {
                     $sheet->setCellValue('B' . $currentRow, $item->date);
                     $sheet->setCellValue('C' . $currentRow, $item->plate_number);
                     $sheet->setCellValue('D' . $currentRow, $item->vehicle_type);
                     $sheet->setCellValue('E' . $currentRow, $item->entry_time);
                     $sheet->setCellValue('F' . $currentRow, $item->exit_time);
                     $sheet->setCellValue('G' . $currentRow, $item->description);
                } elseif ($model === AssetLifecycleLog::class) {
                    $sheet->setCellValue('B' . $currentRow, $item->item?->name);
                    $sheet->setCellValue('C' . $currentRow, $item->item?->code);
                    $sheet->setCellValue('D' . $currentRow, $item->item?->brand);
                    $sheet->setCellValue('E' . $currentRow, $item->item?->purchased_at);
                    $sheet->setCellValue('F' . $currentRow, $item->reason);
                    $sheet->setCellValue('G' . $currentRow, $item->approved_by);
                } elseif ($model === Vehicle::class) {
                    $sheet->setCellValue('B' . $currentRow, $item->name);
                    $sheet->setCellValue('C' . $currentRow, $item->plate_number);
                    $sheet->setCellValue('D' . $currentRow, $item->brand);
                    $sheet->setCellValue('E' . $currentRow, $item->type);
                    $sheet->setCellValue('F' . $currentRow, $item->year);
                    $sheet->setCellValue('G' . $currentRow, $item->stnk_expiry);
                    $sheet->setCellValue('H' . $currentRow, $item->tax_expiry);
                    $sheet->setCellValue('I' . $currentRow, $item->status);
                    $sheet->setCellValue('J' . $currentRow, $item->note);
                } elseif ($model === IsoChecklist::class) {
                    $sheet->setCellValue('B' . $currentRow, $item->title);
                    $sheet->setCellValue('C' . $currentRow, $item->category);
                    $sheet->setCellValue('D' . $currentRow, $item->frequency);
                    $sheet->setCellValue('E' . $currentRow, $item->is_active ? 'Aktif' : 'Non-Aktif');
                    $sheet->setCellValue('F' . $currentRow, json_encode($item->items));
                }
            }


            // Remove other sheets if only exporting one module
            foreach ($spreadsheet->getSheetNames() as $name) {
                if ($name !== $sheetName) {
                    $spreadsheet->removeSheetByIndex($spreadsheet->getIndex($spreadsheet->getSheetByName($name)));
                }
            }
        }

        if (ob_get_length())
            ob_end_clean();

        try {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setPreCalculateFormulas(false);
            $fileName = "ISO-REKAP-" . strtoupper($type) . "-" . date('YmdHis') . ".xlsx";
            $tempFile = tempnam(sys_get_temp_dir(), 'excel');
            $writer->save($tempFile);

            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("ISO Export Save Error: " . $e->getMessage());
            return back()->with('error', 'Gagal membuat file export: ' . $e->getMessage());
        }
    }

    public static function downloadAll()
    {
        // Aggressive memory and time management
        @ini_set('memory_limit', '-1');
        @set_time_limit(0);

        if (function_exists('gc_enable')) {
            gc_enable();
        }

        $templatePath = public_path('FORMISOPROSEDUR.xlsx');
        if (!file_exists($templatePath)) {
            throw new \Exception("Template file not found at: " . $templatePath);
        }

        try {
            $spreadsheet = IOFactory::load($templatePath);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("ISO Export Load Error: " . $e->getMessage());
            throw $e;
        }

        // Use the UrtProcedureController's procedures mapping
        $controller = new \App\Http\Controllers\Admin\UrtProcedureController();
        $reflector = new \ReflectionClass($controller);
        $property = $reflector->getProperty('procedures');
        $property->setAccessible(true);
        $procedures = $property->getValue($controller);

        $processedSheets = [];

        foreach ($procedures as $type => $procedureData) {
            $sheetName = $procedureData['sheet'] ?? null;
            if ($sheetName && !in_array($sheetName, $processedSheets) && $spreadsheet->sheetNameExists($sheetName)) {
                $processedSheets[] = $sheetName;
                $sheet = $spreadsheet->getSheetByName($sheetName);

                $modelClass = $procedureData['model'];
                if (!class_exists($modelClass)) {
                    \Illuminate\Support\Facades\Log::warning("ISO Export: Model class {$modelClass} not found. Skipping.");
                    continue;
                }

                $query = $modelClass::query();

                // Eager load common relations
                $availableRelations = ['institution', 'room', 'vehicle', 'performer', 'user'];
                foreach ($availableRelations as $relation) {
                    if (method_exists($modelClass, $relation)) {
                        $query->with($relation);
                    }
                }

                if (isset($procedureData['type']))
                    $query->where('type', $procedureData['type']);
                if (isset($procedureData['category']))
                    $query->where('category', $procedureData['category']);

                $data = $query->latest()->limit(100)->get();

                $startRow = 10;
                foreach ($data as $index => $item) {
                    $currentRow = $startRow + $index;
                    $sheet->setCellValue('A' . $currentRow, $index + 1);

                    if ($modelClass === MaintenanceLog::class) {
                        if (str_contains($sheetName, 'PEMELIHARAAN GEDUNG')) {
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
                                $sheet->setCellValue('H3', $item->subcategory);
                                $sheet->setCellValue('H4', $item->location);
                                $sheet->setCellValue('H5', $item->year);
                            }
                        } elseif (str_contains($sheetName, 'PEMELIHARAAN POMPA') || str_contains($sheetName, 'PEMELIHARAAN AIR BERSI') || str_contains($sheetName, 'PEMELIHARAAN AIR MINUM') || str_contains($sheetName, 'PEMELIHARAAN GENSET')) {
                            $sheet->setCellValue('B' . $currentRow, $item->title);
                            $sheet->setCellValue('C' . $currentRow, $item->check_standard);
                            $sheet->setCellValue('D' . $currentRow, $item->check_method);
                            $sheet->setCellValue('E' . $currentRow, $item->check_frequency);

                            $months = ['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun'];

                            if (str_contains($sheetName, 'PEMELIHARAAN AIR MINUM') || str_contains($sheetName, 'PEMELIHARAAN GENSET')) {
                                $areas = ['putra', 'putri'];
                            } elseif (str_contains($sheetName, 'PEMELIHARAAN POMPA')) {
                                $areas = ['putra', 'putri', 'lawata'];
                            } else {
                                $areas = ['lawata', 'putra', 'putri'];
                            }

                            $colIdx = 5;

                            foreach ($months as $m) {
                                foreach ($areas as $a) {
                                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                                    $sheet->setCellValue($colLetter . $currentRow, $item->{$m . '_' . $a});
                                    $colIdx++;
                                }
                            }

                            if ($index === 0) {
                                if (str_contains($sheetName, 'PEMELIHARAAN GENSET')) {
                                    $sheet->setCellValue('B4', $item->subcategory);
                                    $sheet->setCellValue('B5', $item->serial_number);
                                    $sheet->setCellValue('B6', $item->location);
                                    $sheet->setCellValue('H7', $item->year);
                                } else {
                                    $sheet->setCellValue('B7', $item->location);
                                    $sheet->setCellValue('H7', $item->year);
                                }
                            }
                        } elseif (str_contains($sheetName, 'PEMELIHARAAN KAMAR MANDI')) {
                            if ($index === 0) {
                                $sheet->setCellValue('B3', $item->subcategory);
                                $sheet->setCellValue('G3', $item->completed_at ? $item->completed_at->format('Y-m-d') : '');
                            }
                            $sheet->setCellValue('B' . $currentRow, $item->completed_at ? $item->completed_at->format('Y-m-d') : '');
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
                            $sheet->setCellValue('C' . $currentRow, $item->check_standard);
                            $sheet->setCellValue('C' . $currentRow, $item->check_standard);
                            $sheet->setCellValue('D' . $currentRow, $item->room?->name ?? $item->location);
                            $sheet->setCellValue('E' . $currentRow, $item->before_condition);
                            $sheet->setCellValue('F' . $currentRow, $item->action_taken);
                            $sheet->setCellValue('G' . $currentRow, $item->after_condition);
                            $sheet->setCellValue('H' . $currentRow, $item->performer?->name ?? 'Staff URT');
                            $sheet->setCellValue('I' . $currentRow, $item->description);

                            if ($index === 0) {
                                $sheet->setCellValue('B3', $item->subcategory);
                            }
                        } elseif (str_contains($sheetName, 'FORM KEGIATAN PEKANAN PETUGAS')) {
                            $sheet->setCellValue('B' . $currentRow, $item->week_name);
                            $sheet->setCellValue('C' . $currentRow, $item->performer?->name);
                            $sheet->setCellValue('D' . $currentRow, $item->room?->name ?? $item->location);
                            $sheet->setCellValue('E' . $currentRow, $item->title);
                            $sheet->setCellValue('F' . $currentRow, $item->scheduled_at);
                            $sheet->setCellValue('G' . $currentRow, $item->is_checked ? 'V' : '');
                            $sheet->setCellValue('H' . $currentRow, $item->description);
                            $sheet->setCellValue('I' . $currentRow, $item->responsible_person);
                        } elseif (str_contains($sheetName, 'PEMELIHARAAN POMPA') || str_contains($sheetName, 'PEMELIHARAAN AIR BERSI') || str_contains($sheetName, 'PEMELIHARAAN AIR MINUM') || str_contains($sheetName, 'PEMELIHARAAN GENSET')) {
                            $sheet->setCellValue('B' . $currentRow, $item->title);
                            $sheet->setCellValue('C' . $currentRow, $item->check_standard);
                            $sheet->setCellValue('D' . $currentRow, $item->check_method);
                            $sheet->setCellValue('E' . $currentRow, $item->check_frequency);

                            $months = ['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun'];
                            if (str_contains($sheetName, 'PEMELIHARAAN AIR MINUM') || str_contains($sheetName, 'PEMELIHARAAN GENSET')) {
                                $areas = ['putra', 'putri'];
                            } elseif (str_contains($sheetName, 'PEMELIHARAAN POMPA')) {
                                $areas = ['putra', 'putri', 'lawata'];
                            } else {
                                $areas = ['lawata', 'putra', 'putri'];
                            }

                            $colIdx = 5;
                            foreach ($months as $m) {
                                foreach ($areas as $a) {
                                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                                    $sheet->setCellValue($colLetter . $currentRow, $item->{$m . '_' . $a});
                                    $colIdx++;
                                }
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
                        } elseif (str_contains($sheetName, 'JADWAL AGENDA PERBAIKAN SARPRAS')) {
                            $sheet->setCellValue('B' . $currentRow, $item->title);
                            $sheet->setCellValue('C' . $currentRow, strtoupper($item->status ?? 'PENDING'));
                            $sheet->setCellValue('D' . $currentRow, $item->performer?->name ?? 'Staff URT');
                            $sheet->setCellValue('E' . $currentRow, $item->description);

                            if ($index === 0) {
                                $sheet->setCellValue('B3', $item->room?->name ?? $item->location);
                                $sheet->setCellValue('B4', $item->scheduled_at);
                            }
                        } elseif (str_contains($sheetName, 'FORM JADWAL PEMELIHARAAN KEBERS')) {
                            $sheet->setCellValue('B' . $currentRow, $item->subcategory);
                            $sheet->setCellValue('C' . $currentRow, $item->performer?->name);
                            $sheet->setCellValue('D' . $currentRow, $item->room?->name ?? $item->location);
                            $sheet->setCellValue('E' . $currentRow, $item->scheduled_at);
                            $sheet->setCellValue('F' . $currentRow, $item->title);
                            $sheet->setCellValue('G' . $currentRow, $item->description);
                        } elseif (str_contains($sheetName, 'FORM PEMELIHARAAN JARINGAN LIST')) {
                            $sheet->setCellValue('B' . $currentRow, $item->title);
                            $sheet->setCellValue('C' . $currentRow, $item->check_standard);
                            $sheet->setCellValue('D' . $currentRow, $item->check_method);
                            $sheet->setCellValue('E' . $currentRow, $item->frequency);

                            foreach (['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun'] as $index => $month) {
                                $colIndex = 6 + $index;
                                $status = $item->{$month . '_status'};
                                if ($status)
                                    $sheet->setCellValueByColumnAndRow($colIndex, $currentRow, $status);
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
                    } elseif ($modelClass === Item::class) {
                        if (str_contains($sheetName, 'BUKU INDUK')) {
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
                            $sheet->setCellValue('B' . $currentRow, $item->name);
                            $sheet->setCellValue('C' . $currentRow, $item->brand);
                            $sheet->setCellValue('D' . $currentRow, $item->serial_number);
                            $sheet->setCellValue('E' . $currentRow, $item->size);
                            $sheet->setCellValue('F' . $currentRow, $item->material);
                            $sheet->setCellValue('G' . $currentRow, $item->purchased_at);
                            $sheet->setCellValue('H' . $currentRow, $item->code);
                            $sheet->setCellValue('I' . $currentRow, $item->stock);

                            if ($item->condition === 'B') $sheet->setCellValue('J' . $currentRow, 'V');
                            if ($item->condition === 'KB') $sheet->setCellValue('K' . $currentRow, 'V');
                            if ($item->condition === 'RB') $sheet->setCellValue('L' . $currentRow, 'V');

                            $sheet->setCellValue('M' . $currentRow, $item->note);
                            if ($index === 0) {
                                $sheet->setCellValue('D3', 'NUSA TENGGARA BARAT');
                                $sheet->setCellValue('D4', 'KOTA MATARAM');
                                $sheet->setCellValue('D5', 'PAH-MTR-' . ($item->institution?->code ?? 'GEN'));
                                $sheet->setCellValue('D6', $item->institution?->name);
                                $sheet->setCellValue('D7', $item->room?->name);
                            }
                        } else {
                            $sheet->setCellValue('B' . $currentRow, $item->institution?->name);
                            $sheet->setCellValue('C' . $currentRow, $item->stock);
                            $sheet->setCellValue('D' . $currentRow, $item->unit);

                            if ($item->condition === 'B') $sheet->setCellValue('E' . $currentRow, 'V');
                            if ($item->condition === 'KB') $sheet->setCellValue('F' . $currentRow, 'V');
                            if ($item->condition === 'RB') $sheet->setCellValue('G' . $currentRow, 'V');

                            $sheet->setCellValue('H' . $currentRow, $item->updated_at?->format('Y-m-d'));
                            $sheet->setCellValue('I' . $currentRow, $item->responsible_person);
                            $sheet->setCellValue('J' . $currentRow, $item->price);
                            $sheet->setCellValue('K' . $currentRow, $item->note);
                        }
                    } elseif ($model === VehicleRequest::class) {
                        $sheet->setCellValue('B' . $currentRow, $item->request_date ? date('d/m/Y', strtotime($item->request_date)) : '');
                        $sheet->setCellValue('C' . $currentRow, $item->vehicle?->name);
                        $sheet->setCellValue('D' . $currentRow, $item->time_range);
                        $sheet->setCellValue('E' . $currentRow, $item->institution_name ?? $item->user?->institution?->name);
                        $sheet->setCellValue('F' . $currentRow, $item->destination);
                        $sheet->setCellValue('G' . $currentRow, $item->start_mileage);
                        $sheet->setCellValue('H' . $currentRow, $item->fuel_level_before);
                        $sheet->setCellValue('I' . $currentRow, $item->condition_before);
                        $sheet->setCellValue('J' . $currentRow, $item->responsible_person);
                    } elseif ($model === BorrowingRecord::class) {
                        $sheet->setCellValue('B' . $currentRow, $item->borrow_date);
                        $sheet->setCellValue('C' . $currentRow, $item->borrower_name);
                        $sheet->setCellValue('D' . $currentRow, $item->item?->name);
                        $sheet->setCellValue('E' . $currentRow, $item->quantity);
                        $sheet->setCellValue('F' . $currentRow, $item->borrow_condition);
                        $sheet->setCellValue('H' . $currentRow, $item->actual_return_date);
                        $sheet->setCellValue('I' . $currentRow, $item->return_condition);
                        $sheet->setCellValue('K' . $currentRow, $item->reason);
                    } elseif ($model === ParkingLog::class) {
                        $sheet->setCellValue('B' . $currentRow, $item->date);
                        $sheet->setCellValue('C' . $currentRow, $item->plate_number);
                        $sheet->setCellValue('D' . $currentRow, $item->vehicle_type);
                        $sheet->setCellValue('E' . $currentRow, $item->entry_time);
                        $sheet->setCellValue('F' . $currentRow, $item->exit_time);
                        $sheet->setCellValue('G' . $currentRow, $item->description);
                    } elseif ($model === AssetLifecycleLog::class) {
                        $sheet->setCellValue('B' . $currentRow, $item->item?->name);
                        $sheet->setCellValue('C' . $currentRow, $item->item?->code);
                        $sheet->setCellValue('D' . $currentRow, $item->item?->brand);
                        $sheet->setCellValue('E' . $currentRow, $item->item?->purchased_at);
                        $sheet->setCellValue('F' . $currentRow, $item->reason);
                        $sheet->setCellValue('G' . $currentRow, $item->approved_by);
                    } elseif ($model === Vehicle::class) {
                        $sheet->setCellValue('B' . $currentRow, $item->name);
                        $sheet->setCellValue('C' . $currentRow, $item->plate_number);
                        $sheet->setCellValue('D' . $currentRow, $item->brand);
                        $sheet->setCellValue('E' . $currentRow, $item->type);
                        $sheet->setCellValue('F' . $currentRow, $item->year);
                        $sheet->setCellValue('G' . $currentRow, $item->stnk_expiry);
                        $sheet->setCellValue('H' . $currentRow, $item->tax_expiry);
                        $sheet->setCellValue('I' . $currentRow, $item->status);
                        $sheet->setCellValue('J' . $currentRow, $item->note);
                    } elseif ($model === IsoChecklist::class) {
                        $sheet->setCellValue('B' . $currentRow, $item->title);
                        $sheet->setCellValue('C' . $currentRow, $item->category);
                        $sheet->setCellValue('D' . $currentRow, $item->frequency);
                        $sheet->setCellValue('E' . $currentRow, $item->is_active ? 'Aktif' : 'Non-Aktif');
                        $sheet->setCellValue('F' . $currentRow, json_encode($item->items));
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

