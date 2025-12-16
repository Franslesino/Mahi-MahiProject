@extends('layouts.instructor')

@section('title', 'Sesi Kelas - ' . $course->judul)

@section('content')
    <div class="max-w-6xl mx-auto">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('instructor.courses.show', $course) }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Sesi Kelas</h1>
                    {!! $course->metode_badge !!}
                </div>
                <p class="text-gray-600">{{ $course->judul }}</p>
            </div>
            <a href="{{ route('instructor.courses.sessions.create', $course) }}"
                class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-[#005F56] text-white rounded-lg hover:bg-[#004940] transition">
                <i class="fas fa-plus mr-2"></i>
                Tambah Sesi
            </a>
        </div>

        {{-- Sessions List --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            @if($sessions->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($sessions as $session)
                        <div class="p-6 hover:bg-gray-50 transition">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span
                                            class="px-2 py-1 rounded text-xs font-medium {{ $session->tipe === 'online' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($session->tipe) }}
                                        </span>
                                        <span class="px-2 py-1 rounded text-xs font-medium
                                                    {{ $session->status === 'completed' ? 'bg-gray-100 text-gray-800' : '' }}
                                                    {{ $session->status === 'scheduled' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $session->status === 'ongoing' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $session->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                                ">
                                            {{ ucfirst($session->status) }}
                                        </span>
                                    </div>
                                    <h3 class="font-semibold text-gray-900 mb-1">
                                        {{ $session->judul ?? 'Pertemuan ' . $loop->iteration }}
                                    </h3>
                                    <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                        <span><i class="fas fa-calendar mr-1"></i> {{ $session->tanggal->format('d M Y') }}</span>
                                        <span><i class="fas fa-clock mr-1"></i> {{ $session->waktu_mulai->format('H:i') }} -
                                            {{ $session->waktu_selesai->format('H:i') }}</span>
                                        @if($session->lokasi)
                                            <span><i class="fas fa-map-marker-alt mr-1"></i> {{ $session->lokasi }}</span>
                                        @endif
                                    </div>
                                    @if($session->meeting_link && $session->tipe === 'online')
                                        <div class="mt-2">
                                            <a href="{{ $session->meeting_link }}" target="_blank"
                                                class="text-blue-600 hover:underline text-sm">
                                                <i class="fas fa-video mr-1"></i> Link Meeting
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-[#005F56]">{{ $session->hadir_count ?? 0 }}</div>
                                        <div class="text-xs text-gray-500">Hadir</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('instructor.courses.sessions.attendances.index', [$course, $session]) }}"
                                            class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium">
                                            <i class="fas fa-clipboard-list mr-1"></i> Absensi
                                        </a>
                                        <a href="{{ route('instructor.courses.sessions.edit', [$course, $session]) }}"
                                            class="px-3 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition text-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('instructor.courses.sessions.destroy', [$course, $session]) }}"
                                            method="POST" class="inline" onsubmit="return confirm('Hapus sesi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-alt text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Belum Ada Sesi Kelas</h3>
                    <p class="text-gray-600 mb-4">Buat sesi kelas untuk kursus offline/hybrid Anda.</p>
                    <a href="{{ route('instructor.courses.sessions.create', $course) }}"
                        class="inline-flex items-center px-4 py-2 bg-[#005F56] text-white rounded-lg hover:bg-[#004940] transition">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Sesi Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection