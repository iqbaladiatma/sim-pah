<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\MaintenanceLog;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use App\Models\IsoChecklist;
use App\Models\IsoChecklistLog;
use App\Models\BorrowingRecord;
use App\Models\AssetLifecycleLog;
use App\Models\ParkingLog;
use App\Models\Item;
use App\Models\Institution;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use App\Exports\IsoProcedureExport;

class UrtProcedureController extends Controller
{
    protected $procedures = [
        // GROUP ASET
        'pendataan-aset' => ['title' => 'Pendataan Aset PAH Mataram', 'model' => Item::class, 'icon' => 'LibraryIcon', 'group' => 'aset', 'sheet' => 'FORMULIR LAPORAN ASET PAH MATAR'],
        'buku-induk' => ['title' => 'Buku Induk Inventaris Barang', 'model' => Item::class, 'icon' => 'BookOpenIcon', 'group' => 'aset', 'sheet' => 'BUKU INDUK INVENTARIS BARANG'],
        'kir-ruangan' => ['title' => 'Kartu Inventaris Ruangan (KIR)', 'model' => Item::class, 'icon' => 'HomeIcon', 'group' => 'aset', 'sheet' => 'KARTU INVENTARIS RUANGAN (KIR)'],
        'monitoring-aset' => ['title' => 'FORMULIR MONITORING ASET', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'category' => 'Aset', 'icon' => 'DesktopComputerIcon', 'group' => 'aset', 'sheet' => 'FORMULIR MONITORING ASET'],
        'pelelangan-aset' => ['title' => 'DAFTAR ASET YANG AKAN DILELANG', 'model' => AssetLifecycleLog::class, 'type' => 'auction', 'icon' => 'CashIcon', 'group' => 'aset', 'sheet' => 'DAFTAR ASET YANG AKAN DILELANG'],
        'berita-acara-pemeriksaan' => ['title' => 'Berita Acara Pemeriksaan Aset', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'category' => 'Pemeriksaan', 'icon' => 'ShieldCheckIcon', 'group' => 'aset', 'sheet' => 'BERITA ACARA PEMERIKSAAN ASET'],

        // GROUP PEMELIHARAAN (SARPRAS)
        'pemeliharaan-gedung' => ['title' => 'Pemeliharaan Gedung', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'category' => 'Gedung', 'icon' => 'OfficeBuildingIcon', 'group' => 'sarpras', 'sheet' => 'FORMULIR PEMELIHARAAN GEDUNG'],
        'pemeliharaan-kamar-mandi' => ['title' => 'Pemeliharaan Kamar Mandi', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'category' => 'Kamar Mandi', 'icon' => 'SparklesIcon', 'group' => 'sarpras', 'sheet' => 'FORMULIR PEMELIHARAAN KAMAR MAN'],
        'pemeliharaan-ac' => ['title' => 'Pemeliharaan AC', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'category' => 'AC', 'icon' => 'WindIcon', 'group' => 'sarpras', 'sheet' => 'FORMULIR PEMELIHARAAN AC'],
        'pemeliharaan-pompa' => ['title' => 'Pemeliharaan Pompa/Filter', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'category' => 'Pompa', 'icon' => 'ColorSwatchIcon', 'group' => 'sarpras', 'sheet' => 'FORMULIR CEKLIST PEMELIHARAAN P'],
        'pemeliharaan-air-bersih' => ['title' => 'Pemeliharaan Air Bersih', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'category' => 'Air Bersih', 'icon' => 'FilterIcon', 'group' => 'sarpras', 'sheet' => 'FORMULIR PEMELIHARAAN AIR BERSI'],
        'pemeliharaan-air-minum' => ['title' => 'Pemeliharaan Air Minum', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'category' => 'Air Minum', 'icon' => 'BeakerIcon', 'group' => 'sarpras', 'sheet' => 'FORMULIR PEMELIHARAAN AIR MINUM'],
        'pemeliharaan-genset' => ['title' => 'Pemeliharaan Genset', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'category' => 'Genset', 'icon' => 'LightningBoltIcon', 'group' => 'sarpras', 'sheet' => 'FORMULIR PEMELIHARAAN GENSET'],
        'pemeliharaan-kipas' => ['title' => 'Pemeliharaan Kipas Angin', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'category' => 'Kipas Angin', 'icon' => 'RefreshIcon', 'group' => 'sarpras', 'sheet' => 'FORMULIR PEMELIHARAAN KIPAS ANG'],
        'pemeliharaan-septik' => ['title' => 'FORMULIR PEMELIHARAAN SEPTIK TANK', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'category' => 'Septik Tank', 'icon' => 'ScaleIcon', 'group' => 'sarpras', 'sheet' => 'FORMULIR PEMELIHARAAN SEPTIK TA'],
        'pemeliharaan-sarpras' => ['title' => 'FORMULIR LAPORAN PEMELIHARAAN SARANA PRASARANA', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'category' => 'Sarpras', 'icon' => 'SparklesIcon', 'group' => 'sarpras', 'sheet' => 'LAPORAN PEMELIHARAAN SARPRAS'],
        'pemeliharaan-listrik' => ['title' => 'FORM PEMELIHARAAN JARINGAN LISTRIK', 'model' => MaintenanceLog::class, 'type' => 'electrical-maintenance', 'category' => 'Listrik', 'icon' => 'LightningBoltIcon', 'group' => 'sarpras', 'sheet' => 'FORM PEMELIHARAAN JARINGAN LIST'],

        // GROUP PERBAIKAN & PROYEK
        'agenda-perbaikan' => ['title' => 'JADWAL AGENDA PERBAIKAN SARPRAS', 'model' => MaintenanceLog::class, 'type' => 'repair', 'icon' => 'CalendarIcon', 'group' => 'proyek', 'sheet' => 'JADWAL AGENDA PERBAIKAN SARPRAS'],
        'rekapan-pengajuan' => ['title' => 'Rekapan Pengajuan Perbaikan', 'model' => MaintenanceLog::class, 'type' => 'repair', 'icon' => 'CollectionIcon', 'group' => 'proyek', 'sheet' => 'REKAPAN PENGAJUAN PERBAIKAN SAR'],
        'pengajuan-rab' => ['title' => 'PENGAJUAN RAB', 'model' => MaintenanceLog::class, 'type' => 'project', 'icon' => 'DocumentTextIcon', 'group' => 'proyek', 'sheet' => 'FORMULIR PENGAJUAN RAB'],
        'laporan-proyek' => ['title' => 'LAPORAN PROYEK KEGIATAN', 'model' => MaintenanceLog::class, 'type' => 'project', 'icon' => 'CubeIcon', 'group' => 'proyek', 'sheet' => 'LAPORAN PROYEK KEGIATAN'],

        // GROUP LOGISTIK & PENGADAAN
        'peminjaman-barang' => ['title' => 'FORMULIR BUKU BESAR PEMINJAMAN BARANG', 'model' => BorrowingRecord::class, 'icon' => 'SwitchHorizontalIcon', 'group' => 'logistik', 'sheet' => 'FORMULIR BUKU BESAR PEMINJAMAN '],
        'pengadaan-sarpras' => ['title' => 'FORMULIR PENGAJUAN PENGADAAN SARANA PRASARANA', 'model' => MaintenanceLog::class, 'type' => 'procurement', 'icon' => 'ShoppingCartIcon', 'group' => 'logistik', 'sheet' => 'FORMULIR PENGAJUAN PENGADAAN SA'],
        'analisis-kebutuhan' => ['title' => 'FORMULIR ANALISIS KEBUTUHAN SARANA PRASARANA', 'model' => MaintenanceLog::class, 'type' => 'analysis', 'icon' => 'PresentationChartLineIcon', 'group' => 'logistik', 'sheet' => 'FORMULIR ANALISIS KEBUTUHAN SAR'],
        'pemilihan-evaluasi' => ['title' => 'FORMULIR PEMILIHAN DAN EVALUASI SUPPLIER', 'model' => MaintenanceLog::class, 'type' => 'vendor-evaluation', 'icon' => 'UserGroupIcon', 'group' => 'logistik', 'sheet' => 'FORMULIR PEMILIHAN DAN EVALUASI'],
        'penerimaan-barang' => ['title' => 'FORMULIR PENERIMAAN BARANG', 'model' => MaintenanceLog::class, 'type' => 'receiving', 'icon' => 'DownloadIcon', 'group' => 'logistik', 'sheet' => 'FORMULIR PENERIMAAN BARANG'],
        'penyerahan-barang' => ['title' => 'FORMULIR PENYERAHAN BARANG', 'model' => MaintenanceLog::class, 'type' => 'handover', 'icon' => 'UploadIcon', 'group' => 'logistik', 'sheet' => 'FORMULIR PENYERAHAN BARANG'],
        'jadwal-token' => ['title' => 'FORMULIR JADWAL PENGISIAN TOKEN LISTRIK', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'category' => 'Token', 'icon' => 'KeyIcon', 'group' => 'logistik', 'sheet' => 'FORMULIR JADWAL PENGISIAN TOKEN'],

        // GROUP KEBERSIHAN
        'timeline-kebersihan' => ['title' => 'Timeline Pemeliharaan Kebersihan', 'model' => MaintenanceLog::class, 'type' => 'cleaning', 'icon' => 'ClockIcon', 'group' => 'kebersihan', 'sheet' => 'TIMELINE PEMELIHARAAN KEBERSIHA'],
        'jadwal-kebersihan' => ['title' => 'FORM MONITORING KEBERSIHAN PAH MATARAM', 'model' => MaintenanceLog::class, 'type' => 'detailed-monitoring', 'icon' => 'ClipboardCheckIcon', 'group' => 'kebersihan', 'sheet' => 'FORM MONITORING KEBERSIHAN DET'],
        'kelengkapan-alat' => ['title' => 'FORM KELENGKAPAN ALAT DAN BAHAN KEBERSIHAN', 'model' => MaintenanceLog::class, 'type' => 'inventory-check', 'icon' => 'ArchiveIcon', 'group' => 'kebersihan', 'sheet' => 'FORM KELENGKAPAN ALAT DAN BAHAN'],
        'pemeliharaan-kebersihan' => ['title' => 'TIMELINE PEMELIHARAAN KEBERSIHAN PAH MATARAM', 'model' => MaintenanceLog::class, 'type' => 'periodic', 'icon' => 'SparklesIcon', 'group' => 'kebersihan', 'sheet' => 'TIMELINE PEMELIHARAAN KEBERSIHA'],
        'monitoring-kebersihan' => ['title' => 'FORM PEMELIHARAAN KEBERSIHAN PAH MATARAM', 'model' => MaintenanceLog::class, 'type' => 'monitoring', 'icon' => 'EyeIcon', 'group' => 'kebersihan', 'sheet' => 'FORM MONITORING KEBERSIHAN PAH '],
        'kegiatan-pekanan' => ['title' => 'FORM KEGIATAN PEKANAN PETUGAS KEBERSIHAN PAH MATARAM', 'model' => MaintenanceLog::class, 'type' => 'weekly-activity', 'icon' => 'UserGroupIcon', 'group' => 'kebersihan', 'sheet' => 'FORM KEGIATAN PEKANAN PETUGAS'],
        'laporan-kebersihan' => ['title' => 'Laporan Pemeliharaan Kebersihan', 'model' => MaintenanceLog::class, 'type' => 'cleaning', 'icon' => 'DocumentReportIcon', 'group' => 'kebersihan', 'sheet' => 'FORM LAPORAN PEMELIHARAAN KEBERS'],

        // GROUP KENDARAAN & LAINNYA
        'parkir-area' => ['title' => 'Parkir Area PAH Mataram', 'model' => ParkingLog::class, 'icon' => 'MapPinIcon', 'group' => 'lainnya', 'sheet' => 'FORMULIR PARKIR AREA'], // Custom mapping
        'penggunaan-kendaraan' => ['title' => 'LOGBOOK PENGAJUAN PENGGUNAAN KENDARAAN PAH MATARAM', 'model' => VehicleRequest::class, 'type' => 'vehicle-log', 'icon' => 'TruckIcon', 'group' => 'lainnya', 'sheet' => 'LOGBOOK PENGAJUAN PENGGUNAAN KE'],
        'registrasi-kendaraan' => ['title' => 'Master Registrasi Kendaraan', 'model' => Vehicle::class, 'icon' => 'TruckIcon', 'group' => 'lainnya', 'sheet' => 'REGISTRASI KENDARAAN'],
        'ceklist-iso' => ['title' => 'Master Ceklist ISO', 'model' => IsoChecklist::class, 'icon' => 'ClipboardCheckIcon', 'group' => 'lainnya', 'sheet' => 'CEKLIST AUDIT INTERNAL ISO'],
    ];

