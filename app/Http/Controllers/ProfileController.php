<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('pages.general.profile.profile');
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'job_position' => 'nullable|string|max:255',
        ]);
        $user->update($request->only('name', 'username', 'email', 'job_position'));
        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updateAvatar(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Hapus avatar lama jika bukan default
        if ($user->avatar_url && $user->avatar_url !== 'images/avatar-default.svg') {
            $oldPath = public_path('storage/' . $user->avatar_url);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        // Simpan avatar baru ke images/avatar/
        $avatarName = uniqid() . '.' . $request->avatar->extension();
        $request->avatar->move(public_path('storage/images/avatar'), $avatarName);

        $user->avatar_url = 'images/avatar/' . $avatarName;
        $user->save();

        return back()->with('success', 'Avatar berhasil diperbarui.');
    }

    public function editPassword()
    {
        return view('pages.general.profile.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah.']);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect()->route('profile.index')->with('success', 'Password berhasil diubah.');
    }
}