<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function index()
    {
        if (!in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Akses ditolak.');
        }

        $pendingUsers = User::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $users = User::where('status', '!=', 'pending')
            ->with('approvedBy')
            // 1. Urutkan Role ASC ('admin' huruf A, akan muncul sebelum 'user' huruf U)
            ->orderBy('role', 'asc') 
            // 2. Kemudian urutkan Nama ASC (A ke Z)
            ->orderBy('name', 'asc') 
            ->get();

        return view('pages.admin.management.index', compact('users', 'pendingUsers'));
    }

    public function create()
    {
        return view('pages.admin.management.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
        ]);
        User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'avatar_url' => 'images/avatar-default.svg',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);
        return redirect()->route('user.management')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('pages.admin.management.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|unique:users,username,' . $user->id,
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required',
        ]);
        $data = $request->only('username', 'name', 'email', 'role');
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        return redirect()->route('user.management')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.management')->with('success', 'User berhasil dihapus.');
    }

    public function approve(User $user)
    {
        if (!in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Akses ditolak.');
        }
        if ($user->status !== 'pending') {
            return redirect()->back()->with('error', 'User tidak dalam status pending.');
        }

        $user->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', "Akun {$user->username} berhasil disetujui.");
    }

    public function reject(User $user)
    {
        if (!in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Akses ditolak.');
        }
        if ($user->status !== 'pending') {
            return redirect()->back()->with('error', 'User tidak dalam status pending.');
        }

        $user->update([
            'status' => 'rejected',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', "Akun {$user->username} berhasil ditolak.");
    }
}
