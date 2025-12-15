@extends('layouts.instructor')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Carbon;

    /** @var \App\Models\User $user */
    $user = $user ?? Auth::user();

    // Format value untuk input date
    $rawDob   = old('dob', $user->dob);
    $dobValue = $rawDob ? Carbon::parse($rawDob)->format('Y-m-d') : '';

    $profesiOptions = ['Mahasiswa', 'Pelajar', 'Karyawan', 'Dosen', 'Freelancer', 'Wiraswasta', 'Tidak bekerja', 'Lainnya'];
    $profesiValue   = old('profesi', $user->profesi);
    $genderValue    = old('gender', $user->gender);
@endphp

<div class="p-6 lg:p-8">
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center shadow-inner">
                <i class="fas fa-user-cog text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Edit Profile</h1>
                <p class="text-gray-600">Perbarui data profil instruktur Anda</p>
            </div>
        </div>

        {{-- Error messages --}}
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700 shadow-sm">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-xl mt-0.5"></i>
                    <div>
                        <h4 class="font-semibold mb-1">Terdapat kesalahan:</h4>
                        <ul class="list-disc pl-5 space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Flash success --}}
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-lg"></i>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form method="POST" action="{{ route('instructor.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Avatar -->
                <div class="p-6 border-b border-gray-100">
                    <div class="flex flex-col md:flex-row md:items-center gap-6">
                        <div class="relative">
                            <img
                                id="avatarPreview"
                                src="{{ $user->avatar_url
                                        ? $user->avatar_url
                                        : 'data:image/svg+xml;utf8,'.rawurlencode(
                                            '<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'96\' height=\'96\'>
                                                <rect width=\'100%\' height=\'100%\' rx=\'48\' fill=\'#2563eb\'/>
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

                        <div class="flex-1 space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-800">Foto Profil</span>
                                <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">JPG/PNG/WEBP</span>
                            </div>
                            <input
                                type="file"
                                name="avatar"
                                id="avatarInput"
                                accept="image/*"
                                class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-blue-700 cursor-pointer"
                            >
                            <p class="text-xs text-gray-500">Format: JPG, PNG, WEBP - Maksimal 2MB</p>
                        </div>
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- First Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Depan <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="first_name"
                                value="{{ old('first_name', $user->first_name) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                required
                            >
                        </div>

                        {{-- Last Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Belakang <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="last_name"
                                value="{{ old('last_name', $user->last_name) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                required
                            >
                        </div>

                        {{-- Email (read-only) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <div class="relative">
                                <input
                                    type="email"
                                    value="{{ $user->email }}"
                                    class="w-full px-4 py-3 border border-gray-200 bg-gray-50 text-gray-500 rounded-lg outline-none cursor-not-allowed"
                                    disabled
                                >
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 flex items-center gap-1">
                                    <i class="fas fa-lock"></i>
                                    <span>Tidak dapat diubah</span>
                                </span>
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                            <input
                                type="tel"
                                name="phone"
                                value="{{ old('phone', $user->phone) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                placeholder="08xxxxxxxxxx"
                            >
                        </div>

                        {{-- Date of Birth --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                            <input
                                type="date"
                                name="dob"
                                value="{{ $dobValue }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                            >
                        </div>

                        {{-- Profesi --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Profesi</label>
                            <select
                                name="profesi"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none bg-white"
                            >
                                <option value="" {{ $profesiValue === null || $profesiValue === '' ? 'selected' : '' }}>Pilih</option>
                                @foreach($profesiOptions as $option)
                                    <option value="{{ $option }}" {{ $profesiValue === $option ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Gender --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                            <select
                                name="gender"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                            >
                                <option value="" {{ $genderValue === null || $genderValue === '' ? 'selected' : '' }}>Pilih jenis kelamin</option>
                                <option value="Pria" {{ $genderValue === 'Pria' ? 'selected' : '' }}>Pria</option>
                                <option value="Wanita" {{ $genderValue === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                                <option value="Other" {{ $genderValue === 'Other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>

                        
                    </div>
                </div>

                <!-- Actions -->
                <div class="p-6 border-t border-gray-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <a href="{{ route('instructor.dashboard') }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali</span>
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                        <i class="fas fa-save"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
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
            alert('Ukuran gambar melebihi 2MB. Silakan pilih file yang lebih kecil.');
            input.value = '';
            return;
        }

        // Validasi format
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            alert('Format file tidak valid. Gunakan JPG, PNG, atau WEBP.');
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
