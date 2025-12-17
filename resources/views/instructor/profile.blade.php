@extends('layouts.instructor')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Carbon;

    /** @var \App\Models\User $user */
    $user = isset($user) ? $user : Auth::user();

    // pastikan input <input type="date"> selalu format Y-m-d
    $rawDob   = old('dob', $user->dob);
    $dobValue = $rawDob ? Carbon::parse($rawDob)->format('Y-m-d') : '';
@endphp

<div class="p-8">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Edit Profile</h2>
        <p class="text-gray-600 mt-2">Kelola informasi profil Anda</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
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
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">
                                    <i class="fas fa-lock mr-1"></i>Tidak dapat diubah
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

                        {{-- Gender --}}
                        @php $g = old('gender', $user->gender); @endphp
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                            <select
                                name="gender"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                            >
                                <option value="" {{ $g === null || $g === '' ? 'selected' : '' }}>Pilih jenis kelamin</option>
                                <option value="Pria" {{ $g === 'Pria' ? 'selected' : '' }}>Pria</option>
                                <option value="Wanita" {{ $g === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                                <option value="Other" {{ $g === 'Other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between pt-4">
                    <a href="{{ route('instructor.dashboard') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>
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

<style>
    /* Custom scrollbar untuk form yang panjang jika diperlukan */
    ::-webkit-scrollbar {
        width: 6px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
@endsection
