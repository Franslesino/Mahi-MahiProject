@extends('layouts.admin')

@section('content')
<div class="p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kelola Banner Promo</h2>
        <a href="{{ route('admin.promo-banners.create') }}" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Tambah Banner</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm">
        @if($banners->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Urutan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preview</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Badge</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($banners as $banner)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="text-lg font-bold text-gray-700">{{ $banner->order }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="w-32 h-16 rounded-lg bg-gradient-to-r from-{{ $banner->gradient_from }} to-{{ $banner->gradient_to }} flex items-center justify-center text-white text-xs font-semibold">
                                Preview
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ $banner->title }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($banner->description, 50) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($banner->badge)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">
                                {{ $banner->badge }}
                            </span>
                            @else
                            <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($banner->start_date || $banner->end_date)
                            <div class="flex flex-col gap-1">
                                <span class="text-xs">{{ $banner->start_date ? $banner->start_date->format('d M Y') : 'Selamanya' }}</span>
                                <span class="text-xs text-gray-400">s/d</span>
                                <span class="text-xs">{{ $banner->end_date ? $banner->end_date->format('d M Y') : 'Selamanya' }}</span>
                            </div>
                            @else
                            <span class="text-gray-400 text-sm">Permanen</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($banner->is_active)
                                @if($banner->isValid())
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                                @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                    <i class="fas fa-clock"></i> Terjadwal
                                </span>
                                @endif
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                <i class="fas fa-times-circle"></i> Nonaktif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.promo-banners.show', $banner->id) }}" 
                                   class="text-gray-600 hover:text-gray-800" 
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.promo-banners.edit', $banner->id) }}" 
                                   class="text-blue-600 hover:text-blue-800"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.promo-banners.toggle', $banner->id) }}" 
                                      method="POST" 
                                      class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="text-amber-600 hover:text-amber-800"
                                            title="{{ $banner->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.promo-banners.destroy', $banner->id) }}" 
                                      method="POST" 
                                      class="inline"
                                      data-confirm-title="Hapus banner?"
                                      data-confirm="Yakin ingin menghapus banner ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-800"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4">
            {{ $banners->links() }}
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-16 px-6">
            <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg font-semibold">Belum ada banner promo</p>
            <p class="text-gray-400 text-sm mt-2 text-center">Buat banner promo pertama untuk ditampilkan di halaman utama</p>
            <a href="{{ route('admin.promo-banners.create') }}" class="mt-4 px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600">
                Tambah Banner
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
