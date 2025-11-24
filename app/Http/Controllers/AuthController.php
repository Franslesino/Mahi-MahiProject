<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Tampilkan halaman register
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses login manual
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            return $this->redirectBasedOnRole();
        }

        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    /**
     * Proses register manual
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:15',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'student',
        ]);

        // Create notification for new account
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Akun Berhasil Dibuat',
            'message' => 'Selamat datang di Mahi-Mahi! Akun Anda telah berhasil dibuat. Silakan login untuk mulai belajar.',
            'type' => 'success',
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Registrasi berhasil, silakan login.');
    }

    /**
     * Redirect ke Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google
     */
    public function handleGoogleCallback()
    {
        try {
            // Dapatkan user dari Google
            $googleUser = Socialite::driver('google')->user();
            
            // Cek apakah user sudah pernah login dengan Google ID ini
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // User sudah ada dengan Google ID, langsung login
                Auth::login($user, true);
                return $this->redirectBasedOnRole();
            }

            // Cek apakah email sudah terdaftar
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                // Email sudah ada, link akun Google ke user yang ada
                $existingUser->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                ]);

                Auth::login($existingUser, true);
                return $this->redirectBasedOnRole();
            }

            // User baru, buat akun baru
            $newUser = User::create([
                'name'      => $googleUser->getName(),
                'email'     => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
                'role'      => 'student',
                'password'  => null, // Password null untuk OAuth user
                'email_verified_at' => now(), // Email sudah terverifikasi via Google
            ]);

            // Create notification for new account via Google
            Notification::create([
                'user_id' => $newUser->id,
                'title' => 'Akun Berhasil Dibuat',
                'message' => 'Selamat datang di Mahi-Mahi! Akun Anda telah berhasil dibuat melalui Google. Silakan mulai belajar.',
                'type' => 'success',
            ]);

            Auth::login($newUser, true);
            return $this->redirectBasedOnRole();

        } catch (Exception $e) {
            return redirect()
                ->route('login')
                ->withErrors(['error' => 'Gagal login dengan Google. Silakan coba lagi.']);
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logout berhasil');
    }

    /**
     * Helper: Redirect berdasarkan role
     */
    private function redirectBasedOnRole()
    {
        $user = Auth::user();

        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'instructor' => redirect()->route('instructor.dashboard'),
            default => redirect()->route('home')
        };
    }
}