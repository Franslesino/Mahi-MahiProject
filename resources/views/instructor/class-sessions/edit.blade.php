@extends('layouts.instructor')

@section('title', 'Edit Sesi Kelas')

@section('content')
    <div class="max-w-2xl mx-auto">
        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('instructor.courses.sessions.index', $course) }}"
                class="text-gray-500 hover:text-gray-700 mb-2 inline-block">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Edit Sesi Kelas</h1>
            <p class="text-gray-600">{{ $course->judul }}</p>
        </div>

        {{-- Form --}}
        <form action="{{ route('instructor.courses.sessions.update', [$course, $session]) }}" method="POST"
            class="bg-white rounded-xl shadow-sm p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- Judul --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Sesi</label>
                    <input type="text" name="judul" value="{{ old('judul', $session->judul) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent"
                        placeholder="Contoh: Pertemuan 1: Pengenalan">
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $session->tanggal->format('Y-m-d')) }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent">
                </div>

                {{-- Waktu --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai <span
                                class="text-red-500">*</span></label>
                        <input type="time" name="waktu_mulai"
                            value="{{ old('waktu_mulai', $session->waktu_mulai->format('H:i')) }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai <span
                                class="text-red-500">*</span></label>
                        <input type="time" name="waktu_selesai"
                            value="{{ old('waktu_selesai', $session->waktu_selesai->format('H:i')) }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent">
                    </div>
                </div>

                {{-- Tipe & Status --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Sesi <span
                                class="text-red-500">*</span></label>
                        <select name="tipe" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent">
                            <option value="offline" {{ old('tipe', $session->tipe) === 'offline' ? 'selected' : '' }}>Offline
                            </option>
                            <option value="online" {{ old('tipe', $session->tipe) === 'online' ? 'selected' : '' }}>Online
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span
                                class="text-red-500">*</span></label>
                        <select name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent">
                            <option value="scheduled" {{ old('status', $session->status) === 'scheduled' ? 'selected' : '' }}>
                                Terjadwal</option>
                            <option value="ongoing" {{ old('status', $session->status) === 'ongoing' ? 'selected' : '' }}>
                                Berlangsung</option>
                            <option value="completed" {{ old('status', $session->status) === 'completed' ? 'selected' : '' }}>
                                Selesai</option>
                            <option value="cancelled" {{ old('status', $session->status) === 'cancelled' ? 'selected' : '' }}>
                                Dibatalkan</option>
                        </select>
                    </div>
                </div>

                {{-- Lokasi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <input type="text" name="lokasi" value="{{ old('lokasi', $session->lokasi) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent"
                        placeholder="Contoh: Ruang 301, Gedung A">
                </div>

                {{-- Meeting Link --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Link Meeting</label>
                    <input type="url" name="meeting_link" value="{{ old('meeting_link', $session->meeting_link) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent"
                        placeholder="https://zoom.us/j/...">
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="catatan" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005F56] focus:border-transparent">{{ old('catatan', $session->catatan) }}</textarea>
                </div>
            </div>

            {{-- Submit --}}
            <div class="mt-6 flex gap-3">
                <button type="submit"
                    class="flex-1 px-4 py-3 bg-[#005F56] text-white rounded-lg hover:bg-[#004940] transition font-semibold">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
                <a href="{{ route('instructor.courses.sessions.index', $course) }}"
                    class="px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection