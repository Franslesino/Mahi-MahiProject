@extends('layouts.app')

@section('title', 'Kursus Saya - Pelayanan TIK PNJ')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Kursus Saya</h1>
    
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <h2 class="text-2xl font-semibold text-gray-700 mb-2">Belum Ada Kursus</h2>
        <p class="text-gray-500 mb-6">Anda belum mendaftar kursus apapun</p>
        <a href="{{ route('home') }}" class="inline-block px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">
            Jelajahi Kursus
        </a>
    </div>
</div>
@endsection