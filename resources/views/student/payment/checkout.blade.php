{{-- resources/views/student/payment/checkout.blade.php --}}
@extends('layouts.app')

@php
    // Data untuk tampilan
    $judul        = $course->judul ?? $course->title ?? '';
    $hargaAsli    = $course->harga ?? $course->price ?? 0;
    $hargaDiskon  = $course->discount_price && $course->discount_price > 0
                    ? $course->discount_price
                    : null;
    
    $hargaTampil  = $hargaDiskon ?? $hargaAsli;
    $diskon       = $hargaDiskon ? ($hargaAsli - $hargaDiskon) : 0;
    
    // Pajak (10% dari harga setelah diskon)
    $pajak        = $hargaTampil * 0.1;
    $totalBayar   = $hargaTampil + $pajak;
@endphp

@section('title', 'Pembayaran - ' . $judul)

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('courses.show', $course) }}" class="text-teal-600 hover:text-teal-700 font-semibold flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Kursus
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Section - Detail Pembayaran -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Detail Data User -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Detail Data User</h2>
                    
                    <div class="space-y-5">
                        <!-- Email -->
                        <div>
                            <label class="text-sm font-semibold text-gray-700">Email</label>
                            <p class="text-gray-900 font-medium mt-1">{{ auth()->user()->email }}</p>
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <label class="text-sm font-semibold text-gray-700">Nama Lengkap</label>
                            <p class="text-gray-900 font-medium mt-1">{{ auth()->user()->name ?? '-' }}</p>
                        </div>

                        <!-- Negara -->
                        <div>
                            <label class="text-sm font-semibold text-gray-700">Negara</label>
                            <div class="mt-1">
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 bg-white focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                    <option selected>Indonesia</option>
                                    <option>Malaysia</option>
                                    <option>Singapura</option>
                                    <option>Thailand</option>
                                </select>
                            </div>
                        </div>

                        <!-- Nomor Telepon -->
                        <div>
                            <label class="text-sm font-semibold text-gray-700">Nomor Telepon</label>
                            <p class="text-gray-900 font-medium mt-1">{{ auth()->user()->phone ?? '082143253412' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Metode Pembayaran -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Metode Pembayaran</h2>
                    
                    <div class="space-y-4">
                        <!-- BNI Bank Card -->
                        <div class="border-2 border-teal-500 rounded-lg p-6 bg-teal-50">
                            <!-- Bank Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-4">
                                    <!-- Bank Logo/Image -->
                                    <div class="w-16 h-16 bg-white rounded-lg flex items-center justify-center flex-shrink-0 border border-gray-200">
                                        <img src="https://images.seeklogo.com/logo-png/35/1/bank-bni-logo-png_seeklogo-355606.png" alt="BNI" class="w-12 h-12 object-contain">
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-lg">Bank Negara Indonesia (BNI)</h3>
                                        <p class="text-sm text-gray-600">Transfer Bank</p>
                                    </div>
                                </div>
                                <input type="radio" name="metode_pembayaran" value="bank_transfer" class="w-5 h-5 text-teal-600" checked>
                            </div>

                            <!-- Bank Account Details -->
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <p class="text-xs text-gray-600 font-semibold mb-2">NOMOR REKENING</p>
                                <div class="flex items-center gap-3">
                                    <span class="text-lg font-bold text-gray-900 tracking-wide">71 7482 95374 1034</span>
                                    <button type="button" onclick="copyToClipboard('71 7482 95374 1034')" 
                                            class="p-2 hover:bg-gray-100 rounded-lg transition text-teal-600 hover:text-teal-700"
                                            title="Salin nomor rekening">
                                        <i class="fas fa-copy text-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Instructions -->
                            <div class="mt-4 text-sm text-gray-700">
                                <p class="font-semibold mb-2">Instruksi Pembayaran:</p>
                                <ol class="list-decimal list-inside space-y-1 text-gray-600">
                                    <li>Buka aplikasi perbankan Anda atau ATM</li>
                                    <li>Transfer ke nomor rekening di atas</li>
                                    <li>Pembayaran akan dikonfirmasi secara otomatis</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Section - Ringkasan Pesanan -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-4">
                    <!-- Header -->
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Ringkasan Pesanan</h3>

                    <!-- Course Item -->
                    <div class="pb-6 border-b border-gray-200">
                        <div class="flex gap-4">
                            @if($course->image_url)
                                <img src="{{ $course->image_url }}" alt="{{ $judul }}" 
                                     class="w-16 h-16 object-cover rounded-lg">
                            @else
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-800 to-black rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-code text-white text-lg opacity-20"></i>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 line-clamp-2">{{ $judul }}</h4>
                                <p class="text-sm text-gray-600 mt-1">Pesanan Kamu (1 Kursus)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Details -->
                    <div class="py-6 space-y-4">
                        <!-- Subtotal -->
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($hargaAsli, 0, ',', '.') }}</span>
                        </div>

                        <!-- Diskon (jika ada) -->
                        @if($diskon > 0)
                            <div class="flex justify-between items-center text-teal-600">
                                <span>Diskon</span>
                                <span class="font-semibold">-Rp {{ number_format($diskon, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <!-- Pajak -->
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Pajak (10%)</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($pajak, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 pt-4 mb-6">
                        <!-- Total -->
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-900">Total</span>
                            <span class="text-3xl font-bold text-teal-600">
                                Rp {{ number_format($totalBayar, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Payment Button -->
                    <form action="{{ route('payment.process', $course) }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="metode_pembayaran" value="bank_transfer">
                        <input type="hidden" name="subtotal" value="{{ $hargaAsli }}">
                        <input type="hidden" name="diskon" value="{{ $diskon }}">
                        <input type="hidden" name="pajak" value="{{ $pajak }}">
                        <input type="hidden" name="total_bayar" value="{{ $totalBayar }}">
                        
                        <button type="submit" 
                                class="w-full px-6 py-3 bg-teal-600 text-white font-bold rounded-lg hover:bg-teal-700 transition flex items-center justify-center gap-2">
                            <i class="fas fa-lock"></i>
                            Proses Pembayaran
                        </button>
                    </form>

                    <!-- Back to Course -->
                    <a href="{{ route('courses.show', $course) }}" 
                       class="w-full block text-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:border-gray-400 transition mt-3">
                        Batal
                    </a>

                    <!-- Security Badge -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-center gap-2 text-xs text-gray-600">
                            <i class="fas fa-shield-alt text-teal-600"></i>
                            <span>Pembayaran Anda dijamin aman dan terlindungi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Tampilkan feedback visual
            const button = event.target.closest('button');
            const originalIcon = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check text-lg"></i>';
            
            setTimeout(() => {
                button.innerHTML = originalIcon;
            }, 2000);
        }).catch(err => {
            console.error('Gagal menyalin:', err);
        });
    }
</script>
@endsection
