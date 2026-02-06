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
        // GROUP ASET
        'pendataan-aset' => ['title' => 'Pendataan Aset PAH Mataram', 'model' => Item::class, 'icon' => 'LibraryIcon', 'group' => 'aset', 'sheet' => 'FORMULIR LAPORAN ASET PAH MATAR'],
        'buku-induk' => ['title' => 'Buku Induk Inventaris Barang', 'model' => Item::class, 'icon' => 'BookOpenIcon', 'group' => 'aset', 'sheet' => 'BUKU INDUK INVENTARIS BARANG'],
        'kir-ruangan' => ['title' => 'Kartu Inventaris Ruangan (KIR)', 'model' => Item::class, 'icon' => 'HomeIcon', 'group' => 'aset', 'sheet' => 'KARTU INVENTARIS RUANGAN (KIR)'],
        'monitoring-aset' => ['title' => 'Monitoring Aset', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'category' => 'Aset', 'icon' => 'DesktopComputerIcon', 'group' => 'aset', 'sheet' => 'FORMULIR MONITORING ASET'],
        'pelelangan-aset' => ['title' => 'Daftar Aset Dilelang', 'model' => AssetLifecycleLog::class, 'type' => 'auction', 'icon' => 'CashIcon', 'group' => 'aset', 'sheet' => ' DAFTAR ASET YANG AKAN DILELANG'],
        'penjualan-bekas' => ['title' => 'Penjualan Barang Bekas', 'model' => AssetLifecycleLog::class, 'type' => 'disposal', 'icon' => 'TagIcon', 'group' => 'aset', 'sheet' => 'FORMULIR PENJUALAN BARANG BEKAS'],
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
        'pemeliharaan-listrik' => ['title' => 'Pemeliharaan Jaringan Listrik', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'category' => 'Listrik', 'icon' => 'LightningBoltIcon', 'group' => 'sarpras', 'sheet' => 'FORM PEMELIHARAAN JARINGAN LIST'],

        // GROUP PERBAIKAN & PROYEK
        'agenda-perbaikan' => ['title' => 'JADWAL AGENDA PERBAIKAN SARPRAS', 'model' => MaintenanceLog::class, 'type' => 'repair', 'icon' => 'CalendarIcon', 'group' => 'proyek', 'sheet' => 'JADWAL AGENDA PERBAIKAN SARPRAS'],
        'rekapan-pengajuan' => ['title' => 'Rekapan Pengajuan Perbaikan', 'model' => MaintenanceLog::class, 'type' => 'repair', 'icon' => 'CollectionIcon', 'group' => 'proyek', 'sheet' => 'REKAPAN PENGAJUAN PERBAIKAN SAR'],
        'pengajuan-rab' => ['title' => 'Pengajuan RAB', 'model' => MaintenanceLog::class, 'type' => 'project', 'icon' => 'DocumentTextIcon', 'group' => 'proyek', 'sheet' => 'FORMULIR PENGAJUAN RAB'],
        'laporan-proyek' => ['title' => 'Laporan Proyek Kegiatan', 'model' => MaintenanceLog::class, 'type' => 'project', 'icon' => 'CubeIcon', 'group' => 'proyek', 'sheet' => 'LAPORAN PROYEK KEGIATAN'],

        // GROUP LOGISTIK & PENGADAAN
        'peminjaman-barang' => ['title' => 'Buku Besar Peminjaman Barang', 'model' => BorrowingRecord::class, 'icon' => 'SwitchHorizontalIcon', 'group' => 'logistik', 'sheet' => 'FORMULIR BUKU BESAR PEMINJAMAN '],
        'pengadaan-sarpras' => ['title' => 'Pengajuan Pengadaan Sarpras', 'model' => Item::class, 'icon' => 'ShoppingCartIcon', 'group' => 'logistik', 'sheet' => 'FORMULIR PENGAJUAN PENGADAAN SA'],
        'analisis-kebutuhan' => ['title' => 'Analisis Kebutuhan Sarpras', 'model' => Item::class, 'icon' => 'PresentationChartLineIcon', 'group' => 'logistik', 'sheet' => 'FORMULIR ANALISIS KEBUTUHAN SAR'],
        'pemilihan-evaluasi' => ['title' => 'Pemilihan & Evaluasi Vendor', 'model' => User::class, 'icon' => 'UserGroupIcon', 'group' => 'logistik', 'sheet' => 'FORMULIR PEMILIHAN DAN EVALUASI'],
        'penerimaan-barang' => ['title' => 'Penerimaan Barang', 'model' => Item::class, 'icon' => 'DownloadIcon', 'group' => 'logistik', 'sheet' => 'FORMULIR PENERIMAAN BARANG'],
        'penyerahan-barang' => ['title' => 'Penyerahan Barang', 'model' => Item::class, 'icon' => 'UploadIcon', 'group' => 'logistik', 'sheet' => 'FORMULIR PENYERAHAN BARANG'],
        'jadwal-token' => ['title' => 'Jadwal Pengisian Token', 'model' => MaintenanceLog::class, 'type' => 'maintenance', 'category' => 'Token', 'icon' => 'KeyIcon', 'group' => 'logistik', 'sheet' => 'FORMULIR JADWAL PENGISIAN TOKEN'],

        // GROUP KEBERSIHAN
        'timeline-kebersihan' => ['title' => 'Timeline Pemeliharaan Kebersihan', 'model' => MaintenanceLog::class, 'type' => 'cleaning', 'icon' => 'ClockIcon', 'group' => 'kebersihan', 'sheet' => 'TIMELINE PEMELIHARAAN KEBERSIHA'],
        'jadwal-kebersihan' => ['title' => 'Jadwal Pemeliharaan Kebersihan', 'model' => MaintenanceLog::class, 'type' => 'cleaning', 'icon' => 'CalendarIcon', 'group' => 'kebersihan', 'sheet' => 'FORM JADWAL PEMELIHARAAN KEBERS'],
        'kelengkapan-alat' => ['title' => 'Kelengkapan Alat & Bahan', 'model' => Item::class, 'icon' => 'ArchiveIcon', 'group' => 'kebersihan', 'sheet' => 'FORM KELENGKAPAN ALAT DAN BAHAN'],
        'pemeliharaan-kebersihan' => ['title' => 'Pemeliharaan Kebersihan PAH', 'model' => MaintenanceLog::class, 'type' => 'cleaning', 'icon' => 'SparklesIcon', 'group' => 'kebersihan', 'sheet' => 'FORM PEMELIHARAAN KEBERSIHAN PA'],
        'monitoring-kebersihan' => ['title' => 'Monitoring Kebersihan PAH', 'model' => MaintenanceLog::class, 'type' => 'cleaning', 'icon' => 'EyeIcon', 'group' => 'kebersihan', 'sheet' => 'FORM MONITORING KEBERSIHAN PAH '],
        'kegiatan-pekanan' => ['title' => 'Kegiatan Pekanan Petugas', 'model' => MaintenanceLog::class, 'type' => 'cleaning', 'icon' => 'UserGroupIcon', 'group' => 'kebersihan', 'sheet' => 'FORM KEGIATAN PEKANAN PETUGAS K'],
        'laporan-kebersihan' => ['title' => 'Laporan Pemeliharaan Kebersihan', 'model' => MaintenanceLog::class, 'type' => 'cleaning', 'icon' => 'DocumentReportIcon', 'group' => 'kebersihan', 'sheet' => 'FORM LAPORAN PEMELIHARAAN KEBERS'],

        // GROUP KENDARAAN & LAINNYA
        'parkir-area' => ['title' => 'Parkir Area PAH Mataram', 'model' => ParkingLog::class, 'icon' => 'MapPinIcon', 'group' => 'lainnya', 'sheet' => 'FORMULIR PARKIR AREA'], // Custom mapping
        'penggunaan-kendaraan' => ['title' => 'Logbook Penggunaan Kendaraan', 'model' => VehicleRequest::class, 'icon' => 'TruckIcon', 'group' => 'lainnya', 'sheet' => 'LOGBOOK PENGAJUAN PENGGUNAAN KE'],
        'registrasi-kendaraan' => ['title' => 'Master Registrasi Kendaraan', 'model' => Vehicle::class, 'icon' => 'TruckIcon', 'group' => 'lainnya', 'sheet' => 'REGISTRASI KENDARAAN'],
        'ceklist-iso' => ['title' => 'Master Ceklist ISO', 'model' => IsoChecklist::class, 'icon' => 'ClipboardCheckIcon', 'group' => 'lainnya', 'sheet' => 'CEKLIST AUDIT INTERNAL ISO'],
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

        if (isset($procedure['category'])) {
            $query->where('category', $procedure['category']);
        }

        $data = $query->with(['institution', 'room', 'vehicle', 'performer'])->latest()->paginate(20);

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

        if (isset($procedure['category'])) {
            $data['category'] = $procedure['category'];
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

        if ($procedure['model'] === Item::class) {
            Excel::import(new \App\Imports\ItemImport(), $request->file('file'));
            return back()->with('success', "Data aset {$type} berhasil di-import.");
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
