<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Tampilkan form edit profil user yang sedang login
     */
    public function edit()
    {
        $user = Auth::user();

        return view('profile', compact('user'));
    }

    /**
     * Update data profil (termasuk upload foto avatar)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'dob'        => ['nullable', 'date'],
            'gender'     => ['nullable', 'in:Pria,Wanita,Other'],
            'nim'        => ['nullable', 'string', 'max:50'],
            'avatar'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Update field teks
        $user->first_name = $validated['first_name'];
        $user->last_name  = $validated['last_name'];
        $user->name       = trim($validated['first_name'].' '.$validated['last_name']);

        $user->phone = $validated['phone'] ?? null;
        $user->dob   = $validated['dob'] ?? null;
        $user->gender = $validated['gender'] ?? null;

        if (array_key_exists('nim', $validated)) {
            $user->nim = $validated['nim'];
        }

        /**
         * Upload Avatar (Foto Profil)
         */
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            // Buat nama file unik: user_ID_timestamp.ext
            $ext = $request->file('avatar')->getClientOriginalExtension();
            $filename = 'user_' . $user->id . '_' . time() . '.' . $ext;

            // Simpan file ke storage/app/public/profile_photos
            $path = $request->file('avatar')->storeAs('profile_photos', $filename, 'public');

            // Simpan path ke database
            $user->avatar_path = $path;
        }

        // Simpan perubahan ke database
        $user->save();

        // ✅ Refresh session user agar sidebar & navbar pakai data terbaru
        Auth::setUser($user);

        // Create notification for successful profile update
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Profil Berhasil Diperbarui',
            'message' => 'Perubahan profil Anda telah berhasil disimpan.',
            'type' => 'success',
        ]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
