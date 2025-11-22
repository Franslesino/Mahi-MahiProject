{{-- resources/views/courses/show.blade.php --}}
@extends('layouts.app')

@php
    // Biar gampang baca di Blade
    $judul        = $course->judul ?? $course->title ?? '';
    $deskripsi    = $course->deskripsi ?? $course->description ?? '';
    $kategori     = $course->kategori ?? $course->category ?? 'General';
    $mode         = $course->mode ?? 'Online';
    $badge        = $course->badge ?? null;

    // Harga / diskon – ambil dari kolom yang ada
    $hargaAsli    = $course->harga ?? $course->price ?? 0;
    $hargaDiskon  = $course->discount_price && $course->discount_price > 0
                    ? $course->discount_price
                    : null;

    $hargaTampil  = $hargaDiskon ?? $hargaAsli;

    // Hindari bagi 0 saat hitung persen diskon
    $persenDiskon = ($hargaDiskon && $hargaAsli > 0)
        ? round((($hargaAsli - $hargaDiskon) / $hargaAsli) * 100)
        : 0;

    // Hitung count materi kalau belum ada
    $materialsCount = $course->materials_count
        ?? $course->materi_count
        ?? ($materials->count() ?? 0);
@endphp

@section('title', $judul . ' - Detail Kursus')

@section('content')
<div class="bg-white min-h-screen">
    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Content (Main) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Course Thumbnail -->
                <div class="rounded-2xl overflow-hidden shadow-lg">
                    @if($course->image)
                        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $judul }}" 
                             class="w-full h-64 object-cover">
                    @else
                        <div class="w-full h-64 bg-gradient-to-br from-gray-800 to-black flex items-center justify-center">
                            <i class="fas fa-code text-white text-8xl opacity-20"></i>
                        </div>
                    @endif
                </div>

                <!-- Course Title -->
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-3">{{ $judul }}</h1>
                    <p class="text-gray-600 text-lg leading-relaxed">{{ $deskripsi }}</p>
                </div>

                <!-- Instructor Info -->
                <div class="flex items-center gap-4 py-6 border-y border-gray-200">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-2xl font-bold">
                            {{ substr($course->instructor->name ?? 'I', 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">{{ $course->instructor->name ?? 'Instruktur' }}</h3>
                        <p class="text-gray-600 text-sm">Instruktur</p>
                    </div>
                </div>

                <!-- Skills Section -->
                 <!-- Masih Statis -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Keterampilan yang didapat</h2>
                    <div class="flex flex-wrap gap-3">
                        @php
                            $skills = ['Web Service', 'SQL', 'Data Modeling', 'Web Scraping', 'Data Modeling', 'API', 'Data Visualization'];
                        @endphp
                        @foreach($skills as $skill)
                            <span class="px-4 py-2 bg-teal-600 text-white rounded-full text-sm font-medium hover:bg-teal-700 transition cursor-pointer">
                                {{ $skill }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- What You'll Learn Section -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Apa yang akan kamu pelajari?</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Spesialisasi ini dibangun berdasarkan kesuksesan kursus {{ $judul }} untuk Semua Orang 
                        dan akan memperkenalkan konsep-konsep pemrograman fundamental termasuk struktur data, 
                        antarmuka program aplikasi jaringan, dan basis data, menggunakan bahasa pemrograman Python.
                    </p>
                    @auth
                        @if(!$isEnrolled)
                            <a href="{{ route('payment.checkout', $course) }}" class="inline-block px-8 py-3 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition">
                                Bayar
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="lg:col-span-1">
                <!-- Price Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-4 border border-gray-200">
                    <div class="mb-6">
                        @if($hargaDiskon)
                            <div class="mb-2">
                                <span class="text-4xl font-bold text-gray-900">
                                    Rp {{ number_format($hargaDiskon, 0, ',', '.') }}
                                </span>
                                <span class="text-lg text-gray-500 line-through ml-2">
                                    Rp {{ number_format($hargaAsli, 0, ',', '.') }}
                                </span>
                            </div>
                            @if($persenDiskon > 0)
                                <span class="inline-block px-3 py-1 bg-red-100 text-red-700 text-sm rounded-full font-semibold">
                                    Hemat {{ $persenDiskon }}%
                                </span>
                            @endif
                        @else
                            <span class="text-4xl font-bold text-gray-900">
                                Rp {{ number_format($hargaAsli, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>

                    @auth
                        @if($isEnrolled)
                            <a href="{{ route('student.course.learn', $course) }}" 
                               class="block w-full text-center px-6 py-3 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition mb-3">
                                <i class="fas fa-play mr-2"></i>
                                Lanjutkan Belajar
                            </a>
                        @else
                            <a href="{{ route('payment.checkout', $course) }}" 
                               class="block w-full text-center px-6 py-3 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition mb-3">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Beli Sekarang
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" 
                           class="block w-full text-center px-6 py-3 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition mb-3">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login untuk Membeli
                        </a>
                    @endauth

                    <button class="w-full px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:border-teal-600 hover:text-teal-600 transition">
                        <i class="fas fa-heart mr-2"></i>
                        Tambahkan ke Wishlist
                    </button>
                </div>

                <!-- Course Stats -->
                <div class="mt-6 space-y-3">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-3 text-gray-700">
                            <i class="fas fa-users text-teal-600 text-xl"></i>
                            <div>
                                <div class="text-2xl font-bold text-gray-900">{{ $course->students_count ?? 0 }}</div>
                                <p class="text-sm text-gray-600">Siswa Terdaftar</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-3 text-gray-700">
                            <i class="fas fa-video text-red-600 text-xl"></i>
                            <div>
                                <div class="text-2xl font-bold text-gray-900">{{ $course->videos ?? 0 }}</div>
                                <p class="text-sm text-gray-600">Video Pembelajaran</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-3 text-gray-700">
                            <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                            <div>
                                <div class="text-2xl font-bold text-gray-900">{{ $materialsCount }}</div>
                                <p class="text-sm text-gray-600">Total Materi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>