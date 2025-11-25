@extends('layouts.admin')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <a href="{{ route('admin.vouchers.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2 mb-4">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Daftar Voucher</span>
        </a>
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">Detail Voucher</h2>
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.vouchers.toggle', $voucher->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" 
                            class="px-4 py-2 {{ $voucher->is_active ? 'bg-gray-500 hover:bg-gray-600' : 'bg-green-500 hover:bg-green-600' }} text-white rounded-lg flex items-center gap-2">
                        <i class="fas fa-power-off"></i>
                        <span>{{ $voucher->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                    </button>
                </form>
                <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" 
                   class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                @if($voucher->used_count == 0)
                <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" 
                      method="POST" 
                      class="inline"
                      onsubmit="return confirm('Yakin ingin menghapus voucher ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center gap-2">
                        <i class="fas fa-trash"></i>
                        <span>Hapus</span>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
        <span>{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <!-- Informasi Voucher -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-ticket-alt text-emerald-500"></i>
            <span>Informasi Voucher</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Kode Voucher</p>
                <p class="text-2xl font-bold text-emerald-600 font-mono">{{ $voucher->code }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 mb-1">Nama Voucher</p>
                <p class="text-lg font-semibold text-gray-800">{{ $voucher->name }}</p>
            </div>

            @if($voucher->description)
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Deskripsi</p>
                <p class="text-gray-700">{{ $voucher->description }}</p>
            </div>
            @endif

            <div>
                <p class="text-sm text-gray-500 mb-1">Tipe Diskon</p>
                @if($voucher->type === 'percentage')
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-700 inline-block">
                    <i class="fas fa-percent"></i> Persentase
                </span>
                @else
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-700 inline-block">
                    <i class="fas fa-money-bill"></i> Nominal Tetap
                </span>
                @endif
            </div>

            <div>
                <p class="text-sm text-gray-500 mb-1">Nilai Diskon</p>
                @if($voucher->type === 'percentage')
                <p class="text-xl font-bold text-gray-800">{{ $voucher->value }}%</p>
                @else
                <p class="text-xl font-bold text-gray-800">Rp {{ number_format($voucher->value, 0, ',', '.') }}</p>
                @endif
            </div>

            @if($voucher->max_discount)
            <div>
                <p class="text-sm text-gray-500 mb-1">Maksimal Diskon</p>
                <p class="text-lg font-semibold text-gray-800">Rp {{ number_format($voucher->max_discount, 0, ',', '.') }}</p>
            </div>
            @endif

            @if($voucher->min_purchase)
            <div>
                <p class="text-sm text-gray-500 mb-1">Minimal Pembelian</p>
                <p class="text-lg font-semibold text-gray-800">Rp {{ number_format($voucher->min_purchase, 0, ',', '.') }}</p>
            </div>
            @endif

            <div>
                <p class="text-sm text-gray-500 mb-1">Penggunaan</p>
                <p class="text-lg font-semibold text-gray-800">
                    {{ $voucher->used_count }}
                    @if($voucher->max_usage)
                    / {{ $voucher->max_usage }}
                    @else
                    / ∞
                    @endif
                </p>
                @if($voucher->max_usage && $voucher->used_count >= $voucher->max_usage)
                <p class="text-xs text-red-500 mt-1">Kuota voucher sudah habis</p>
                @endif
            </div>

            <div>
                <p class="text-sm text-gray-500 mb-1">Status</p>
                @if(!$voucher->is_active)
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-700 inline-block">
                    <i class="fas fa-times-circle"></i> Nonaktif
                </span>
                @elseif($voucher->is_active && !$voucher->end_date)
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-700 inline-block">
                    <i class="fas fa-infinity"></i> Aktif Permanen
                </span>
                @elseif($voucher->is_active && $voucher->end_date && $voucher->end_date->isFuture())
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-700 inline-block">
                    <i class="fas fa-check-circle"></i> Aktif
                </span>
                @elseif($voucher->end_date && $voucher->end_date->isPast())
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-700 inline-block">
                    <i class="fas fa-clock"></i> Expired
                </span>
                @else
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-700 inline-block">
                    <i class="fas fa-question-circle"></i> Unknown
                </span>
                @endif
            </div>

            <div>
                <p class="text-sm text-gray-500 mb-1">Periode Berlaku</p>
                <p class="text-gray-800">
                    {{ $voucher->start_date ? $voucher->start_date->format('d M Y') : '-' }} 
                    @if($voucher->end_date)
                        - {{ $voucher->end_date->format('d M Y') }}
                    @else
                        <span class="text-blue-600 font-semibold">- Tidak Terbatas</span>
                    @endif
                </p>
                @if($voucher->end_date)
                <p class="text-xs text-gray-500 mt-1">
                    @if($voucher->end_date->isFuture())
                        Berakhir dalam {{ $voucher->end_date->diffForHumans() }}
                    @else
                        Sudah berakhir {{ $voucher->end_date->diffForHumans() }}
                    @endif
                </p>
                @else
                <p class="text-xs text-blue-600 mt-1">Voucher berlaku selamanya</p>
                @endif
            </div>

            <div>
                <p class="text-sm text-gray-500 mb-1">Dibuat</p>
                <p class="text-gray-800">{{ $voucher->created_at->format('d M Y H:i') }}</p>
            </div>

            @if($voucher->allowed_courses && count($voucher->allowed_courses) > 0)
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-2">Kursus yang Diizinkan</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(\App\Models\Kursus::whereIn('id', $voucher->allowed_courses)->get() as $course)
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">
                        {{ $course->judul }}
                    </span>
                    @endforeach
                </div>
            </div>
            @else
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Kursus yang Diizinkan</p>
                <p class="text-gray-800">Semua kursus</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Riwayat Penggunaan -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-history text-emerald-500"></i>
            <span>Riwayat Penggunaan ({{ $voucher->usages->count() }})</span>
        </h3>

        @if($voucher->usages->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Transaksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diskon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($voucher->usages as $usage)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            @if($usage->user)
                            <p class="font-medium text-gray-800">{{ $usage->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $usage->user->email }}</p>
                            @else
                            <p class="text-gray-400">User tidak ditemukan</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($usage->transaction)
                            <a href="{{ route('admin.transactions.show', $usage->transaction->id) }}" 
                               class="text-blue-600 hover:text-blue-800 font-mono text-sm">
                                {{ $usage->transaction->transaction_code }}
                            </a>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-green-600">
                                Rp {{ number_format($usage->discount_amount, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $usage->used_at ? $usage->used_at->format('d M Y H:i') : ($usage->created_at ? $usage->created_at->format('d M Y H:i') : '-') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8">
            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">Voucher ini belum pernah digunakan</p>
        </div>
        @endif
    </div>
</div>
@endsection
