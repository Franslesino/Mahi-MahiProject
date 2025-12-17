@extends('layouts.instructor')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;

    /** @var \App\Models\User $user */
    $user = isset($user) ? $user : Auth::user();
@endphp

<div class="p-8">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('instructor.dashboard') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm mb-4 inline-flex items-center gap-1">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
        <h2 class="text-3xl font-bold text-gray-800 mt-4">Edit Profil Instruktur</h2>
        <p class="text-gray-600 mt-2">Kelola informasi profil Anda</p>
    </div>

    {{-- Error messages --}}
    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-xl mt-0.5"></i>
                <div>
                    <h4 class="font-semibold mb-2">Terdapat kesalahan:</h4>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Flash success --}}
    @if (session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-xl"></i>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm p-6 max-w-2xl">
        <form method="POST" action="{{ route('instructor.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- Avatar Section --}}
                <div class="pb-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Foto Profil</h3>
                    <div class="flex items-center gap-6">
                        <div class="relative">
                            <img
                                id="avatarPreview"
                                src="{{ $user->avatar_url
                                        ? $user->avatar_url
                                        : 'data:image/svg+xml;utf8,'.rawurlencode(
                                            '<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'96\' height=\'96\'>
                                                <rect width=\'100%\' height=\'100%\' rx=\'48\' fill=\'#3b82f6\'/>
                                                <text x=\'50%\' y=\'56%\' font-size=\'40\' text-anchor=\'middle\' fill=\'white\' font-family=\'Arial, Helvetica, sans-serif\'>'
                                                . e(strtoupper(substr($user->first_name ?: $user->name, 0, 1)))
                                                . '</text>
                                            </svg>'
                                        )
                                    }}"
                                alt="Avatar"
                                class="w-24 h-24 rounded-full object-cover ring-4 ring-blue-500/20 bg-gray-100"
                            >
                        </div>

                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ubah Foto Profil</label>
                            <input
                                type="file"
                                name="avatar"
                                id="avatarInput"
                                accept="image/*"
                                class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-blue-700 cursor-pointer"
                            >
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Format: JPG, PNG, WEBP • Maksimal 2MB
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Personal Information --}}
                <div class="pb-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pribadi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- First Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Depan</label>
                            <input
                                type="text"
                                name="first_name"
                                value="{{ old('first_name', $user->first_name) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                                required
                            >
                            @error('first_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Last Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Belakang</label>
                            <input
                                type="text"
                                name="last_name"
                                value="{{ old('last_name', $user->last_name) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                                required
                            >
                            @error('last_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Email (read-only) --}}
                <div class="pb-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Akun</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-xs text-gray-500">(tidak bisa diubah)</span></label>
                        <input
                            type="email"
                            value="{{ $user->email }}"
                            class="w-full px-4 py-3 border border-gray-200 bg-gray-100 text-gray-600 rounded-lg outline-none cursor-not-allowed"
                            disabled
                        >
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-lock mr-1"></i>
                            Email Anda tidak dapat diubah untuk keamanan akun
                        </p>
                    </div>
                </div>

                {{-- Password Section --}}
                <div class="pb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Ubah Password</h3>
                    <p class="text-sm text-gray-600 mb-4">Biarkan kosong jika tidak ingin mengubah password</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                            <input
                                type="password"
                                name="password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                                placeholder="Minimal 8 karakter"
                            >
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password Confirmation --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                                placeholder="Konfirmasi password baru"
                            >
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3 pt-4">
                    <button 
                        type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2"
                    >
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>
                    <a 
                        href="{{ route('instructor.dashboard') }}"
                        class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium"
                    >
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Preview avatar client-side --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('avatarInput');
    const preview = document.getElementById('avatarPreview');

    if (!input || !preview) return;

    input.addEventListener('change', () => {
        const file = input.files && input.files[0];
        if (!file) return;

        // Validasi size 2MB
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran gambar melebihi 2MB. Silakan pilih gambar yang lebih kecil.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection
