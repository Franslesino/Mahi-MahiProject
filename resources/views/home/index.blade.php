@extends('layouts.app')

@section('title', 'Semua Kursus - EDUQUEST')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Promo Banner -->
    <div class="mb-8 bg-gradient-to-r from-teal-600 to-teal-700 rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="relative z-10 max-w-md">
            <div class="inline-block bg-white/20 px-3 py-1 rounded-full text-sm font-semibold mb-3">
                35% OFF
            </div>
            <h2 class="text-3xl font-bold mb-2">PNJ SPECIAL</h2>
            <p class="text-teal-50 mb-4 leading-relaxed">
                Dapatkan Voucher Kode Bagi<br>Mahasiswa Politeknik Negeri Jakarta
            </p>
            <button class="bg-white text-teal-700 px-6 py-2 rounded-lg font-semibold hover:bg-teal-50 transition shadow-sm">
                Dapatkan Sekarang
            </button>
        </div>
        <div class="absolute right-0 top-0 w-40 h-40 bg-teal-500/30 rounded-full -mr-20 -mt-10"></div>
        <div class="absolute right-20 bottom-0 w-32 h-32 bg-teal-400/20 rounded-full -mb-10"></div>
    </div>

    <!-- Categories -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Kategori</h3>
            <a href="#" class="text-pnj-blue font-semibold text-sm hover:underline flex items-center gap-1">
                LIHAT SEMUA
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="flex gap-3 overflow-x-auto pb-2">
            <button class="px-6 py-2 rounded-lg font-medium whitespace-nowrap transition shadow-sm bg-pnj-blue text-white">
                Semua
            </button>
            <button class="px-6 py-2 rounded-lg font-medium whitespace-nowrap transition shadow-sm bg-white text-gray-700 hover:bg-gray-50">
                3D Design
            </button>
            <button class="px-6 py-2 rounded-lg font-medium whitespace-nowrap transition shadow-sm bg-white text-gray-700 hover:bg-gray-50">
                Data Analytic
            </button>
            <button class="px-6 py-2 rounded-lg font-medium whitespace-nowrap transition shadow-sm bg-white text-gray-700 hover:bg-gray-50">
                Graphic Design
            </button>
            <button class="px-6 py-2 rounded-lg font-medium whitespace-nowrap transition shadow-sm bg-white text-gray-700 hover:bg-gray-50">
                Web Development
            </button>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($courses as $course)
        <a href="{{ route('courses.show', $course) }}"
           class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow overflow-hidden group cursor-pointer">
            
            <!-- Image -->
            <div class="relative h-40 bg-gray-900 overflow-hidden">
                @if($course->image)
                    <img src="{{ asset('storage/' . $course->image) }}"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white text-5xl opacity-50"></i>
                    </div>
                @endif

                <!-- Category -->
                <div class="absolute top-3 left-3">
                    <span class="bg-teal-500 text-white text-xs px-2 py-1 rounded-full font-semibold">
                        {{ $course->category ?? 'General' }}
                    </span>
                </div>

                <!-- Badge -->
                @if($course->badge)
                <div class="absolute top-3 right-3">
                    <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full font-semibold">
                        {{ $course->badge }}
                    </span>
                </div>
                @endif
            </div>

            <!-- Info Card -->
            <div class="p-4">
                <h4 class="font-bold text-gray-900 mb-2 line-clamp-2">{{ $course->title }}</h4>

                <div class="flex items-center gap-2 text-sm text-gray-600 mb-3">
                    <i class="fas fa-user-tie"></i>
                    <span>{{ $course->instructor->name ?? 'Instruktur' }}</span>
                </div>

                <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                    <span><i class="fas fa-video mr-1"></i>{{ $course->videos }} Video</span>
                    <span class="text-gray-400">•</span>
                    <span>{{ $course->materials_count }} Materi</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold text-pnj-blue">
                        Rp {{ number_format($course->price, 0, ',', '.') }}
                    </span>

                    <div class="flex items-center gap-1 text-yellow-500">
                        <i class="fas fa-star text-yellow-500"></i>
                        <span class="text-sm text-gray-600">{{ number_format($course->rating, 1) }}</span>
                    </div>
                </div>
            </div>

        </a>
        @empty
        <div class="col-span-full text-center py-12">
            <p class="text-gray-500 text-lg">Belum ada kursus tersedia</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
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
    .pnj-blue {
        color: #003d82;
    }
</style>
@endsection
