<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        return view('instructor.profile-sidebar', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'dob'        => ['nullable', 'date'],
            'gender'     => ['nullable', 'in:Pria,Wanita,Other'],
            'nim'        => ['nullable', 'string', 'max:50'],
            'profesi'    => ['nullable', 'string', 'max:100'],
            'avatar'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user->first_name = $validated['first_name'];
        $user->last_name  = $validated['last_name'];
        $user->name       = trim($validated['first_name'] . ' ' . $validated['last_name']);
        $user->phone      = $validated['phone'] ?? null;
        $user->dob        = $validated['dob'] ?? null;
        $user->gender     = $validated['gender'] ?? null;
        $user->profesi    = $validated['profesi'] ?? null;

        if (array_key_exists('nim', $validated)) {
            $user->nim = $validated['nim'];
        }

        if ($request->hasFile('avatar')) {
            $storage = app(SupabaseStorageService::class);

            if ($user->avatar_path) {
                $storage->delete($user->avatar_path);
                if (!str_starts_with($user->avatar_path, 'http') && Storage::disk('public')->exists($user->avatar_path)) {
                    Storage::disk('public')->delete($user->avatar_path);
                }
            }

            $upload = $storage->upload($request->file('avatar'), 'profile_photos');
            $user->avatar_path = $upload['public_url'] ?? $upload['path'];
        }

        $user->save();
        Auth::setUser($user);

        Notification::create([
            'user_id' => $user->id,
            'title'   => 'Profil Berhasil Diperbarui',
            'message' => 'Perubahan profil instruktur telah disimpan.',
            'type'    => 'success',
        ]);

        return back()->with('success', 'Profil instruktur berhasil diperbarui!');
    }
}
