{{-- resources/views/student/courses/index.blade.php --}}
@extends('layouts.app') {{-- ganti kalau layout-mu namanya lain --}}

@section('title', 'Kursus Saya')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Kursus Saya</h1>
        <p class="text-gray-600 text-sm mt-1">
            Daftar kursus yang sudah kamu ikuti akan tampil di sini.
        </p>
    </div>

    {{-- Sementara statis dulu, nanti bisa dihubungkan ke tabel enrollments --}}
    <div class="bg-white border border-dashed border-gray-300 rounded-xl p-8 text-center">
        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="text-gray-600 mb-2">
            Kamu belum mengambil kursus apa pun.
        </p>
        <a href="{{ route('courses.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-pnj-teal text-white text-sm rounded-full hover:bg-emerald-700 transition">
            <span>Jelajahi Kursus</span>
        </a>
    </div>
</div>
@endsection
