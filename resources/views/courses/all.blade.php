@extends('layouts.app')

@section('title', 'Semua Kursus - UpGreenius')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Back to Home -->
        <div class="mb-6">
            <a href="{{ route('home') }}"
                class="inline-flex items-center gap-2 text-pnj-teal font-semibold hover:underline">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Semua Kursus</h1>
            <p class="text-gray-600">Jelajahi semua kursus yang tersedia di platform UpGreenius</p>
        </div>

        <!-- Search & Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <form action="{{ route('courses.all') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <!-- Search Input -->
                <div class="flex-1 relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari kursus berdasarkan judul atau deskripsi..."
                        class="w-full rounded-lg border border-gray-300 pl-12 pr-4 py-3 focus:ring-2 focus:ring-pnj-teal focus:outline-none shadow-sm placeholder-gray-400">
                    <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Search Button -->
                <button type="submit"
                    class="px-6 py-3 bg-pnj-teal text-white font-semibold rounded-lg hover:bg-teal-700 transition shadow-sm">
                    Cari
                </button>
            </form>
        </div>

        <!-- Categories Filter -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Kategori</h3>
            <div class="flex gap-3 overflow-x-auto pb-2">
                @php
                    $currentCategory = request('category', 'Semua');
                    $categoryIcons = [
                        '3D Design' => 'fa-cube',
                        'Data Analytic' => 'fa-chart-line',
                        'Graphic Design' => 'fa-pen-nib',
                        'Web Development' => 'fa-code',
                        'AI / Machine Learning' => 'fa-brain',
                        'Database' => 'fa-database',
                        'Programming' => 'fa-terminal',
                        'Mobile Development' => 'fa-mobile-alt',
                        'Data Science' => 'fa-flask',
                    ];
                    $categoriesList = collect($categoryCounts ?? [])->keys()->prepend('Semua');
                @endphp

                @foreach($categoriesList as $cat)
                    @php
                        $icon = $categoryIcons[$cat] ?? 'fa-folder';
                        $count = $cat === 'Semua' ? ($courses->total() ?? 0) : ($categoryCounts[$cat] ?? 0);
                    @endphp
                    <a href="{{ route('courses.all', array_merge(request()->only('search'), ['category' => $cat])) }}"
                        class="px-5 py-2.5 rounded-lg font-medium whitespace-nowrap transition shadow-sm flex items-center gap-2
                                   {{ $currentCategory === $cat ? 'bg-pnj-teal text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                        <i class="fas {{ $icon }}"></i>
                        <span>{{ $cat }}</span>
                        <span
                            class="text-xs px-2 py-0.5 rounded-full {{ $currentCategory === $cat ? 'bg-white/20' : 'bg-gray-100 text-gray-600' }}">
                            {{ $count }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Results Info -->
        <div class="flex items-center justify-between mb-6">
            <p class="text-gray-600">
                Menampilkan <span class="font-semibold text-gray-900">{{ $courses->count() }}</span> dari
                <span class="font-semibold text-gray-900">{{ $courses->total() }}</span> kursus
            </p>
            @if(request('search') || request('category'))
                <a href="{{ route('courses.all') }}" class="text-pnj-teal font-medium hover:underline flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Reset Filter
                </a>
            @endif
        </div>

        <!-- Courses Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($courses as $course)
                @php
                    $isFull = ($course->isOffline() || $course->isHybrid()) && $course->isFull();
                @endphp
                <a href="{{ $isFull ? '#' : route('courses.show', $course) }}"
                    class="bg-white rounded-xl shadow-sm hover:shadow-xl border border-gray-100 hover:-translate-y-1 transition overflow-hidden group cursor-pointer relative {{ $isFull ? 'grayscale cursor-not-allowed opacity-80' : '' }}">
                    
                    @if($isFull)
                        <div class="absolute inset-0 z-20 flex items-center justify-center bg-black/40 backdrop-blur-[1px]">
                            <div class="bg-red-600 text-white px-4 py-2 rounded-full font-bold shadow-2xl transform -rotate-12 border-2 border-white text-lg tracking-wider">
                                KUOTA HABIS
                            </div>
                        </div>
                    @endif
                    
                    <!-- Image -->
                    <div class="relative h-40 bg-gray-900 overflow-hidden">
                        @if($course->image_url)
                            <img src="{{ $course->image_url }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                alt="{{ $course->judul }}">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white text-5xl opacity-50"></i>
                            </div>
                        @endif

                        <!-- Category -->
                        <div class="absolute top-3 left-3">
                            @php
                                $cat = $course->kategori ?? 'General';
                                $catColors = [
                                    'AI / Machine Learning' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                    'Data Analytic' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'Database' => 'bg-slate-100 text-slate-800 border-slate-200',
                                    'Web Development' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'Graphic Design' => 'bg-pink-100 text-pink-800 border-pink-200',
                                    '3D Design' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'Mobile Development' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'Data Science' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                ];
                                $badgeClass = $catColors[$cat] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold border shadow-sm {{ $badgeClass }}">
                                {{ $cat }}
                            </span>
                        </div>

                        <!-- Badge (Existing) -->
                        @if($course->badge)
                            <div class="absolute top-3 right-3">
                                <span class="bg-yellow-500 text-white text-xs px-2.5 py-1 rounded-full font-semibold shadow-sm">
                                    {{ $course->badge }}
                                </span>
                            </div>
                        @endif

                        <!-- Mode Badge (New) -->
                        <div class="absolute bottom-3 left-3">
                             <span class="text-xs px-2.5 py-1 rounded-full font-semibold shadow-sm border
                                {{ $course->isOnline() ? 'bg-white/90 text-blue-700 border-blue-200 backdrop-blur-sm' : ($course->isOffline() ? 'bg-white/90 text-orange-700 border-orange-200 backdrop-blur-sm' : 'bg-white/90 text-purple-700 border-purple-200 backdrop-blur-sm') }}">
                                <i class="fas fa-{{ $course->isOnline() ? 'laptop' : ($course->isOffline() ? 'users' : 'random') }} mr-1"></i>
                                {{ ucfirst($course->mode ?? 'Online') }}
                            </span>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-pnj-teal transition-colors">
                            {{ $course->judul }}
                        </h4>

                        <div class="flex items-center gap-2 text-sm text-gray-600 mb-3">
                            <i class="fas fa-user-tie"></i>
                            <span>{{ $course->instructor->name ?? $course->pembuat->name ?? 'Instruktur' }}</span>
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                            <span><i class="fas fa-user-graduate mr-1"></i>{{ $course->students_count ?? 0 }} Siswa</span>
                            <span class="text-gray-300">|</span>
                            <span><i class="fas fa-video mr-1"></i>{{ $course->videos_count ?? 0 }} Video</span>
                            <span class="text-gray-300">|</span>
                            <span>{{ $course->materi_count ?? 0 }} Materi</span>
                        </div>

                        <!-- Purchase Deadline (New) -->
                        @if($course->purchase_deadline_date && $course->purchase_deadline_date->isFuture())
                            <div class="mb-3 bg-teal-50 text-teal-800 text-xs px-3 py-1.5 rounded-lg flex items-center gap-2">
                                <i class="fas fa-clock"></i>
                                <span>Tersedia hingga {{ $course->purchase_deadline_date->format('d M Y') }}</span>
                            </div>
                        @endif

                        <!-- Capacity Info for Offline/Hybrid (New) -->
                        @if($course->hasCapacityLimit())
                            <div class="mb-3">
                                @php $slots = $course->available_slots; @endphp
                                <div class="flex justify-between items-center mb-1 text-xs">
                                    <span class="text-gray-600 font-medium"><i class="fas fa-chair mr-1"></i> Sisa Kuota</span>
                                    <span class="{{ $slots > 0 ? 'text-emerald-700' : 'text-red-600' }} font-bold">
                                        {{ $slots > 0 ? $slots : 'PENUH' }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    @php 
                                        $percent = $course->max_participants > 0 
                                            ? min(100, (($course->max_participants - $slots) / $course->max_participants) * 100) 
                                            : 100;
                                    @endphp
                                    <div class="{{ $slots > 0 ? 'bg-teal-500' : 'bg-red-500' }} h-full rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-between">
                            @php
                                $basePrice = $course->harga ?? $course->price ?? 0;
                                $finalPrice = ($course->discount_price ?? 0) > 0
                                    ? $course->discount_price
                                    : $basePrice;
                            @endphp

                            <div class="flex flex-col gap-1">
                                <span class="text-2xl font-bold text-pnj-teal">
                                    Rp {{ number_format($finalPrice, 0, ',', '.') }}
                                </span>

                                @if(($course->discount_price ?? 0) > 0)
                                    <span class="text-sm text-gray-500 line-through">
                                        Rp {{ number_format($basePrice, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>

            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-600 space-y-3">
                        <i class="fas fa-search text-3xl text-gray-400"></i>
                        <p class="font-semibold text-gray-800">Kursus tidak ditemukan</p>
                        <p class="text-sm text-gray-500">Coba gunakan kata kunci lain atau lihat semua kategori.</p>
                        <a href="{{ route('courses.all') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-pnj-teal text-white rounded-lg font-semibold shadow hover:-translate-y-0.5 transition">
                            Reset Filter
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-10 mb-8">
            {{ $courses->links() }}
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