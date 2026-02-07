<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::with('institution')->latest()->paginate(100),
            'stats' => [
                'total' => User::count(),
                'admins' => User::whereIn('role', ['admin', 'super admin'])->count(),
                'lembaga' => User::where('role', 'lembaga')->count(),
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'institutions' => Institution::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:admin,super admin,lembaga',
            'institution_id' => 'nullable|exists:institutions,id',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'institution_id' => $validated['role'] === 'lembaga' ? $validated['institution_id'] : null,
            'password' => Hash::make($validated['password']),
            'is_super_admin' => false, // New users are not super admin
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        // Prevent editing super admin
        if ($user->is_super_admin) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Super Admin tidak dapat diubah.');
        }

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user->load('institution'),
            'institutions' => Institution::all(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        // Prevent updating super admin
        if ($user->is_super_admin) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Super Admin tidak dapat diubah.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,super admin,lembaga',
            'institution_id' => 'nullable|exists:institutions,id',
            'password' => 'nullable|string|min:8',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'institution_id' => $validated['role'] === 'lembaga' ? $validated['institution_id'] : null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting super admin
        if ($user->is_super_admin) {
            return back()->with('error', 'Super Admin tidak dapat dihapus.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}
