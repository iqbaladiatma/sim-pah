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
    public function index(Request $request)
    {
        $totalRooms = Room::count();
        $latestRoom = Room::latest()->first();

        if ($request->has('institution_id') && $request->institution_id) {
            $institution = Institution::findOrFail($request->institution_id);
            $query = Room::with('institution')->where('institution_id', $institution->id);

            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            }

            return Inertia::render('Admin/Rooms/Index', [
                'mode' => 'list',
                'institution' => $institution,
                'rooms' => $query->latest()->paginate(50)->withQueryString(),
                'filters' => $request->only(['search', 'institution_id']),
                'stats' => [
                    'total' => Room::where('institution_id', $institution->id)->count(),
                    'latest' => Room::where('institution_id', $institution->id)->latest()->first()->name ?? '-',
                ],
            ]);
        } else {
            $query = Institution::withCount('rooms');

            if ($request->search) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            }

            return Inertia::render('Admin/Rooms/Index', [
                'mode' => 'folders',
                'institutions' => $query->get(),
                'filters' => $request->only(['search']),
                'stats' => [
                    'total' => $totalRooms,
                    'latest' => $latestRoom ? $latestRoom->name : '-',
                ],
            ]);
        }
    }

    public function showImport()
    {
        return Inertia::render('Admin/Rooms/Import');
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

        $countBefore = Room::count();

        Excel::import(new RoomImport, $request->file('file'));

        $countAfter = Room::count();
        $importedCount = $countAfter - $countBefore;

        // Log activity dengan detail import
        activity()
            ->causedBy(auth()->user())
            ->performedOn(new Room())
            ->withProperties([
                'action' => 'import',
                'total_imported' => $importedCount,
                'total_before' => $countBefore,
                'total_after' => $countAfter,
                'file_name' => $request->file('file')->getClientOriginalName(),
            ])
            ->log("Import {$importedCount} ruangan baru dari file CSV");

        return redirect()->route('admin.rooms.index')->with('success', "Berhasil mengimpor {$importedCount} ruangan baru.");
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
