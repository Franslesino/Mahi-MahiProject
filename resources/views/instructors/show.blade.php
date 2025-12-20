@extends('layouts.app')

@section('title', $instructor->name . ' - Instruktur UpGreenius')

@section('content')
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('instructors.index') }}"
                    class="inline-flex items-center text-gray-600 hover:text-teal-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Daftar Instruktur
                </a>
            </div>

            <!-- Instructor Profile Card -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-teal-600 to-emerald-600 h-32"></div>
                <div class="px-6 pb-6">
                    <div class="flex flex-col sm:flex-row items-center sm:items-end gap-4 -mt-16">
                        <div class="flex-shrink-0">
                            @if($instructor->avatar_url)
                                <img src="{{ $instructor->avatar_url }}" alt="{{ $instructor->name }}"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
                            @else
                                <div
                                    class="w-32 h-32 rounded-full bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center border-4 border-white shadow-lg">
                                    <span class="text-4xl font-bold text-white">{{ $instructor->initials }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="text-center sm:text-left flex-1 pt-4 sm:pt-0">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $instructor->full_name ?? $instructor->name }}
                            </h1>
                            <p class="text-gray-500">{{ $instructor->profesi ?? 'Instruktur' }}</p>
                            @if($instructor->email)
                                <p class="text-sm text-gray-400 mt-1">
                                    <i class="fas fa-envelope mr-1"></i>
                                    {{ $instructor->email }}
                                </p>
                            @endif
                        </div>

                        <!-- Stats -->
                        <div class="flex gap-6 mt-4 sm:mt-0">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-teal-600">{{ $courses->count() }}</div>
                                <div class="text-xs text-gray-500">Kursus</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $courses->sum('students_count') }}</div>
                                <div class="text-xs text-gray-500">Total Siswa</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courses Section -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-book-open text-teal-600 mr-2"></i>
                    Kursus yang Diampu
                </h2>

                @if($courses->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($courses as $course)
                            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-4">
                                <div class="flex gap-4">
                                    <!-- Thumbnail -->
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-20 h-20 rounded-lg overflow-hidden bg-gradient-to-br from-gray-700 to-gray-900">
                                            @if($course->image_url)
                                                <img src="{{ $course->image_url }}" alt="{{ $course->judul }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-graduation-cap text-white text-xl opacity-50"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Course Info -->
                                    <div class="flex-1 min-w-0">
                                        <span class="inline-block px-2 py-0.5 bg-teal-100 text-teal-700 text-xs rounded-full mb-1">
                                            {{ $course->kategori ?? 'General' }}
                                        </span>
                                        <h3 class="font-bold text-gray-900 truncate">{{ $course->judul ?? $course->title }}</h3>
                                        <div class="flex items-center gap-4 text-xs text-gray-500 mt-1">
                                            <span><i class="fas fa-book mr-1"></i>{{ $course->materi_count }} Materi</span>
                                            <span><i class="fas fa-users mr-1"></i>{{ $course->students_count }} Siswa</span>
                                        </div>
                                        <a href="{{ route('courses.show', $course) }}"
                                            class="inline-flex items-center text-teal-600 text-sm font-semibold mt-2 hover:text-teal-700">
                                            Lihat Kursus <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                        <i class="fas fa-book-open text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">Instruktur ini belum memiliki kursus yang dipublikasikan.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection