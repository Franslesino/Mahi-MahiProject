{{-- resources/views/courses/show.blade.php --}}
@extends('layouts.app')

@section('title', $course->title . ' - Detail Kursus')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-900 to-blue-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Course Info -->
                <div class="lg:col-span-2">
                    <!-- Breadcrumb -->
                    <nav class="flex items-center gap-2 text-sm text-blue-200 mb-4">
                        <a href="{{ route('home') }}" class="hover:text-white">Beranda</a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <a href="{{ route('courses.index') }}" class="hover:text-white">Kursus</a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="text-white">{{ $course->title }}</span>
                    </nav>

                    <!-- Category & Badge -->
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-3 py-1 bg-teal-500 text-white text-sm rounded-full font-semibold">
                            {{ $course->category }}
                        </span>
                        @if($course->badge)
                        <span class="px-3 py-1 bg-yellow-500 text-white text-sm rounded-full font-semibold">
                            {{ $course->badge }}
                        </span>
                        @endif
                        <span class="px-3 py-1 bg-purple-500 text-white text-sm rounded-full font-semibold">
                            {{ $course->mode }}
                        </span>
                    </div>

                    <!-- Title -->
                    <h1 class="text-4xl font-bold mb-4">{{ $course->title }}</h1>
                    
                    <!-- Description -->
                    <p class="text-blue-100 text-lg mb-6 leading-relaxed">{{ $course->description }}</p>

                    <!-- Meta Info -->
                    <div class="flex flex-wrap items-center gap-6 text-sm">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user-tie"></i>
                            <span>{{ $course->instructor->name ?? 'Instruktur' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-video"></i>
                            <span>{{ $course->videos }} Video</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-file-alt"></i>
                            <span>{{ $course->materials_count }} Materi</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-star text-yellow-400"></i>
                            <span>{{ number_format($course->rating, 1) }} Rating</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-users"></i>
                            <span>{{ $course->students_count ?? 0 }} Siswa</span>
                        </div>
                    </div>
                </div>

                <!-- Price Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-xl p-6 text-gray-900 sticky top-4">
                        @if($course->image)
                        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" 
                             class="w-full h-48 object-cover rounded-lg mb-4">
                        @else
                        <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg mb-4 flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-white text-6xl opacity-50"></i>
                        </div>
                        @endif

                        <!-- Price -->
                        <div class="mb-6">
                            @if($course->discount_price)
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-3xl font-bold text-blue-900">
                                    Rp {{ number_format($course->discount_price, 0, ',', '.') }}
                                </span>
                                <span class="text-lg text-gray-500 line-through">
                                    Rp {{ number_format($course->price, 0, ',', '.') }}
                                </span>
                            </div>
                            <span class="inline-block px-3 py-1 bg-red-100 text-red-700 text-sm rounded-full font-semibold">
                                Hemat {{ number_format((($course->price - $course->discount_price) / $course->price) * 100, 0) }}%
                            </span>
                            @else
                            <span class="text-3xl font-bold text-blue-900">
                                Rp {{ number_format($course->price, 0, ',', '.') }}
                            </span>
                            @endif
                        </div>

                        @auth
                            @if($isEnrolled)
                                <a href="{{ route('student.course.learn', $course) }}" 
                                   class="block w-full text-center px-6 py-4 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition shadow-lg mb-3">
                                    <i class="fas fa-play-circle mr-2"></i>
                                    Lanjutkan Belajar
                                </a>
                            @else
<form action="{{ route('courses.enroll', $course) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full px-6 py-4 bg-blue-900 text-white rounded-lg font-semibold hover:bg-blue-800 transition shadow-lg mb-3">
                                        <i class="fas fa-shopping-cart mr-2"></i>
                                        Beli Sekarang
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" 
                               class="block w-full text-center px-6 py-4 bg-blue-900 text-white rounded-lg font-semibold hover:bg-blue-800 transition shadow-lg mb-3">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Login untuk Membeli
                            </a>
                        @endauth

                        <button class="w-full px-6 py-3 border-2 border-blue-900 text-blue-900 rounded-lg font-semibold hover:bg-blue-50 transition">
                            <i class="fas fa-heart mr-2"></i>
                            Tambah ke Wishlist
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- What You'll Learn -->
                @if($course->learning)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        Yang Akan Anda Pelajari
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach(explode("\n", $course->learning) as $item)
                            @if(trim($item))
                            <div class="flex items-start gap-3">
                                <i class="fas fa-check text-green-500 mt-1"></i>
                                <span class="text-gray-700">{{ trim($item) }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Course Content -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-list mr-2"></i>
                            Konten Kursus
                        </h2>
                        <span class="text-sm text-gray-600">
                            {{ $materials->count() }} Materi • {{ $course->videos }} Video
                        </span>
                    </div>

                    @if($materials->isEmpty())
                        <div class="text-center py-12">
                            <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">Materi kursus sedang disiapkan</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($materials as $index => $material)
                            <div class="border border-gray-200 rounded-lg hover:border-blue-300 transition">
                                <div class="p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start gap-4 flex-1">
                                            <!-- Icon -->
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ 
                                                $material->type === 'video' ? 'bg-red-100' : 
                                                ($material->type === 'pdf' ? 'bg-blue-100' : 
                                                ($material->type === 'quiz' ? 'bg-purple-100' : 'bg-gray-100'))
                                            }}">
                                                <i class="fas fa-{{ 
                                                    $material->type === 'video' ? 'play-circle' : 
                                                    ($material->type === 'pdf' ? 'file-pdf' : 
                                                    ($material->type === 'quiz' ? 'question-circle' : 'align-left'))
                                                }} text-{{ 
                                                    $material->type === 'video' ? 'red' : 
                                                    ($material->type === 'pdf' ? 'blue' : 
                                                    ($material->type === 'quiz' ? 'purple' : 'gray'))
                                                }}-600"></i>
                                            </div>

                                            <!-- Content -->
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h4 class="font-semibold text-gray-900">{{ $material->title }}</h4>
                                                    @if($material->is_preview)
                                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">
                                                        Preview Gratis
                                                    </span>
                                                    @endif
                                                </div>
                                                @if($material->description)
                                                <p class="text-sm text-gray-600 mb-2">{{ $material->description }}</p>
                                                @endif
                                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                                    <span class="capitalize">
                                                        <i class="fas fa-tag mr-1"></i>{{ $material->type }}
                                                    </span>
                                                    @if($material->duration)
                                                    <span>
                                                        <i class="fas fa-clock mr-1"></i>{{ $material->duration }} menit
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action -->
                                        @if($material->is_preview || $isEnrolled)
                                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                            <i class="fas fa-play mr-1"></i>
                                            Lihat
                                        </button>
                                        @else
                                        <div class="flex items-center gap-2 text-gray-400">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Instructor Info -->
                @if($course->instructor)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-user-tie mr-2"></i>
                        Instruktur
                    </h2>
                    <div class="flex items-start gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-2xl font-bold">{{ substr($course->instructor->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $course->instructor->name }}</h3>
                            <p class="text-gray-600 mb-3">{{ $course->instructor->email }}</p>
                            <div class="flex items-center gap-4 text-sm text-gray-600">
                                <span><i class="fas fa-book mr-1"></i>{{ $instructorCourses }} Kursus</span>
                                <span><i class="fas fa-users mr-1"></i>{{ $instructorStudents }} Siswa</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Course Features -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Fitur Kursus</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-gray-700">
                            <i class="fas fa-infinity text-blue-600"></i>
                            <span>Akses Selamanya</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-700">
                            <i class="fas fa-mobile-alt text-blue-600"></i>
                            <span>Akses via Mobile</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-700">
                            <i class="fas fa-certificate text-blue-600"></i>
                            <span>Sertifikat</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-700">
                            <i class="fas fa-download text-blue-600"></i>
                            <span>Materi Download</span>
                        </div>
                    </div>
                </div>

                <!-- Related Courses -->
                @if($relatedCourses->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Kursus Terkait</h3>
                    <div class="space-y-4">
                        @foreach($relatedCourses as $related)
                        <a href="{{ route('courses.show', $related) }}" class="block group">
                            <div class="flex gap-3">
                                <div class="w-24 h-16 flex-shrink-0 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg overflow-hidden">
                                    @if($related->image)
                                    <img src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->title }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 group-hover:text-blue-600 transition line-clamp-2 text-sm mb-1">
                                        {{ $related->title }}
                                    </h4>
                                    <span class="text-sm font-bold text-blue-900">
                                        Rp {{ number_format($related->discount_price ?? $related->price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection