{{-- resources/views/student/transactions/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Transaksi - ' . $transaction->transaction_code)

@section('content')
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Status Banner -->
            <div class="mb-6">
                @if($transaction->isPending() && !$transaction->isExpired())
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-clock text-yellow-600 text-3xl mr-4 mt-1"></i>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-yellow-900 mb-2">Menunggu Pembayaran</h3>
                                <p class="text-yellow-800 mb-3">Segera selesaikan pembayaran sebelum:</p>
                                <div class="flex items-center gap-2 text-yellow-900 font-bold text-xl">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>{{ $transaction->formatted_deadline }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($transaction->isPaid())
                    <div class="bg-green-50 border-l-4 border-green-400 p-6 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 text-3xl mr-4 mt-1"></i>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-green-900 mb-2">Pembayaran Berhasil!</h3>
                                <p class="text-green-800 mb-3">Terima kasih! Pembayaran Anda telah dikonfirmasi.</p>
                                <a href="{{ route('student.course.learn', $transaction->kursus) }}"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    <i class="fas fa-play-circle mr-2"></i>
                                    Mulai Belajar Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-red-50 border-l-4 border-red-400 p-6 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-times-circle text-red-600 text-3xl mr-4 mt-1"></i>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-red-900 mb-2">Transaksi {{ ucfirst($transaction->status) }}
                                </h3>
                                <p class="text-red-800">Transaksi ini tidak dapat dilanjutkan.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left: Transaction Details -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Transaction Info -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-file-invoice text-blue-600 mr-2"></i>
                            Detail Transaksi
                        </h2>

                        <div class="space-y-4">
                            <div class="flex justify-between py-3 border-b">
                                <span class="text-gray-600 font-medium">Kode Transaksi</span>
                                <span class="text-gray-900 font-bold">{{ $transaction->transaction_code }}</span>
                            </div>
                            <div class="flex justify-between py-3 border-b">
                                <span class="text-gray-600 font-medium">Status</span>
                                <div>{!! $transaction->status_badge !!}</div>
                            </div>
                            <div class="flex justify-between py-3 border-b">
                                <span class="text-gray-600 font-medium">Tanggal Pemesanan</span>
                                <span class="text-gray-900 font-semibold">
                                    {{ $transaction->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}
                                </span>
                            </div>
                            @if($transaction->isPending() && $transaction->payment_deadline)
                                <div class="flex justify-between py-3 border-b">
                                    <span class="text-gray-600 font-medium">Batas Pembayaran</span>
                                    <span class="text-red-600 font-bold">
                                        {{ $transaction->formatted_deadline }}
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

                    <!-- Payment Method -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                            Metode Pembayaran
                        </h2>

                        @if($transaction->payment_method)
                                        <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-lg">
                                            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                                                <i
                                                    class="fas fa-{{ 
                                                                                                                                                                                        $transaction->payment_method === 'bank_transfer' ? 'university' :
                            ($transaction->payment_method === 'e_wallet' ? 'mobile-alt' : 'receipt')
                                                                                                                                                                                    }} text-white text-xl"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900">
                                                    {{ ucwords(str_replace('_', ' ', $transaction->payment_method)) }}
                                                </div>
                                                <div class="text-sm text-gray-600">{{ $transaction->payment_channel }}</div>
                                            </div>
                                        </div>
                        @endif
                        @if($transaction->isPending() && !$transaction->isExpired())
                            <!-- Bank Account Info -->
                            @if($transaction->payment_method === 'bank_transfer')
                                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-start gap-3 mb-3">
                                        <img src="{{ asset('bni.png') }}" alt="BNI" class="h-8 mt-1" alt="Bank BNI"
                                            class="h-8 mt-1">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-900 mb-1">Transfer ke Rekening BNI:</h4>
                                            <div
                                                class="bg-white p-3 rounded border border-blue-300 flex items-center justify-between">
                                                <div>
                                                    <div class="text-xs text-gray-600 mb-1">Nomor Rekening</div>
                                                    <div class="text-xl font-bold text-gray-900 tracking-wider"
                                                        id="rekening-number">
                                                        7189828574 1034
                                                    </div>
                                                    <div class="text-sm text-gray-700 mt-1">a.n. <strong>Politeknik Negri
                                                            Jakarta</strong></div>
                                                </div>
                                                <button onclick="copyRekening()"
                                                    class="ml-3 p-2 text-blue-600 hover:bg-blue-100 rounded transition"
                                                    title="Salin nomor rekening">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                <h4 class="font-semibold text-gray-900 mb-2">Instruksi Pembayaran:</h4>
                                <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                                    <li>Buka aplikasi m-banking atau e-wallet Anda</li>
                                    <li>Pilih menu transfer atau pembayaran</li>
                                    <li>Masukkan nominal: <strong>Rp
                                            {{ number_format($transaction->total_bayar, 0, ',', '.') }}</strong></li>
                                    <li>Konfirmasi pembayaran</li>
                                    <li>Simpan bukti transfer</li>
                                </ol>
                            </div>

                            @if($transaction->snap_token)
                                {{-- Button to open Midtrans popup --}}
                                <button type="button" id="continuePaymentBtn"
                                    class="w-full mt-4 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                                    <i class="fas fa-check mr-2"></i>
                                    Konfirmasi Pembayaran
                                </button>
                            @else
                                {{-- No snap token yet, show button to create new --}}
                                <button type="button" id="createNewPaymentBtn"
                                    class="w-full mt-4 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Buat Pembayaran
                                </button>
                            @endif
                        @endif
                    </div>

                    <!-- Course Info -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-graduation-cap text-blue-600 mr-2"></i>
                            Kursus yang Dibeli
                        </h2>

                        <div class="flex gap-4">
                            @if($transaction->kursus->image_url)
                                <img src="{{ $transaction->kursus->image_url }}"
                                    alt="{{ $transaction->kursus->judul ?? $transaction->kursus->title }}"
                                    class="w-32 h-24 object-cover rounded-lg flex-shrink-0">
                            @else
                                <div
                                    class="w-32 h-24 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex-shrink-0 flex items-center justify-center">
                                    <i class="fas fa-book text-white text-3xl opacity-50"></i>
                                </div>
                            @endif

                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900 mb-1">
                                    {{ $transaction->kursus->judul ?? $transaction->kursus->title }}
                                </h3>
                                <p class="text-sm text-gray-600 mb-2">
                                    Oleh {{ $transaction->kursus->pembuat->name ?? 'Instruktur' }}
                                </p>
                                <a href="{{ route('courses.show', $transaction->kursus) }}"
                                    class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                    Lihat Detail Kursus →
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right: Price Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Pembayaran</h3>

                        <div class="space-y-3 mb-4">
                            <div class="flex justify-between text-gray-700">
                                <span>Harga Kursus</span>
                                <span class="font-semibold">Rp
                                    {{ number_format($transaction->harga_asli, 0, ',', '.') }}</span>
                            </div>

                            @if($transaction->harga_diskon && $transaction->diskon_persen > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>Diskon ({{ $transaction->diskon_persen }}%)</span>
                                    <span class="font-semibold">- Rp
                                        {{ number_format($transaction->harga_asli - $transaction->harga_diskon, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            <div class="border-t pt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-gray-900">Total Pembayaran</span>
                                    <span class="text-2xl font-bold text-blue-900">
                                        Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if($transaction->isPending() && !$transaction->isExpired())
                            <form action="{{ route('transactions.cancel', $transaction) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('Apakah Anda yakin ingin membatalkan transaksi ini?')"
                                    class="w-full px-4 py-3 border-2 border-red-500 text-red-600 rounded-lg font-semibold hover:bg-red-50 transition">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Batalkan Transaksi
                                </button>
                            </form>
                        @endif

                        <!-- Help -->
                        <div class="mt-6 pt-6 border-t">
                            <h4 class="font-semibold text-gray-900 mb-3">Butuh Bantuan?</h4>
                            <div class="space-y-2 text-sm">
                                <a href="#" class="flex items-center gap-2 text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-question-circle"></i>
                                    <span>Pusat Bantuan</span>
                                </a>
                                <a href="#" class="flex items-center gap-2 text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-comments"></i>
                                    <span>Chat dengan Kami</span>
                                </a>
                                <a href="#" class="flex items-center gap-2 text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-envelope"></i>
                                    <span>Email Support</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        function copyRekening() {
            const rekeningText = '71898285741034';
            navigator.clipboard.writeText(rekeningText).then(function () {
                // Show success message
                const btn = event.target.closest('button');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i>';
                btn.classList.add('text-green-600');

                setTimeout(function () {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('text-green-600');
                }, 2000);
            }).catch(function (err) {
                alert('Gagal menyalin nomor rekening');
            });
        }
    </script>

    {{-- Midtrans Snap for Continue Payment --}}
    @if($transaction->isPending() && !$transaction->isExpired() && $transaction->snap_token)
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const continuePaymentBtn = document.getElementById('continuePaymentBtn');
                let snapToken = '{{ $transaction->snap_token }}';
                const transactionId = {{ $transaction->id }};
                const transactionCode = '{{ $transaction->transaction_code }}';

                function openMidtransPopup(token) {
                    snap.pay(token, {
                        onSuccess: function (result) {
                            console.log('Payment Success:', result);
                            // Redirect ke halaman transaksi untuk melihat status terbaru
                            window.location.href = '{{ route("transactions.finish") }}?order_id=' + transactionCode + '&transaction_status=settlement';
                        },
                        onPending: function (result) {
                            console.log('Payment Pending:', result);
                            alert('Pembayaran sedang diproses. Silakan cek status secara berkala.');
                            window.location.reload();
                        },
                        onError: function (result) {
                            console.log('Payment Error:', result);
                            alert('Terjadi kesalahan pada proses pembayaran.');
                            window.location.reload();
                        },
                        onClose: function () {
                            console.log('Customer closed the popup');
                            // Reload untuk mendapatkan status terbaru
                            window.location.reload();
                        }
                    });
                }

                if (continuePaymentBtn) {
                    continuePaymentBtn.addEventListener('click', function () {
                        openMidtransPopup(snapToken);
                    });
                }

                // Handler for create new payment button (when no snap token exists)
                const createNewPaymentBtn = document.getElementById('createNewPaymentBtn');
                if (createNewPaymentBtn) {
                    createNewPaymentBtn.addEventListener('click', function () {
                        regenerateAndOpenPopup(createNewPaymentBtn, '<i class="fas fa-credit-card mr-2"></i>Buat Pembayaran');
                    });
                }

                // Reusable function for regenerating token and opening popup
                function regenerateAndOpenPopup(btn, originalHtml) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

                    fetch('{{ route("transactions.regenerate-snap-token", $transaction) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.snap_token) {
                                snapToken = data.snap_token;
                                openMidtransPopup(snapToken);
                            } else {
                                alert(data.message || 'Gagal memperbarui token pembayaran');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan. Silakan coba lagi.');
                        })
                        .finally(() => {
                            btn.disabled = false;
                            btn.innerHTML = originalHtml;
                        });
                }
            });
        </script>
    @endif
@endsection