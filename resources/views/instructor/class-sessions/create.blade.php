@extends('layouts.instructor')

@section('title', 'Tambah Sesi Kelas')

@section('content')
    <div class="max-w-2xl mx-auto">
        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('instructor.courses.sessions.index', $course) }}"
                class="text-gray-500 hover:text-gray-700 mb-2 inline-block">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Sesi Kelas</h1>
            <p class="text-gray-600">{{ $course->judul }}</p>
        </div>

        {{-- Form --}}
        <form action="{{ route('instructor.courses.sessions.store', $course) }}" method="POST"
            class="bg-white rounded-xl shadow-sm p-6">
            @csrf

            <div class="space-y-6">
                {{-- Judul --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Sesi <span
                            class="text-gray-400">(opsional)</span></label>
                    <input type="text" name="judul" value="{{ old('judul') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent"
                        placeholder="Contoh: Pertemuan 1: Pengenalan">
                    @error('judul')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent">
                    @error('tanggal')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Waktu --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai <span
                                class="text-red-500">*</span></label>
                        <input type="time" name="waktu_mulai" value="{{ old('waktu_mulai') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent">
                        @error('waktu_mulai')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai <span
                                class="text-red-500">*</span></label>
                        <input type="time" name="waktu_selesai" value="{{ old('waktu_selesai') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent">
                        @error('waktu_selesai')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Tipe Sesi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Sesi <span
                            class="text-red-500">*</span></label>
                    <select name="tipe" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent">
                        <option value="offline" {{ old('tipe') === 'offline' ? 'selected' : '' }}>Offline (Tatap Muka)
                        </option>
                        <option value="online" {{ old('tipe') === 'online' ? 'selected' : '' }}>Online (Virtual)</option>
                    </select>
                    @error('tipe')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lokasi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi <span class="text-gray-400">(untuk
                            offline)</span></label>
                    <input type="text" name="lokasi" value="{{ old('lokasi', $course->lokasi) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent"
                        placeholder="Contoh: Ruang 301, Gedung A">
                    @error('lokasi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Meeting Link --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Link Meeting <span
                            class="text-gray-400">(untuk online)</span></label>
                    <input type="url" name="meeting_link" value="{{ old('meeting_link') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent"
                        placeholder="https://zoom.us/j/...">
                    @error('meeting_link')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan <span
                            class="text-gray-400">(opsional)</span></label>
                    <textarea name="catatan" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent"
                        placeholder="Catatan tambahan untuk sesi ini...">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submit --}}
            <div class="mt-6 flex gap-3">
                <button type="submit"
                    class="flex-1 px-4 py-3 bg-[#005F56] text-white rounded-lg hover:bg-[#004940] transition font-semibold">
                    <i class="fas fa-plus mr-2"></i> Simpan Sesi
                </button>
                <a href="{{ route('instructor.courses.sessions.index', $course) }}"
                    class="px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection