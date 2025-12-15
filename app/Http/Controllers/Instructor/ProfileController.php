<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\SupabaseStorageService;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Tampilkan form edit profil instruktur
     */
    public function edit()
    {
        $user = Auth::user();
        return view('instructor.profile.edit', compact('user'));
    }

    /**
     * Update data profil instruktur (avatar, nama, password)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'avatar'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password'   => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Update nama
        $user->first_name = $validated['first_name'];
        $user->last_name  = $validated['last_name'];
        $user->name       = trim($validated['first_name'] . ' ' . $validated['last_name']);

        /**
         * Upload Avatar (Foto Profil)
         */
        if ($request->hasFile('avatar')) {
            $storage = app(SupabaseStorageService::class);

            // Hapus avatar lama jika ada
            if ($user->avatar_path) {
                $storage->delete($user->avatar_path);
                if (!str_starts_with($user->avatar_path, 'http') && Storage::disk('public')->exists($user->avatar_path)) {
                    Storage::disk('public')->delete($user->avatar_path);
                }
            }

            $upload = $storage->upload($request->file('avatar'), 'profile_photos');

            // Simpan path/public URL ke database
            $user->avatar_path = $upload['public_url'] ?? $upload['path'];
        }

        /**
         * Update Password (jika diisi)
         * Password hanya diubah jika field tidak kosong
         */
        if (!empty($validated['password'])) {
            $user->password = $validated['password']; // Laravel's password cast akan hash otomatis
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
