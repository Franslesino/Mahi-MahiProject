@extends('layouts.app')

@section('title', 'Semua Kursus - UpGreenius')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Semua Kursus</h1>
            <p class="text-gray-600">Jelajahi semua kursus yang tersedia di platform UpGreenius</p>
        </div>

        @php
            $currentCategory = request('category', 'Semua');
            $categoriesList = collect($categoryCounts ?? [])->keys()->prepend('Semua');
        @endphp

        <div class="mb-6 flex gap-3 overflow-x-auto pb-2">
            @foreach($categoriesList as $cat)
                @php
                    $count = $cat === 'Semua' ? ($courses->total() ?? 0) : ($categoryCounts[$cat] ?? 0);
                @endphp
                <a href="{{ route('courses.all', array_merge(request()->only('search'), ['category' => $cat])) }}"
                    class="px-5 py-2.5 rounded-lg font-medium whitespace-nowrap transition shadow-sm flex items-center gap-2
                                  {{ $currentCategory === $cat ? 'bg-pnj-teal text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                    <span>{{ $cat }}</span>
                    <span
                        class="text-xs px-2 py-0.5 rounded-full {{ $currentCategory === $cat ? 'bg-white/20' : 'bg-gray-100 text-gray-600' }}">
                        {{ $count }}
                    </span>
                </a>
            @endforeach
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($courses as $course)
                <a href="{{ route('courses.show', $course) }}"
                    class="bg-white rounded-xl shadow-sm hover:shadow-xl border border-gray-100 hover:-translate-y-1 transition overflow-hidden group cursor-pointer">
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
                    </div>
                    <div class="p-4 space-y-2">
                        <h4 class="font-bold text-gray-900 line-clamp-2 group-hover:text-pnj-teal transition-colors">
                            {{ $course->judul }}
                        </h4>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-user-tie"></i>
                            <span>{{ $course->instructor->name ?? $course->pembuat->name ?? 'Instruktur' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <span><i class="fas fa-user-graduate mr-1"></i>{{ $course->students_count ?? 0 }} Siswa</span>
                            <span class="text-gray-300">|</span>
                            <span><i class="fas fa-video mr-1"></i>{{ $course->videos_count ?? 0 }} Video</span>
                            <span class="text-gray-300">|</span>
                            <span>{{ $course->materi_count ?? 0 }} Materi</span>
                        </div>
                        @php
                            $basePrice = $course->harga ?? $course->price ?? 0;
                            $finalPrice = ($course->discount_price ?? 0) > 0 ? $course->discount_price : $basePrice;
                        @endphp
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-bold text-pnj-teal">Rp
                                {{ number_format($finalPrice, 0, ',', '.') }}</span>
                            @if(($course->discount_price ?? 0) > 0)
                                <span class="text-sm text-gray-500 line-through">Rp
                                    {{ number_format($basePrice, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full bg-white border border-gray-200 rounded-xl p-10 text-center text-gray-600">
                    <i class="fas fa-search text-3xl text-gray-400"></i>
                    <p class="font-semibold text-gray-800 mt-2">Kursus tidak ditemukan</p>
                    <p class="text-sm text-gray-500">Coba gunakan kategori lain atau reset pencarian.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $courses->withQueryString()->links() }}
        </div>
    </div>
@endsection