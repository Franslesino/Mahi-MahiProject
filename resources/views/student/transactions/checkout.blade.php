{{-- resources/views/student/transactions/checkout.blade.php --}}
@extends('layouts.app')

@section('title', 'Checkout - ' . ($course->judul ?? $course->title))

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Pembayaran Kursus</h1>
            <p class="text-gray-600">Lengkapi data pembayaran untuk memulai belajar</p>
        </div>

        <form action="{{ route('transactions.process', $course) }}" method="POST" id="checkoutForm">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column: Payment Form -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- User Details Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                            Detail Data User
                        </h2>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600 font-medium">Email</span>
                                <span class="text-gray-900 font-semibold">{{ Auth::user()->email }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600 font-medium">Nama Lengkap</span>
                                <span class="text-gray-900 font-semibold">{{ Auth::user()->name }}</span>
                            </div>
                            @if(Auth::user()->phone || Auth::user()->no_telepon)
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600 font-medium">Negara</span>
                                <span class="text-gray-900 font-semibold">Indonesia</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Voucher Section (NEW!) -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-ticket-alt text-blue-600 mr-2"></i>
                            Kode Voucher
                        </h2>

                        <div class="flex gap-3">
                            <input type="text" 
                                   id="voucherCode" 
                                   name="voucher_code"
                                   placeholder="Masukkan kode voucher (e.g., PNJ-TIK-35)" 
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="{{ old('voucher_code') }}">
                            <button type="button" 
                                    id="applyVoucher"
                                    class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                                Gunakan
                            </button>
                        </div>

                        <!-- Voucher Message -->
                        <div id="voucherMessage" class="mt-3 hidden"></div>

                        <!-- Applied Voucher Display -->
                        <div id="appliedVoucher" class="mt-4 hidden">
                            <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <i class="fas fa-check-circle text-green-600 text-xl mt-1"></i>
                                <div class="flex-1">
                                    <h4 class="font-bold text-green-900 mb-1" id="voucherName"></h4>
                                    <p class="text-sm text-green-700 mb-2" id="voucherDescription"></p>
                                    <p class="text-lg font-bold text-green-800">
                                        Diskon: <span id="voucherDiscount"></span>
                                    </p>
                                </div>
                                <button type="button" 
                                        id="removeVoucher"
                                        class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Available Vouchers Info -->
                        {{-- <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <h4 class="font-semibold text-blue-900 mb-2">
                                <i class="fas fa-info-circle mr-2"></i>
                                Voucher Tersedia:
                            </h4>
                            <ul class="text-sm text-blue-800 space-y-1">
                                <li>• <strong>PNJ-TIK-35</strong> - Diskon 35% untuk mahasiswa PNJ TIK</li>
                                <li>• <strong>WELCOME50K</strong> - Potongan Rp 50.000 (min. Rp 100.000)</li>
                                <li>• <strong>FLASH20</strong> - Diskon 20% (max. Rp 100.000)</li>
                                <li>• <strong>STUDENT15</strong> - Diskon 15% untuk pelajar</li>
                            </ul>
                        </div> --}}
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                            Metode Pembayaran
                        </h2>

                        <!-- Bank Transfer -->
                        <div class="mb-4">
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition payment-option">
                                <input type="radio" name="payment_method" value="bank_transfer" class="mr-3" required>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Transfer Bank</div>
                                    <div class="text-sm text-gray-600">BCA, Mandiri, BNI, BRI</div>
                                </div>
                                <i class="fas fa-university text-blue-600 text-2xl"></i>
                            </label>
                            
                            <!-- Bank Options -->
                            <div id="bank-options" class="hidden mt-3 ml-7 space-y-2">
                                <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                    <input type="radio" name="payment_channel" value="BCA" class="mr-3">
                                    <span class="font-medium">Bank BCA</span>
                                </label>
                                <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                    <input type="radio" name="payment_channel" value="Mandiri" class="mr-3">
                                    <span class="font-medium">Bank Mandiri</span>
                                </label>
                                <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                    <input type="radio" name="payment_channel" value="BNI" class="mr-3">
                                    <span class="font-medium">Bank BNI</span>
                                </label>
                                <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                    <input type="radio" name="payment_channel" value="BRI" class="mr-3">
                                    <span class="font-medium">Bank BRI</span>
                                </label>
                            </div>
                        </div>

                        <!-- E-Wallet -->
                        <div class="mb-4">
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition payment-option">
                                <input type="radio" name="payment_method" value="e_wallet" class="mr-3" required>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">E-Wallet</div>
                                    <div class="text-sm text-gray-600">GoPay, OVO, Dana, ShopeePay</div>
                                </div>
                                <i class="fas fa-mobile-alt text-green-600 text-2xl"></i>
                            </label>
                            
                            <!-- E-Wallet Options -->
                            <div id="ewallet-options" class="hidden mt-3 ml-7 space-y-2">
                                <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                    <input type="radio" name="payment_channel" value="GoPay" class="mr-3">
                                    <span class="font-medium">GoPay</span>
                                </label>
                                <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                    <input type="radio" name="payment_channel" value="OVO" class="mr-3">
                                    <span class="font-medium">OVO</span>
                                </label>
                                <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                    <input type="radio" name="payment_channel" value="Dana" class="mr-3">
                                    <span class="font-medium">Dana</span>
                                </label>
                                <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                    <input type="radio" name="payment_channel" value="ShopeePay" class="mr-3">
                                    <span class="font-medium">ShopeePay</span>
                                </label>
                            </div>
                        </div>

                        <!-- Virtual Account -->
                        <div class="mb-4">
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition payment-option">
                                <input type="radio" name="payment_method" value="virtual_account" class="mr-3" required>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Virtual Account</div>
                                    <div class="text-sm text-gray-600">Nomor VA otomatis</div>
                                </div>
                                <i class="fas fa-receipt text-purple-600 text-2xl"></i>
                            </label>
                            
                            <!-- VA Options -->
                            <div id="va-options" class="hidden mt-3 ml-7 space-y-2">
                                <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                    <input type="radio" name="payment_channel" value="BCA VA" class="mr-3">
                                    <span class="font-medium">BCA Virtual Account</span>
                                </label>
                                <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                    <input type="radio" name="payment_channel" value="Mandiri VA" class="mr-3">
                                    <span class="font-medium">Mandiri Virtual Account</span>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Ringkasan Pesanan</h2>
                        
                        <!-- Course Info -->
                        <div class="mb-6 pb-6 border-b">
                            @if($course->image_url)
                                <img src="{{ $course->image_url }}" 
                                     alt="{{ $course->judul ?? $course->title }}" 
                                     class="w-full h-32 object-cover rounded-lg mb-3">
                            @else
                                <div class="w-full h-32 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg mb-3 flex items-center justify-center">
                                    <i class="fas fa-graduation-cap text-white text-4xl opacity-50"></i>
                                </div>
                            @endif
                            <h3 class="font-semibold text-gray-900 mb-1">{{ $course->judul ?? $course->title }}</h3>
                            <p class="text-sm text-gray-600">{{ $course->instructor->name ?? 'Instruktur' }}</p>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-gray-700">
                                <span>Subtotal</span>
                                <span class="font-semibold" id="subtotalDisplay">Rp {{ number_format($hargaAsli, 0, ',', '.') }}</span>
                            </div>
                            
                            @if($hargaDiskon)
                                <div class="flex justify-between text-green-600">
                                    <span>Diskon Kursus ({{ $diskonPersen }}%)</span>
                                    <span class="font-semibold">- Rp {{ number_format($hargaAsli - $hargaDiskon, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            <!-- Voucher Discount (Dynamic) -->
                            <div id="voucherDiscountRow" class="hidden flex justify-between text-green-600">
                                <span>Diskon Voucher (<span id="voucherDiscountLabel"></span>)</span>
                                <span class="font-semibold">- Rp <span id="voucherDiscountAmount">0</span></span>
                            </div>

                            <div class="flex justify-between text-gray-700">
                                <span>Biaya Platform</span>
                                <span class="font-semibold text-green-600">Free</span>
                            </div>

                            <div class="border-t pt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-gray-900">Total</span>
                                    <span class="text-2xl font-bold text-blue-900" id="totalDisplay">
                                        Rp {{ number_format($totalBayar, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Savings Display -->
                            <div id="savingsDisplay" class="hidden pt-3 border-t">
                                <div class="p-3 bg-green-50 rounded-lg">
                                    <p class="text-sm text-green-800">
                                        <i class="fas fa-tag mr-1"></i>
                                        Hemat: <strong>Rp <span id="totalSavings">0</span></strong>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs for voucher -->
                        <input type="hidden" name="voucher_id" id="voucherId" value="">
                        <input type="hidden" name="voucher_discount" id="voucherDiscountInput" value="0">

                        <!-- Action Buttons -->
                        <button type="submit" 
                                id="payButton"
                                class="w-full px-6 py-4 bg-blue-900 text-white rounded-lg font-semibold hover:bg-blue-800 transition shadow-lg mb-3">
                            <i class="fas fa-lock mr-2"></i>
                            Bayar Sekarang
                        </button>

                        <a href="{{ route('courses.show', $course) }}" 
                           class="block w-full text-center px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                            Kembali ke Kursus
                        </a>

                        <!-- Trust Badges -->
                        <div class="mt-6 pt-6 border-t">
                            <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                                <i class="fas fa-shield-alt text-green-500"></i>
                                <span>Pembayaran Aman & Terenkripsi</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span>Garansi 30 Hari</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>

<!-- Midtrans Snap Script -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" 
        data-client-key="{{ config('midtrans.client_key') }}"></script>

<!-- Loading Modal -->
<div id="loadingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-8 text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-900 border-t-blue-400 mb-4"></div>
        <p class="text-gray-700 font-semibold">Memproses pembayaran...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment method toggle
    const paymentOptions = document.querySelectorAll('input[name="payment_method"]');
    
    paymentOptions.forEach(option => {
        option.addEventListener('change', function() {
            document.getElementById('bank-options').classList.add('hidden');
            document.getElementById('ewallet-options').classList.add('hidden');
            document.getElementById('va-options').classList.add('hidden');
            
            document.querySelectorAll('input[name="payment_channel"]').forEach(ch => ch.checked = false);
            
            if (this.value === 'bank_transfer') {
                document.getElementById('bank-options').classList.remove('hidden');
            } else if (this.value === 'e_wallet') {
                document.getElementById('ewallet-options').classList.remove('hidden');
            } else if (this.value === 'virtual_account') {
                document.getElementById('va-options').classList.remove('hidden');
            }
        });
    });
    
    document.querySelectorAll('.payment-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.payment-option').forEach(opt => {
                opt.classList.remove('border-blue-500', 'bg-blue-50');
                opt.classList.add('border-gray-200');
            });
            if (this.querySelector('input[type="radio"]').checked) {
                this.classList.add('border-blue-500', 'bg-blue-50');
                this.classList.remove('border-gray-200');
            }
        });
    });

    // Voucher functionality
    const originalPrice = {{ $hargaDiskon ?? $hargaAsli }};
    let voucherDiscount = 0;
    let appliedVoucherData = null;

    // Apply voucher
    document.getElementById('applyVoucher').addEventListener('click', function() {
        const voucherCode = document.getElementById('voucherCode').value.trim();
        
        if (!voucherCode) {
            showVoucherMessage('Silakan masukkan kode voucher', 'error');
            return;
        }

        // Send AJAX request to validate voucher
        fetch(`{{ route('voucher.validate') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                voucher_code: voucherCode,
                course_id: {{ $course->id }},
                price: originalPrice
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                applyVoucherToCheckout(data.voucher, data.discount);
                showVoucherMessage('Voucher berhasil diterapkan!', 'success');
            } else {
                showVoucherMessage(data.message, 'error');
            }
        })
        .catch(error => {
            showVoucherMessage('Terjadi kesalahan. Silakan coba lagi.', 'error');
        });
    });

    // Remove voucher
    document.getElementById('removeVoucher').addEventListener('click', function() {
        removeVoucher();
    });

    function applyVoucherToCheckout(voucher, discount) {
        appliedVoucherData = voucher;
        voucherDiscount = discount;

        // Update UI
        document.getElementById('appliedVoucher').classList.remove('hidden');
        document.getElementById('voucherName').textContent = voucher.name;
        document.getElementById('voucherDescription').textContent = voucher.description;
        document.getElementById('voucherDiscount').textContent = voucher.discount_text;

        // Show voucher discount row
        document.getElementById('voucherDiscountRow').classList.remove('hidden');
        document.getElementById('voucherDiscountLabel').textContent = voucher.code;
        document.getElementById('voucherDiscountAmount').textContent = formatNumber(discount);

        // Update total
        const newTotal = originalPrice - discount;
        document.getElementById('totalDisplay').textContent = 'Rp ' + formatNumber(newTotal);

        // Show savings
        const totalSavings = {{ isset($diskonPersen) && $diskonPersen > 0 ? $hargaAsli - $hargaDiskon : 0 }} + discount;
        document.getElementById('savingsDisplay').classList.remove('hidden');
        document.getElementById('totalSavings').textContent = formatNumber(totalSavings);

        // Set hidden inputs
        document.getElementById('voucherId').value = voucher.id;
        document.getElementById('voucherDiscountInput').value = discount;

        // Disable voucher input
        document.getElementById('voucherCode').disabled = true;
        document.getElementById('applyVoucher').disabled = true;
    }

    function removeVoucher() {
        appliedVoucherData = null;
        voucherDiscount = 0;

        // Reset UI
        document.getElementById('appliedVoucher').classList.add('hidden');
        document.getElementById('voucherDiscountRow').classList.add('hidden');
        document.getElementById('totalDisplay').textContent = 'Rp ' + formatNumber(originalPrice);

        // Hide/update savings
        const courseDiscount = {{ isset($diskonPersen) && $diskonPersen > 0 ? $hargaAsli - $hargaDiskon : 0 }};
        if (courseDiscount > 0) {
            document.getElementById('totalSavings').textContent = formatNumber(courseDiscount);
        } else {
            document.getElementById('savingsDisplay').classList.add('hidden');
        }

        // Clear hidden inputs
        document.getElementById('voucherId').value = '';
        document.getElementById('voucherDiscountInput').value = '0';

        // Enable voucher input
        document.getElementById('voucherCode').disabled = false;
        document.getElementById('voucherCode').value = '';
        document.getElementById('applyVoucher').disabled = false;

        // Hide message
        document.getElementById('voucherMessage').classList.add('hidden');
    }

    function showVoucherMessage(message, type) {
        const messageEl = document.getElementById('voucherMessage');
        messageEl.classList.remove('hidden');
        
        if (type === 'success') {
            messageEl.className = 'mt-3 p-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm';
            messageEl.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + message;
        } else {
            messageEl.className = 'mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm';
            messageEl.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>' + message;
        }
    }

    function formatNumber(num) {
        return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Handle form submission untuk Midtrans payment
    const checkoutForm = document.getElementById('checkoutForm');
    const payButton = document.getElementById('payButton');

    checkoutForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validasi form
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        const paymentChannel = document.querySelector('input[name="payment_channel"]:checked');

        if (!paymentMethod) {
            alert('Silakan pilih metode pembayaran');
            return;
        }

        if (!paymentChannel) {
            alert('Silakan pilih channel pembayaran');
            return;
        }

        // Show loading
        showLoading(true);
        payButton.disabled = true;

        // Submit form untuk create transaction
        const formData = new FormData(this);

        fetch('{{ route("transactions.process", $course) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            showLoading(false);

            if (data.success && data.snap_token) {
                // Show Midtrans popup
                showMidtransPopup(data.snap_token, data.transaction_id, data.transaction_code);
            } else {
                alert(data.message || 'Terjadi kesalahan saat membuat transaksi');
                payButton.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showLoading(false);
            alert('Terjadi kesalahan. Silakan coba lagi.');
            payButton.disabled = false;
        });
    });

    function showLoading(show) {
        const modal = document.getElementById('loadingModal');
        if (show) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }

    function showMidtransPopup(snapToken, transactionId, transactionCode) {
        snap.pay(snapToken, {
            onSuccess: function(result) {
                console.log('Payment Success:', result);
                handlePaymentSuccess(transactionId, transactionCode);
            },
            onPending: function(result) {
                console.log('Payment Pending:', result);
                showLoading(true);
                // Check status setiap 2 detik
                setTimeout(() => {
                    checkPaymentStatus(transactionCode);
                }, 2000);
            },
            onError: function(result) {
                console.log('Payment Error:', result);
                alert('Terjadi kesalahan pada proses pembayaran.');
                payButton.disabled = false;
            },
            onClose: function() {
                console.log('Customer closed the popup');
                payButton.disabled = false;
            }
        });
    }

    function handlePaymentSuccess(transactionId, transactionCode) {
        showLoading(true);

        // Call backend to complete enrollment
        fetch('{{ route("transactions.complete-payment") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                transaction_id: transactionId,
                transaction_code: transactionCode
            })
        })
        .then(response => response.json())
        .then(data => {
            showLoading(false);

            if (data.success) {
                // Show success message dan redirect
                alert('Pembayaran berhasil! Anda sekarang terdaftar di kursus ini.');
                window.location.href = data.redirect_url;
            } else {
                alert(data.message || 'Pembayaran berhasil tapi ada kesalahan saat mendaftarkan kursus');
                window.location.href = window.location.href;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showLoading(false);
            alert('Pembayaran berhasil! Silakan tunggu, sistem sedang memproses...');
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        });
    }

    function checkPaymentStatus(transactionCode) {
        fetch('{{ route("transactions.check-status") }}?code=' + transactionCode, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'paid') {
                handlePaymentSuccess(data.transaction_id, transactionCode);
            } else if (data.status === 'pending') {
                setTimeout(() => {
                    checkPaymentStatus(transactionCode);
                }, 2000);
            } else {
                showLoading(false);
                alert('Pembayaran gagal atau dibatalkan');
                payButton.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error checking status:', error);
            showLoading(false);
        });
    }
});
</script>
@endsection
