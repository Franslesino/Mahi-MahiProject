@extends('layouts.app')

@section('title', 'Edit Profile - Pelayanan TIK PNJ')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Carbon;

    /** @var \App\Models\User $user */
    $user = isset($user) ? $user : Auth::user();

    // pastikan input <input type="date"> selalu format Y-m-d
    $rawDob   = old('dob', $user->dob);
    $dobValue = $rawDob ? Carbon::parse($rawDob)->format('Y-m-d') : '';
@endphp

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Edit Profile</h1>

    {{-- Error messages --}}
    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Flash success --}}
    @if (session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- Avatar --}}
                <div class="flex items-center gap-6">
                    <div class="relative">
                        <img
                            id="avatarPreview"
                            src="{{ $user->avatar_url
                                    ? $user->avatar_url
                                    : 'data:image/svg+xml;utf8,'.rawurlencode(
                                        '<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'96\' height=\'96\'>
                                            <rect width=\'100%\' height=\'100%\' rx=\'48\' fill=\'#14b8a6\'/>
                                            <text x=\'50%\' y=\'56%\' font-size=\'40\' text-anchor=\'middle\' fill=\'white\' font-family=\'Arial, Helvetica, sans-serif\'>'
                                            . e(strtoupper(substr($user->first_name ?: $user->name, 0, 1)))
                                            . '</text>
                                        </svg>'
                                    )
                                }}"
                            alt="Avatar"
                            class="w-24 h-24 rounded-full object-cover ring-2 ring-teal-500/20 bg-gray-100"
                        >
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-700">Ubah Foto</label>
                        <input
                            type="file"
                            name="avatar"
                            id="avatarInput"
                            accept="image/*"
                            class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-teal-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-teal-700"
                        >
                        <p class="text-xs text-gray-500">Format: JPG/PNG/WEBP • Maks 2MB</p>
                    </div>
                </div>

                {{-- Full Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none"
                        required
                    >
                </div>

                {{-- Email (read-only) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email (tidak bisa diubah)</label>
                    <input
                        type="email"
                        value="{{ $user->email }}"
                        class="w-full px-4 py-3 border border-gray-200 bg-gray-100 text-gray-500 rounded-lg outline-none cursor-not-allowed"
                        disabled
                    >
                </div>

                {{-- Nomor Telepon --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                    <input
                        type="tel"
                        name="phone"
                        inputmode="numeric"
                        pattern="[0-9+\-\s()]*"
                        value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none"
                        placeholder="08xxxxxxxxxx"
                    >
                </div>

                {{-- Tanggal Lahir --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                    <input
                        type="date"
                        name="dob"
                        value="{{ $dobValue }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none"
                    >
                </div>

                {{-- Profesi --}}
                @php
                    $profesiOptions = ['Mahasiswa','Pelajar','Karyawan','Dosen','Freelancer','Wiraswasta','Tidak bekerja','Lainnya…'];
                    $profesiValue = old('profesi', $user->profesi);
                @endphp
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Profesi</label>
                    <select
                        name="profesi"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none bg-white"
                    >
                        <option value="" {{ $profesiValue === null || $profesiValue === '' ? 'selected' : '' }}>Pilih</option>
                        @foreach($profesiOptions as $option)
                            <option value="{{ $option }}" {{ $profesiValue === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Gender --}}
                @php $g = old('gender', $user->gender); @endphp
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select
                        name="gender"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none"
                    >
                        <option value="" {{ $g === null || $g === '' ? 'selected' : '' }}>Pilih gender</option>
                        <option value="Pria"   {{ $g === 'Pria' ? 'selected' : '' }}>Pria</option>
                        <option value="Wanita" {{ $g === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                        <option value="Other"  {{ $g === 'Other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

              

                {{-- Tombol --}}
                <div class="flex gap-3 pt-4">
                    <button type="submit"
                            class="px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition font-medium">
                        Simpan Perubahan
                    </button>
                    <a href="{{ url()->previous() }}"
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
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

        // Validasi size 2MB (opsional, server side juga sudah ada)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran gambar melebihi 2MB.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result; // tampilkan preview
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection
