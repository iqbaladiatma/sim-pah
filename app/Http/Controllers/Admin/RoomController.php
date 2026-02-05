<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Institution;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RoomImport;

class RoomController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Rooms/Index', [
            'rooms' => Room::with('institution')->latest()->paginate(50),
            'institutions' => Institution::all(),
        ]);
    }

    public function create()
    {
        $institutions = Institution::all();
        return Inertia::render('Admin/Rooms/Create', [
            'institutions' => $institutions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Room::create($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Ruangan berhasil dibuat.');
    }

    public function edit(Room $room)
    {
        $institutions = Institution::all();
        return Inertia::render('Admin/Rooms/Edit', [
            'room' => $room,
            'institutions' => $institutions
        ]);
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $room->update($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->back()->with('success', 'Ruangan berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new RoomImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data ruangan berhasil diimpor.');
    }

    public function downloadTemplate()
    {
        $headers = ['institution_code', 'name', 'description'];
        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['SMA-AH', 'Lab Komputer 1', 'Lantai 2 Gedung Utama']);
            fputcsv($file, ['SMA-AH', 'Kantor TU', 'Lantai 1']);
            fclose($file);
        };

        return response()->streamDownload($callback, 'template_ruangan.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function getByInstitution($institutionId)
    {
        $rooms = Room::where('institution_id', $institutionId)->where('is_active', true)->get();
        return response()->json($rooms);
    }
}
