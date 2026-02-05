<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = \App\Models\User::with('institution')->latest()->paginate(10);
        $institutions = \App\Models\Institution::all();

        return inertia('Admin/Users/Index', [
            'users' => $users,
            'institutions' => $institutions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:super admin,admin,lembaga',
            'institution_id' => 'nullable|exists:institutions,id',
            'phone' => 'nullable|string',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        \App\Models\User::create($validated);

        return redirect()->back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, \App\Models\User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:super admin,admin,lembaga',
            'institution_id' => 'nullable|exists:institutions,id',
            'phone' => 'nullable|string',
            'password' => 'nullable|string|min:8',
        ]);

        if (isset($validated['password']) && $validated['password']) {
            $validated['password'] = bcrypt($validated['password']);
        }
        else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(\App\Models\User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}
