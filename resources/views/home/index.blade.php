@extends('layouts.app')

@section('title', 'Beranda - Pelayanan TIK PNJ')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Promo Banner -->
    <div class="mb-8 bg-gradient-to-r from-teal-600 to-teal-700 rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="relative z-10 max-w-md">
            <div class="inline-block bg-white/20 px-3 py-1 rounded-full text-sm font-semibold mb-3">
                35% OFF
            </div>
            <h2 class="text-3xl font-bold mb-2">PNJ SPECIAL</h2>
            <p class="text-teal-50 mb-4 leading-relaxed">Dapatkan Voucher Kode Bagi<br>Mahasiswa Politeknik Negeri Jakarta</p>
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
        <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow overflow-hidden group cursor-pointer">
            <div class="relative h-40 bg-gray-900 overflow-hidden">
                <img src="{{ $course->image }}" 
                     alt="{{ $course->title }}" 
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                <div class="absolute top-3 left-3">
                    <span class="bg-teal-500 text-white text-xs px-2 py-1 rounded-full font-semibold">
                        {{ $course->category }}
                    </span>
                </div>
                @if($course->badge)
                <div class="absolute top-3 right-3">
                    <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full font-semibold">
                        {{ $course->badge }}
                    </span>
                </div>
                @endif
            </div>
            <div class="p-4">
                <h4 class="font-bold text-gray-900 mb-2 line-clamp-2">{{ $course->title }}</h4>
                <div class="flex items-center gap-2 text-sm text-gray-600 mb-3">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $course->videos }} Video</span>
                    <span class="text-gray-400">•</span>
                    <span>{{ $course->mode }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold text-pnj-blue">
                        Rp {{ number_format($course->price, 0, ',', '.') }}
                    </span>
                    <div class="flex items-center gap-1 text-yellow-500">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                        <span class="text-sm text-gray-600">{{ $course->rating }}</span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p class="text-gray-500 text-lg">Belum ada kursus tersedia</p>
        </div>
        @endforelse
    </div>
    
    <!-- Load More Button -->
    @if(count($courses) > 0)
    <div class="text-center mt-12">
        <button class="bg-white text-pnj-blue border-2 border-pnj-blue px-8 py-3 rounded-lg font-semibold hover:bg-pnj-blue hover:text-white transition-all shadow-sm">
            Lihat Lebih Banyak
        </button>
    </div>
    @endif
    
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