    public function dashboard()
    {
        // Global Metrics
        $totalLogsCount = MaintenanceLog::count();
        $completedLogsCount = MaintenanceLog::where('status', 'completed')->orWhere('status', 'Selesai')->count(); // Support both EN/ID statuses
        $complianceRate = $totalLogsCount > 0 ? round(($completedLogsCount / $totalLogsCount) * 100) : 0;

        // Procedures Stats
        $procedureGroups = collect($this->procedures)->groupBy('group');

        // Distribution of Data across main models
        $distribution = [
            'Aset' => Item::count(),
            'Logistik' => BorrowingRecord::count(),
            'Sarpras' => MaintenanceLog::whereIn('type', ['maintenance', 'repair'])->count(),
            'Kebersihan' => MaintenanceLog::where('type', 'cleaning')->count(),
            'Lainnya' => VehicleRequest::count() + ParkingLog::count()
        ];

        $stats = [
            'total_procedures' => count($this->procedures),
            'compliance_rate' => $complianceRate,
            'active_checklists' => IsoChecklist::where('is_active', true)->count(),
            'total_logs' => $totalLogsCount,
            'recent_logs_list' => MaintenanceLog::with(['institution', 'room'])
                ->latest()
                ->take(8)
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'title' => $log->title ?: 'Pemeliharaan ' . $log->category,
                        'institution' => $log->institution,
                        'status' => $log->status,
                        'created_at' => $log->created_at->diffForHumans()
                    ];
                })
        ];

        // Group Counts (How many procedure modules exist per group)
        $groups = $procedureGroups->map(fn($g) => $g->count());

        // Trend Analysis (Last 7 Months)
        $months = [];
        $maintenanceTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->translatedFormat('M');
            $maintenanceTrend[] = MaintenanceLog::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // ISO Clauses Compliance Mapping (Dynamically linked to real data where possible)
        $clauses = [
            'Kl. 4: Konteks Organisasi' => 100, // Policy based
            'Kl. 5: Kepemimpinan' => 95,
            'Kl. 6: Perencanaan' => 90,
            'Kl. 7: Dukungan (Aset)' => Item::count() > 0 ? 100 : 0,
            'Kl. 8: Operasional (URT)' => $complianceRate,
            'Kl. 9: Evaluasi Kinerja' => MaintenanceLog::whereNotNull('completed_at')->count() > 0 ? 92 : 0,
            'Kl. 10: Peningkatan' => MaintenanceLog::where('type', 'project')->count() > 0 ? 88 : 0,
        ];

        $isoCompliance = collect($clauses)->map(fn($val, $clause) => [
            'sector' => $clause,
            'value' => $val
        ])->values();

        return Inertia::render('Admin/Procedures/Dashboard', [
            'stats' => $stats,
            'groups' => $groups,
            'distribution' => $distribution,
            'maintenanceTrend' => $maintenanceTrend,
            'isoCompliance' => $isoCompliance,
            'months' => $months
        ]);
    }

    public function index(Request $request)
    {
        $group = $request->get('group');

        $filteredProcedures = collect($this->procedures)
            ->filter(fn($p) => !$group || $p['group'] === $group)
            ->map(fn($p, $key) => array_merge($p, [
                'id' => $key,
                'url' => route('admin.procedures.show', $key)
            ]));

        return Inertia::render('Admin/Procedures/Index', [
            'procedures' => $filteredProcedures->values(),
            'currentGroup' => $group
        ]);
    }

    public function show($type)
    {
        if (!isset($this->procedures[$type]))
            abort(404);

        $procedure = $this->procedures[$type];
        $model = new $procedure['model'];

        $query = $procedure['model']::query();
        $tableName = $model->getTable();

        if (isset($procedure['type']) && Schema::hasColumn($tableName, 'type')) {
            $query->where('type', $procedure['type']);
        }

        if (isset($procedure['category']) && Schema::hasColumn($tableName, 'category')) {
            $query->where('category', $procedure['category']);
        }

        // Filter by Year (for specific types)
        if (Schema::hasColumn($tableName, 'period_year')) {
            $year = request('year', date('Y'));
            $query->where('period_year', $year);
        }

        // Filter by Location
        if (request('location') && Schema::hasColumn($tableName, 'location')) {
            $query->where('location', 'LIKE', '%' . request('location') . '%');
        }

        // Smartly load relationships based on model availability
        $availableRelations = ['institution', 'room', 'vehicle', 'performer', 'item', 'user'];
        $loadRelations = [];
        $modelClass = $procedure['model'];

        foreach ($availableRelations as $relation) {
            if (method_exists($modelClass, $relation)) {
                $loadRelations[] = $relation;
            }
        }

        $query->with($loadRelations);

        // Sort by ID asc for checklist items to keep order
        if ($type === 'pemeliharaan-listrik') {
            $query->orderBy('id', 'asc');
        } else {
            $query->latest();
        }

        $data = $query->paginate(20);

        // Additional data for forms
        $rooms = Room::all();
        $institutions = Institution::all();
        $items = Item::all();
        $users = User::all();
        $vehicles = Vehicle::all();

        return Inertia::render('Admin/Procedures/Show', [
            'type' => $type,
            'procedure' => $procedure,
            'data' => $data,
            'rooms' => $rooms,
            'institutions' => $institutions,
            'items' => $items,
            'users' => $users,
            'vehicles' => $vehicles,
            'filters' => request()->all() // Pass filters back
        ]);
    }

    public function store(Request $request, $type)
    {
        if (!isset($this->procedures[$type]))
            abort(404);
        $procedure = $this->procedures[$type];

        $data = $request->all();
        $model = new $procedure['model'];
        $tableName = $model->getTable();

        if (isset($procedure['type']) && Schema::hasColumn($tableName, 'type')) {
            $data['type'] = $procedure['type'];
        }

        if (isset($procedure['category']) && Schema::hasColumn($tableName, 'category')) {
            $data['category'] = $procedure['category'];
        }

        if (Schema::hasColumn($tableName, 'title') && empty($data['title'])) {
            $data['title'] = $procedure['title'];
        }

        if (Schema::hasColumn($tableName, 'period_year') && empty($data['period_year'])) {
            $data['period_year'] = date('Y');
        }

        if (Schema::hasColumn($tableName, 'description') && empty($data['description'])) {
            $data['description'] = 'Pencatatan data untuk ' . $procedure['title'];
        }

        // Mandatory Validation for Vehicle Requests
        if ($procedure['model'] === \App\Models\VehicleRequest::class) {
            if (empty($data['vehicle_id'])) {
                return back()->withErrors(['vehicle_id' => 'Kendaraan wajib dipilih.']);
            }
            if (empty($data['destination'])) {
                return back()->withErrors(['destination' => 'Tujuan wajib diisi.']);
            }
            if (empty($data['purpose'])) {
                return back()->withErrors(['purpose' => 'Keperluan wajib diisi.']);
            }
        }

        // Auto-fill or Validate Institution ID
        if (Schema::hasColumn($tableName, 'institution_id')) {
            if (empty($data['institution_id'])) {
                if (auth()->user()->institution_id) {
                    $data['institution_id'] = auth()->user()->institution_id;
                } else {
                    return back()->withErrors(['institution_id' => 'Lembaga wajib dipilih.']);
                }
            }
        }

        // Auto-fill User ID
        if (Schema::hasColumn($tableName, 'user_id') && empty($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        // Handle photo uploads
        if ($request->hasFile('photo_evidence')) {
            $data['photo_evidence'] = $request->file('photo_evidence')->store('reports', 'public');
        }
        if ($request->hasFile('photo_before')) {
            $data['photo_before'] = $request->file('photo_before')->store('reports', 'public');
        }
        if ($request->hasFile('photo_after')) {
            $data['photo_after'] = $request->file('photo_after')->store('reports', 'public');
        }

        $procedure['model']::create($data);

        return back()->with('success', 'Data prosedur berhasil ditambahkan.');
    }

    public function update(Request $request, $type, $id)
    {
        if (!isset($this->procedures[$type]))
            abort(404);
        $procedure = $this->procedures[$type];
        $model = $procedure['model']::findOrFail($id);

        $data = $request->all();

        // Handle photo uploads
        if ($request->hasFile('photo_evidence')) {
            if ($model->photo_evidence)
                Storage::disk('public')->delete($model->photo_evidence);
            $data['photo_evidence'] = $request->file('photo_evidence')->store('reports', 'public');
        }
        if ($request->hasFile('photo_before')) {
            if ($model->photo_before)
                Storage::disk('public')->delete($model->photo_before);
            $data['photo_before'] = $request->file('photo_before')->store('reports', 'public');
        }
        if ($request->hasFile('photo_after')) {
            if ($model->photo_after)
                Storage::disk('public')->delete($model->photo_after);
            $data['photo_after'] = $request->file('photo_after')->store('reports', 'public');
        }

        $model->update($data);

        return back()->with('success', 'Data prosedur berhasil diperbarui.');
    }

    public function destroy($type, $id)
    {
        if (!isset($this->procedures[$type]))
            abort(404);
        $procedure = $this->procedures[$type];
        $procedure['model']::find($id)->delete();
        return back()->with('success', 'Data prosedur berhasil dihapus.');
    }

    public function export($type)
    {
        if (!isset($this->procedures[$type]))
            abort(404);

        return \App\Exports\IsoProcedureExport::download($type, $this->procedures[$type]);
    }

    public function exportAll()
    {
        return \App\Exports\IsoProcedureExport::downloadAll();
    }

    public function checkImportHeaders(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'type' => 'required|string',
            'sheet' => 'nullable|string'
        ]);

        try {
            // Read the headers (first row of each sheet)
            $headings = (new HeadingRowImport)->toArray($request->file('file'));

            // Extract sheet names -> keys of the headings array are often sheet names or indices
            // If it's a single sheet, it might be [0 => [...]] or ['Sheet1' => [...]]
            // We normalize this.
            $sheets = [];
            foreach (array_keys($headings) as $key) {
                // If key is string, it's likely a sheet name. If int, we might need to assume 'Sheet'.
                // HeadingRowImport usually returns sheet names as keys if multiple.
                $sheets[] = (string) $key;
            }

            // Determine selected sheet
            $selectedSheet = $request->input('sheet');
            if (!$selectedSheet || !isset($headings[$selectedSheet])) {
                $selectedSheet = $sheets[0] ?? null;
            }

            $fileHeaders = [];
            if ($selectedSheet && isset($headings[$selectedSheet])) {
                // The import usually returns [ [row1], [row2] ] for each sheet? 
                // HeadingRowImport returns [ [ 'col1', 'col2' ] ] (wrapped in array)
                // Let's verify structure: $headings['Sheet1'][0] should be the array of column names
                $fileHeaders = $headings[$selectedSheet][0] ?? [];
            }

            // Normalize headers: flatten if necessary or filter nulls
            $fileHeaders = array_filter($fileHeaders);

            // Get required DB fields configuration
            $config = $this->getImportFields($request->type);

            return response()->json([
                'sheets' => $sheets,
                'selected_sheet' => $selectedSheet,
                'headers' => array_values($fileHeaders),
                'fields' => $config
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal membaca file: ' . $e->getMessage()], 422);
        }
    }

    public function import(Request $request, $type)
    {
        if (!isset($this->procedures[$type]))
            abort(404);
        $procedure = $this->procedures[$type];

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'mapping' => 'nullable|array',
            'sheet' => 'nullable|string'
        ]);

        $mapping = $request->input('mapping', []);
        $sheet = $request->input('sheet');
        $file = $request->file('file');

        $importer = null;
        if ($procedure['model'] === MaintenanceLog::class) {
            $importer = new \App\Imports\MaintenanceLogImport($procedure['type'] ?? null, $mapping);
        } elseif ($procedure['model'] === Item::class) {
            $importer = new \App\Imports\ItemImport($mapping);
        } else {
            $importer = new \App\Imports\GenericImport($procedure['model'], $mapping);
        }

        if ($sheet) {
            Excel::selectSheets($sheet)->import($importer, $file);
        } else {
            Excel::import($importer, $file);
        }

        return back()->with('success', "Data {$type} berhasil di-import.");
    }

    private function getImportFields($type)
    {
        // Default common fields
        if (!isset($this->procedures[$type]))
            return [];
        $procedure = $this->procedures[$type];
        $model = $procedure['model'];

        $fields = [];

        if ($model === MaintenanceLog::class) {
            $fields = [
                ['key' => 'judul', 'label' => 'Judul / Uraian', 'required' => true],
                ['key' => 'deskripsi', 'label' => 'Deskripsi / Keterangan', 'required' => false],
                ['key' => 'lokasi', 'label' => 'Lokasi / Ruangan', 'required' => false],
                ['key' => 'biaya', 'label' => 'Biaya (Rp)', 'required' => false],
                ['key' => 'status', 'label' => 'Status', 'required' => false],
                ['key' => 'jadwal', 'label' => 'Tanggal Jadwal', 'required' => false],
                ['key' => 'petugas', 'label' => 'Petugas', 'required' => false],
            ];
            // Add specialized fields based on type
            if ($type === 'pemeliharaan-ac') {
                $fields[] = ['key' => 'ac_pk', 'label' => 'PK (AC)', 'required' => false];
            }
        } elseif ($model === Item::class) {
            $fields = [
                ['key' => 'code', 'label' => 'Kode Barang', 'required' => false],
                ['key' => 'brand', 'label' => 'Merk', 'required' => false],
                ['key' => 'name', 'label' => 'Nama Barang', 'required' => true],
                ['key' => 'stock', 'label' => 'Jumlah Stock', 'required' => true],
                ['key' => 'unit', 'label' => 'Satuan', 'required' => true],
                ['key' => 'condition', 'label' => 'Kondisi (B/KB/RB)', 'required' => false],
                ['key' => 'location', 'label' => 'Lokasi / Ruangan', 'required' => false],
                ['key' => 'institution_code', 'label' => 'Kode Lembaga', 'required' => false],
                ['key' => 'purchase_date', 'label' => 'Tanggal Pembelian', 'required' => false],
                ['key' => 'price', 'label' => 'Harga Perolehan', 'required' => false],
                ['key' => 'source', 'label' => 'Sumber Dana', 'required' => false],
                ['key' => 'specification', 'label' => 'Spesifikasi', 'required' => false],
            ];
        } elseif ($model === VehicleRequest::class) {
            $fields = [
                ['key' => 'vehicle_plate', 'label' => 'Plat Nomor', 'required' => true],
                ['key' => 'start_time', 'label' => 'Waktu Mulai', 'required' => true],
                ['key' => 'end_time', 'label' => 'Waktu Selesai', 'required' => true],
                ['key' => 'destination', 'label' => 'Tujuan', 'required' => true],
                ['key' => 'purpose', 'label' => 'Keperluan', 'required' => true],
            ];
        } elseif ($model === BorrowingRecord::class) {
            $fields = [
                ['key' => 'item_code', 'label' => 'Kode Barang', 'required' => true],
                ['key' => 'borrow_date', 'label' => 'Tanggal Pinjam', 'required' => true],
                ['key' => 'return_date', 'label' => 'Tanggal Kembali', 'required' => false],
                ['key' => 'borrower_name', 'label' => 'Peminjam', 'required' => true],
                ['key' => 'status', 'label' => 'Status', 'required' => false],
            ];
        }

        return $fields;
    }

    public function downloadTemplate($type)
    {
        if (!isset($this->procedures[$type]))
            abort(404);

        // Generate simple headers array from getImportFields
        $fields = $this->getImportFields($type);
        $headers = array_map(fn($f) => $f['key'], $fields);

        // Fallback if empty (shouldn't differ much from previous logic, but safer)
        if (empty($headers)) {
            $headers = ['title', 'description', 'status'];
        }

        return Excel::download(
            new class ($headers) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $h;
            public function __construct($h)
            {
                $this->h = $h; }
            public function array(): array
            {
                return [$this->h]; }
            },
            "template-{$type}.xlsx"
        );
    }
}
