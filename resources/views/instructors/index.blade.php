@extends('layouts.app')

@section('title', 'Instruktur - UpGreenius')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 text-pnj-teal font-semibold hover:underline">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Instruktur</h1>
            <p class="text-gray-600">Daftar instruktur dengan kursus yang sudah dipublikasikan.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($instructors as $instructor)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex gap-4 items-center">
                    <div class="flex-shrink-0">
                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-teal-500 to-emerald-500 flex items-center justify-center text-white text-lg font-bold">
                            {{ strtoupper(substr($instructor->name, 0, 2)) }}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $instructor->name }}</h3>
                        <p class="text-sm text-gray-600 truncate">{{ $instructor->email }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $instructor->total_courses ?? 0 }} kursus</p>
                    </div>
                    <a href="{{ route('instructors.show', $instructor) }}"
                       class="text-pnj-teal text-sm font-semibold hover:underline flex-shrink-0">Lihat</a>
                </div>
            @empty
                <div class="col-span-full bg-white border border-gray-200 rounded-xl p-10 text-center text-gray-600">
                    <i class="fas fa-users text-3xl text-gray-400"></i>
                    <p class="font-semibold text-gray-800 mt-2">Instruktur belum tersedia</p>
                    <p class="text-sm text-gray-500">Belum ada kursus yang dipublikasikan.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $instructors->links() }}
        </div>
    </div>
@endsection
