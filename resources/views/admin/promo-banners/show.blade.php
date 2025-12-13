@extends('layouts.admin')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <a href="{{ route('admin.promo-banners.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2 mb-4">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Daftar Banner</span>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Detail Banner Promo</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Preview Banner -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Preview Banner</h3>
                
                <div class="bg-gradient-to-r from-{{ $promoBanner->gradient_from }} to-{{ $promoBanner->gradient_to }} p-8 text-white relative overflow-hidden rounded-lg">
                    <div class="absolute inset-0 bg-black/10 md:bg-black/15"></div>
                    <div class="relative z-10 max-w-md">
                        @if($promoBanner->badge)
                        <div class="inline-block bg-white/20 px-3 py-1 rounded-full text-sm font-semibold mb-3">
                            {{ $promoBanner->badge }}
                        </div>
                        @endif
                        <h2 class="text-3xl font-bold mb-2">{{ $promoBanner->title }}</h2>
                        <p class="text-white/90 mb-4 leading-relaxed">
                            {!! nl2br(e($promoBanner->description)) !!}
                        </p>
                        @if($promoBanner->button_text)
                        <button class="inline-flex items-center gap-2 bg-white text-gray-900 px-6 py-3 rounded-xl font-semibold hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200 shadow-sm text-sm md:text-base">
                            {{ $promoBanner->button_text }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        @endif
                    </div>
                    <div class="absolute right-0 top-0 w-40 h-40 bg-white/20 rounded-full -mr-20 -mt-10"></div>
                    <div class="absolute right-20 bottom-0 w-32 h-32 bg-white/10 rounded-full -mb-10"></div>
                </div>
            </div>
        </div>

        <!-- Detail Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Informasi Banner</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Status</p>
                        @if($promoBanner->is_active)
                            @if($promoBanner->isValid())
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-700">
                                <i class="fas fa-check-circle"></i> Aktif
                            </span>
                            @else
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                <i class="fas fa-clock"></i> Terjadwal
                            </span>
                            @endif
                        @else
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-700">
                            <i class="fas fa-times-circle"></i> Nonaktif
                        </span>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 mb-1">Urutan Tampil</p>
                        <p class="font-semibold">{{ $promoBanner->order }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 mb-1">Badge</p>
                        <p class="font-semibold">{{ $promoBanner->badge ?: '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 mb-1">Link Tombol</p>
                        @if($promoBanner->button_link)
                        <a href="{{ $promoBanner->button_link }}" target="_blank" class="text-blue-600 hover:underline text-sm break-all">
                            {{ $promoBanner->button_link }}
                        </a>
                        @else
                        <p class="text-gray-400">-</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 mb-1">Gradient</p>
                        <div class="flex gap-2 items-center">
                            <div class="w-8 h-8 rounded bg-{{ $promoBanner->gradient_from }}"></div>
                            <span class="text-gray-400">→</span>
                            <div class="w-8 h-8 rounded bg-{{ $promoBanner->gradient_to }}"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $promoBanner->gradient_from }} → {{ $promoBanner->gradient_to }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 mb-1">Periode Berlaku</p>
                        @if($promoBanner->start_date || $promoBanner->end_date)
                        <p class="text-sm">
                            {{ $promoBanner->start_date ? $promoBanner->start_date->format('d M Y') : 'Selamanya' }}
                            <br>
                            <span class="text-gray-400">sampai</span>
                            <br>
                            {{ $promoBanner->end_date ? $promoBanner->end_date->format('d M Y') : 'Selamanya' }}
                        </p>
                        @else
                        <p class="font-semibold text-blue-600">Permanen</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 mb-1">Dibuat</p>
                        <p class="text-sm">{{ $promoBanner->created_at->format('d M Y H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 mb-1">Terakhir Diubah</p>
                        <p class="text-sm">{{ $promoBanner->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-2">
                    <a href="{{ route('admin.promo-banners.edit', $promoBanner->id) }}" 
                       class="w-full text-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-semibold">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Banner
                    </a>
                    
                    <form action="{{ route('admin.promo-banners.toggle', $promoBanner->id) }}" 
                          method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-semibold">
                            <i class="fas fa-power-off mr-2"></i>
                            {{ $promoBanner->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.promo-banners.destroy', $promoBanner->id) }}" 
                          method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus banner ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 font-semibold">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus Banner
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
