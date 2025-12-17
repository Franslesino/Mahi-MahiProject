@extends('layouts.admin')

@section('content')
<div class="p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kelola Voucher</h2>
        <a href="{{ route('admin.vouchers.create') }}" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Tambah Voucher</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm">
        @if($vouchers->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Berlaku Untuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penggunaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($vouchers as $voucher)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-mono font-bold text-emerald-600">{{ $voucher->code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ $voucher->name }}</p>
                            @if($voucher->description)
                            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($voucher->description, 50) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($voucher->type === 'percentage')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                <i class="fas fa-percent"></i> Persentase
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">
                                <i class="fas fa-money-bill"></i> Tetap
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($voucher->type === 'percentage')
                            <span class="font-bold text-gray-800">{{ $voucher->value }}%</span>
                            @else
                            <span class="font-bold text-gray-800">Rp {{ number_format($voucher->value, 0, ',', '.') }}</span>
                            @endif
                            @if($voucher->max_discount)
                            <p class="text-xs text-gray-500">Max: Rp {{ number_format($voucher->max_discount, 0, ',', '.') }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($voucher->allowed_courses && count($voucher->allowed_courses) > 0)
                                @php
                                    $courseCount = count($voucher->allowed_courses);
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">
                                    <i class="fas fa-filter"></i> {{ $courseCount }} Kursus
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                    <i class="fas fa-check-double"></i> Semua Kursus
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-800">{{ $voucher->used_count }}</span>
                            @if($voucher->max_usage)
                            <span class="text-sm text-gray-500">/ {{ $voucher->max_usage }}</span>
                            @else
                            <span class="text-sm text-gray-500">/ ∞</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs">{{ $voucher->start_date ? $voucher->start_date->format('d M Y') : '-' }}</span>
                                <span class="text-xs text-gray-400">s/d</span>
                                <span class="text-xs">{{ $voucher->end_date ? $voucher->end_date->format('d M Y') : '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if(!$voucher->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                <i class="fas fa-times-circle"></i> Nonaktif
                            </span>
                            @elseif($voucher->is_active && !$voucher->end_date)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                <i class="fas fa-infinity"></i> Aktif Permanen
                            </span>
                            @elseif($voucher->is_active && $voucher->end_date && $voucher->end_date->isFuture())
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                <i class="fas fa-check-circle"></i> Aktif
                            </span>
                            @elseif($voucher->end_date && $voucher->end_date->isPast())
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                <i class="fas fa-clock"></i> Expired
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                <i class="fas fa-question-circle"></i> Unknown
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.vouchers.show', $voucher->id) }}" 
                                   class="text-gray-600 hover:text-gray-800" 
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" 
                                   class="text-blue-600 hover:text-blue-800"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.vouchers.toggle', $voucher->id) }}" 
                                      method="POST" 
                                      class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="text-amber-600 hover:text-amber-800"
                                            title="{{ $voucher->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" 
                                      method="POST" 
                                      class="inline"
                                      data-confirm="Yakin ingin menghapus voucher ini?">
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
            {{ $vouchers->links() }}
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-16 px-6">
            <i class="fas fa-ticket-alt text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg font-semibold">Belum ada voucher</p>
            <p class="text-gray-400 text-sm mt-2 text-center">Buat voucher pertama untuk memberikan diskon pada kursus</p>
            <a href="{{ route('admin.vouchers.create') }}" class="mt-4 px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600">
                Tambah Voucher
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
