{{-- resources/views/admin/transactions/show.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="p-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.transactions.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Transaksi
        </a>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Detail Transaksi</h2>
        <p class="text-gray-600">{{ $transaction->transaction_code }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Transaction Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-file-invoice text-blue-600 mr-2"></i>
                    Informasi Transaksi
                </h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between py-3 border-b">
                        <span class="text-gray-600 font-medium">Kode Transaksi</span>
                        <span class="text-gray-900 font-mono font-bold">{{ $transaction->transaction_code }}</span>
                    </div>
                    
                    <div class="flex justify-between py-3 border-b">
                        <span class="text-gray-600 font-medium">Status</span>
                        <div>
                            @php
                                $statusConfig = [
                                    'pending' => ['class' => 'bg-yellow-100 text-yellow-700', 'icon' => 'clock', 'text' => 'Pending'],
                                    'paid' => ['class' => 'bg-green-100 text-green-700', 'icon' => 'check-circle', 'text' => 'Paid'],
                                    'expired' => ['class' => 'bg-gray-100 text-gray-700', 'icon' => 'times-circle', 'text' => 'Expired'],
                                    'cancelled' => ['class' => 'bg-red-100 text-red-700', 'icon' => 'ban', 'text' => 'Cancelled'],
                                ];
                                $status = $statusConfig[$transaction->status] ?? ['class' => 'bg-gray-100 text-gray-700', 'icon' => 'question', 'text' => $transaction->status];
                            @endphp
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $status['class'] }} inline-flex items-center">
                                <i class="fas fa-{{ $status['icon'] }} mr-1"></i>
                                {{ $status['text'] }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between py-3 border-b">
                        <span class="text-gray-600 font-medium">Tanggal Dibuat</span>
                        <span class="text-gray-900 font-semibold">
                            {{ $transaction->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}
                        </span>
                    </div>
                    
                    @if($transaction->payment_deadline)
                    <div class="flex justify-between py-3 border-b">
                        <span class="text-gray-600 font-medium">Batas Pembayaran</span>
                        <span class="text-red-600 font-semibold">
                            {{ $transaction->payment_deadline->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}
                        </span>
                    </div>
                    @endif
                    
                    @if($transaction->paid_at)
                    <div class="flex justify-between py-3 border-b">
                        <span class="text-gray-600 font-medium">Dibayar Pada</span>
                        <span class="text-green-600 font-semibold">
                            {{ $transaction->paid_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- User Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user text-blue-600 mr-2"></i>
                    Informasi Pembeli
                </h3>
                
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-xl">{{ substr($transaction->user->name, 0, 2) }}</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900 mb-1">{{ $transaction->user->name }}</h4>
                        <p class="text-sm text-gray-600 mb-2">{{ $transaction->user->email }}</p>
                        @if($transaction->user->no_telepon)
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-phone mr-1"></i>
                            {{ $transaction->user->no_telepon }}
                        </p>
                        @endif
                        <a href="{{ route('admin.users.show', $transaction->user) }}" 
                           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 mt-2">
                            Lihat Profil Lengkap
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Course Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-graduation-cap text-blue-600 mr-2"></i>
                    Kursus yang Dibeli
                </h3>
                
                <div class="flex gap-4">
                    @if($transaction->kursus->image)
                        <img src="{{ asset('storage/' . $transaction->kursus->image) }}" 
                             alt="{{ $transaction->kursus->judul ?? $transaction->kursus->title }}" 
                             class="w-32 h-24 object-cover rounded-lg flex-shrink-0">
                    @else
                        <div class="w-32 h-24 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex-shrink-0 flex items-center justify-center">
                            <i class="fas fa-book text-white text-3xl opacity-50"></i>
                        </div>
                    @endif
                    
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900 mb-1">
                            {{ $transaction->kursus->judul ?? $transaction->kursus->title }}
                        </h4>
                        <p class="text-sm text-gray-600 mb-2">
                            Oleh {{ $transaction->kursus->pembuat->name ?? 'Instruktur' }}
                        </p>
                        <div class="flex items-center gap-4 text-sm text-gray-600 mb-2">
                            <span>
                                <i class="fas fa-tag mr-1"></i>
                                {{ $transaction->kursus->kategori ?? $transaction->kursus->category }}
                            </span>
                            <span>
                                <i class="fas fa-video mr-1"></i>
                                {{ $transaction->kursus->materi_count ?? 0 }} Materi
                            </span>
                        </div>
                        <a href="{{ route('admin.courses.show', $transaction->kursus) }}" 
                           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
                            Lihat Detail Kursus
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                    Detail Pembayaran
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-lg">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-{{ 
                                $transaction->payment_method === 'bank_transfer' ? 'university' : 
                                ($transaction->payment_method === 'e_wallet' ? 'mobile-alt' : 
                                ($transaction->payment_method === 'credit_card' ? 'credit-card' : 'receipt'))
                            }} text-white text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">
                                {{ ucwords(str_replace('_', ' ', $transaction->payment_method ?? '-')) }}
                            </div>
                            @if($transaction->payment_channel)
                            <div class="text-sm text-gray-600">{{ $transaction->payment_channel }}</div>
                            @endif
                        </div>
                    </div>

                    @if($transaction->payment_details)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Detail Tambahan:</h4>
                        <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ $transaction->payment_details }}</pre>
                    </div>
                    @endif

                    @if($transaction->notes)
                    <div class="p-4 bg-yellow-50 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Catatan:</h4>
                        <p class="text-sm text-gray-700">{{ $transaction->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Price Summary -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6 sticky top-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Harga</h3>
                
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between text-gray-700">
                        <span>Harga Kursus</span>
                        <span class="font-semibold">Rp {{ number_format($transaction->harga_asli, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($transaction->harga_diskon && $transaction->diskon_persen > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Diskon ({{ $transaction->diskon_persen }}%)</span>
                            <span class="font-semibold">- Rp {{ number_format($transaction->harga_asli - $transaction->harga_diskon, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="border-t pt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-900">Total</span>
                            <span class="text-2xl font-bold text-blue-900">
                                Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                @if($transaction->status === 'pending')
                <form action="{{ route('admin.transactions.updateStatus', $transaction) }}" method="POST" class="mb-3">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="paid">
                    <button type="submit" 
                            onclick="return confirm('Konfirmasi pembayaran ini?')"
                            class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                        <i class="fas fa-check mr-2"></i>
                        Konfirmasi Pembayaran
                    </button>
                </form>

                <form action="{{ route('admin.transactions.updateStatus', $transaction) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" 
                            onclick="return confirm('Batalkan transaksi ini?')"
                            class="w-full px-4 py-3 border-2 border-red-500 text-red-600 rounded-lg hover:bg-red-50 transition font-semibold">
                        <i class="fas fa-times mr-2"></i>
                        Batalkan Transaksi
                    </button>
                </form>
                @elseif($transaction->status === 'paid')
                <div class="p-4 bg-green-50 rounded-lg text-center">
                    <i class="fas fa-check-circle text-green-600 text-3xl mb-2"></i>
                    <p class="text-green-800 font-semibold">Pembayaran Berhasil</p>
                </div>

                <form action="{{ route('admin.transactions.updateStatus', $transaction) }}" method="POST" class="mt-3">
                    @csrf
                    @method('PATCH')
                   
                </form>
                @endif

                @if(in_array($transaction->status, ['cancelled', 'expired']))
                <form action="{{ route('admin.transactions.destroy', $transaction) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Hapus transaksi ini secara permanen?')"
                            class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Transaksi
                    </button>
                </form>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection