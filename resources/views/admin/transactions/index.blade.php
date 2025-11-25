{{-- resources/views/admin/transactions/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Kelola Transaksi</h2>
        <p class="text-gray-600">Pantau dan kelola semua transaksi pembelian kursus</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Transaksi</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-receipt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Berhasil</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['paid'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Pendapatan</p>
                    <p class="text-xl font-bold text-emerald-600">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wallet text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Cards -->
    <div class="grid grid-cols-1 gap-6 mb-8">


        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-sm p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm mb-1">Pendapatan Hari Ini</p>
                    <p class="text-3xl font-bold">Rp {{ number_format($stats['today_revenue'], 0, ',', '.') }}</p>
                </div>
                <i class="fas fa-calendar-day text-4xl opacity-20"></i>
            </div>
        </div>

        
    </div>

    <!-- Filters & Table -->
    <div class="bg-white rounded-lg shadow-sm">
        <!-- Filters -->
        <div class="px-6 py-4 border-b border-gray-200">
            <form method="GET" class="flex flex-wrap gap-3">
                <!-- Search -->
                <input type="text" 
                       name="search" 
                       placeholder="Cari kode transaksi atau nama pengguna..." 
                       value="{{ request('search') }}"
                       class="flex-1 min-w-[250px] px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">

                <!-- Status Filter -->
                <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>

                <!-- Payment Method Filter -->
                <select name="payment_method" class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Metode</option>
                    <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="e_wallet" {{ request('payment_method') === 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                    <option value="virtual_account" {{ request('payment_method') === 'virtual_account' ? 'selected' : '' }}>Virtual Account</option>
                    <option value="credit_card" {{ request('payment_method') === 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                </select>

                <!-- Date From -->
                <input type="date" 
                       name="date_from" 
                       value="{{ request('date_from') }}"
                       class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">

                <!-- Date To -->
                <input type="date" 
                       name="date_to" 
                       value="{{ request('date_to') }}"
                       class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">

                <!-- Buttons -->
                <button type="submit" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                
                
            </form>
        </div>

        <!-- Transactions Table -->
        @if($transactions->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Transaksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kursus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($transactions as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm font-semibold text-blue-600">
                                {{ $transaction->transaction_code }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-800">{{ $transaction->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $transaction->user->email }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs">
                                <p class="font-medium text-gray-800 truncate">
                                    {{ $transaction->kursus->judul ?? $transaction->kursus->title }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ $transaction->kursus->kategori ?? $transaction->kursus->category }}
                                </p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-semibold text-gray-800">
                                    Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}
                                </p>
                                @if($transaction->diskon_persen > 0)
                                <p class="text-xs text-green-600">
                                    Diskon {{ $transaction->diskon_persen }}%
                                </p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-gray-800">
                                    {{ ucwords(str_replace('_', ' ', $transaction->payment_method ?? '-')) }}
                                </p>
                                @if($transaction->payment_channel)
                                <p class="text-xs text-gray-500">{{ $transaction->payment_channel }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusConfig = [
                                    'pending' => ['class' => 'bg-yellow-100 text-yellow-700', 'icon' => 'clock', 'text' => 'Pending'],
                                    'paid' => ['class' => 'bg-green-100 text-green-700', 'icon' => 'check-circle', 'text' => 'Paid'],
                                    'expired' => ['class' => 'bg-gray-100 text-gray-700', 'icon' => 'times-circle', 'text' => 'Expired'],
                                    'cancelled' => ['class' => 'bg-red-100 text-red-700', 'icon' => 'ban', 'text' => 'Cancelled'],
                                    'refunded' => ['class' => 'bg-purple-100 text-purple-700', 'icon' => 'undo', 'text' => 'Refunded'],
                                ];
                                $status = $statusConfig[$transaction->status] ?? ['class' => 'bg-gray-100 text-gray-700', 'icon' => 'question', 'text' => $transaction->status];
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $status['class'] }} inline-flex items-center">
                                <i class="fas fa-{{ $status['icon'] }} mr-1"></i>
                                {{ $status['text'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div>
                                <p>{{ $transaction->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-gray-400">{{ $transaction->created_at->format('H:i') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.transactions.show', $transaction) }}" 
                                   class="text-blue-600 hover:text-blue-800"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if(in_array($transaction->status, ['cancelled', 'expired']))
                                <form action="{{ route('admin.transactions.destroy', $transaction) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-800"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4">
            {{ $transactions->links() }}
        </div>
        @else
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center py-16 px-6">
            <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg font-semibold">Belum ada transaksi</p>
            <p class="text-gray-400 text-sm mt-2 text-center">Transaksi pembelian kursus akan ditampilkan di sini</p>
        </div>
        @endif
    </div>
</div>
@endsection