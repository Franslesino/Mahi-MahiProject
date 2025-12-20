@extends('layouts.app')

@section('title', 'Instruktur - UpGreenius')

@section('content')
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center text-gray-600 hover:text-teal-600 transition font-medium group">
                    <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Beranda
                </a>
            </div>

            <!-- Header -->
            <div class="text-center mb-10">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    <span class="text-teal-600">Instruktur</span> Kami
                </h1>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Belajar dari para ahli di bidangnya.
                </p>
            </div>

            <!-- Instructors Grid -->
            @if($instructors->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($instructors as $instructor)
                        <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 p-6 text-center">
                            <div class="mb-4">
                                @if($instructor->avatar_url)
                                    <img src="{{ $instructor->avatar_url }}" 
                                         alt="{{ $instructor->name }}"
                                         class="w-20 h-20 rounded-full object-cover mx-auto border-4 border-teal-100">
                                @else
                                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center mx-auto">
                                        <span class="text-2xl font-bold text-white">{{ $instructor->initials ?? strtoupper(substr($instructor->name, 0, 2)) }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Name -->
                            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $instructor->name }}</h3>

                            <!-- Email -->
                            <p class="text-sm text-gray-600 truncate mb-2">{{ $instructor->email }}</p>

                            <!-- Course Count -->
                            <p class="text-sm text-gray-500 mb-4">
                                <i class="fas fa-book-open text-teal-500 mr-1"></i>
                                {{ $instructor->total_courses ?? $instructor->courses_count ?? 0 }} Kursus
                            </p>

                            <!-- Detail Button -->
                            <a href="{{ route('instructors.show', $instructor) }}" 
                               class="inline-flex items-center justify-center w-full px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-semibold hover:bg-teal-700 transition">
                                <i class="fas fa-user mr-2"></i>
                                Lihat Detail
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $instructors->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                    <div class="max-w-sm mx-auto">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chalkboard-teacher text-gray-400 text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Instruktur</h3>
                        <p class="text-gray-600 mb-4">Instruktur akan segera tersedia.</p>
                        <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
