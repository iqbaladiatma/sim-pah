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
use Maatwebsite\Excel\Facades\Excel;

class UrtProcedureController extends Controller
{
    protected $procedures = [
        // ASET GROUP
        'pendataan-aset' => ['title' => 'Pendataan Aset PAH Mataram', 'model' => Item::class, 'icon' => 'LibraryIcon', 'group' => 'aset'],
        'pelelangan-aset' => ['title' => 'Pelelangan Aset', 'model' => AssetLifecycleLog::class, 'type' => 'auction', 'icon' => 'CashIcon', 'group' => 'aset'],
        'penghapusan-aset' => ['title' => 'Penghapusan Aset', 'model' => AssetLifecycleLog::class, 'type' => 'disposal', 'icon' => 'TrashIcon', 'group' => 'aset'],
        'pengadaan' => ['title' => 'Pengadaan Sarana Prasarana', 'model' => Item::class, 'icon' => 'ShoppingCartIcon', 'group' => 'aset'],

        // SARPRAS GROUP
        'pemeliharaan-sarpras' => ['title' => 'Pemeliharaan Sarpras', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'icon' => 'ToolsIcon', 'group' => 'sarpras'],
        'perbaikan-sarpras' => ['title' => 'Perbaikan Sarpras', 'model' => MaintenanceLog::class, 'type' => 'repair', 'icon' => 'ExclamationIcon', 'group' => 'sarpras'],
        'proyek-sarpras' => ['title' => 'Proyek Kegiatan Sarpras', 'model' => MaintenanceLog::class, 'type' => 'project', 'icon' => 'OfficeBuildingIcon', 'group' => 'sarpras'],
        'listrik-padam' => ['title' => 'Penanganan Listrik Padam', 'model' => MaintenanceLog::class, 'type' => 'power_outage', 'icon' => 'LightningBoltIcon', 'group' => 'sarpras'],
        'kebersihan' => ['title' => 'Pemeliharaan Kebersihan', 'model' => MaintenanceLog::class, 'type' => 'cleaning', 'icon' => 'SparklesIcon', 'group' => 'sarpras'],
        'pertamanan' => ['title' => 'Pemeliharaan Pertamanan', 'model' => MaintenanceLog::class, 'type' => 'garden', 'icon' => 'SunIcon', 'group' => 'sarpras'],

        // KENDARAAN GROUP
        'registrasi-kendaraan' => ['title' => 'Registrasi Kendaraan', 'model' => Vehicle::class, 'icon' => 'IdentificationIcon', 'group' => 'kendaraan'],
        'pengajuan-kendaraan' => ['title' => 'Pengajuan Kendaraan', 'model' => VehicleRequest::class, 'icon' => 'TruckIcon', 'group' => 'kendaraan'],
        'perawatan-kendaraan' => ['title' => 'Perawatan Kendaraan', 'model' => MaintenanceLog::class, 'type' => 'vehicle', 'icon' => 'ChipIcon', 'group' => 'kendaraan'],
        'parkir-area' => ['title' => 'Parkir Area PAH Mataram', 'model' => ParkingLog::class, 'icon' => 'MapPinIcon', 'group' => 'kendaraan'],

        // LOGISTIK GROUP
        'peminjaman-barang' => ['title' => 'Peminjaman Barang URT', 'model' => BorrowingRecord::class, 'icon' => 'SwitchHorizontalIcon', 'group' => 'logistik'],

        // ISO GROUP (Audit)
        'ceklist-iso' => ['title' => 'Ceklist Prosedur ISO URT', 'model' => IsoChecklist::class, 'icon' => 'ClipboardCheckIcon', 'group' => 'iso'],
    ];

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

        if (isset($procedure['type'])) {
            $query->where('type', $procedure['type']);
        }

        $data = $query->latest()->paginate(10);

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
            'vehicles' => $vehicles
        ]);
    }

    public function store(Request $request, $type)
    {
        if (!isset($this->procedures[$type]))
            abort(404);
        $procedure = $this->procedures[$type];

        $data = $request->all();
        if (isset($procedure['type'])) {
            $data['type'] = $procedure['type'];
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

    public function update(Request $request, $id)
    {
        // This is a generic update, we need to know the type or find the model
        // For simplicity, we'll pass the model type in the request or use a mapping if needed.
        // But for now, let's assume we handle it via the Show page's specific update logic.
        return back()->with('error', 'Update logic requires specific model handling.');
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
        $procedure = $this->procedures[$type];

        if ($procedure['model'] === MaintenanceLog::class) {
            return Excel::download(new \App\Exports\MaintenanceLogExport($procedure['type'] ?? null), "prosedur-{$type}.xlsx");
        }

        if ($procedure['model'] === Vehicle::class) {
            return Excel::download(new \App\Exports\VehicleExport(), "pendaftaran-kendaraan.xlsx");
        }

        return back()->with('error', 'Fitur Export Excel untuk ' . $type . ' belum tersedia.');
    }

    public function import(Request $request, $type)
    {
        if (!isset($this->procedures[$type]))
            abort(404);
        $procedure = $this->procedures[$type];

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        if ($procedure['model'] === MaintenanceLog::class) {
            Excel::import(new \App\Imports\MaintenanceLogImport($procedure['type'] ?? null), $request->file('file'));
            return back()->with('success', "Data prosedur {$type} berhasil di-import.");
        }

        return back()->with('error', 'Fitur Import Excel untuk ' . $type . ' belum tersedia.');
    }

    public function downloadTemplate($type)
    {
        if (!isset($this->procedures[$type]))
            abort(404);
        $procedure = $this->procedures[$type];

        $headers = [];
        if ($procedure['model'] === MaintenanceLog::class) {
            $headers = ['judul', 'deskripsi', 'lokasi', 'biaya', 'status', 'jadwal'];
        } else {
            return back()->with('error', 'Template untuk ' . $type . ' belum tersedia.');
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
