@extends('layouts.admin')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <a href="{{ route('admin.vouchers.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2 mb-4">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Daftar Voucher</span>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Tambah Voucher Baru</h2>
    </div>

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <p class="font-semibold">Terdapat kesalahan:</p>
        <ul class="list-disc list-inside mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.vouchers.store') }}" method="POST" class="bg-white rounded-lg shadow-sm p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kode Voucher -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kode Voucher <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="code" 
                       value="{{ old('code') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 uppercase"
                       placeholder="DISKON50"
                       required>
                <p class="text-xs text-gray-500 mt-1">Kode akan otomatis diubah ke huruf kapital</p>
            </div>

            <!-- Nama Voucher -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Voucher <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Diskon 50% Hari Kemerdekaan"
                       required>
            </div>

            <!-- Deskripsi -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea name="description" 
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                          placeholder="Deskripsi singkat tentang voucher ini...">{{ old('description') }}</textarea>
            </div>

            <!-- Tipe Diskon -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tipe Diskon <span class="text-red-500">*</span>
                </label>
                <select name="type" 
                        id="discountType"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        required>
                    <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                    <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                </select>
            </div>

            <!-- Nilai Diskon -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nilai Diskon <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" 
                           name="value" 
                           value="{{ old('value') }}"
                           step="0.01"
                           min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="50"
                           required>
                    <span id="discountUnit" class="absolute right-4 top-2.5 text-gray-500">%</span>
                </div>
                <p class="text-xs text-gray-500 mt-1" id="discountHint">Maksimal 100 untuk persentase</p>
            </div>

            <!-- Maksimal Diskon (untuk percentage) -->
            <div id="maxDiscountDiv">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Maksimal Diskon (Opsional)
                </label>
                <input type="number" 
                       name="max_discount" 
                       value="{{ old('max_discount') }}"
                       step="1000"
                       min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="100000">
                <p class="text-xs text-gray-500 mt-1">Batas maksimal potongan dalam Rupiah</p>
            </div>

            <!-- Minimal Pembelian -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Minimal Pembelian (Opsional)
                </label>
                <input type="number" 
                       name="min_purchase" 
                       value="{{ old('min_purchase') }}"
                       step="1000"
                       min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="50000">
                <p class="text-xs text-gray-500 mt-1">Minimal harga kursus untuk menggunakan voucher</p>
            </div>

            <!-- Maksimal Penggunaan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Maksimal Penggunaan (Opsional)
                </label>
                <input type="number" 
                       name="max_usage" 
                       value="{{ old('max_usage') }}"
                       min="1"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="100">
                <p class="text-xs text-gray-500 mt-1">Kosongkan untuk penggunaan tidak terbatas</p>
            </div>

            <!-- Tanggal Mulai -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Mulai <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       name="start_date" 
                       value="{{ old('start_date', date('Y-m-d')) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       required>
            </div>

            <!-- Tanggal Berakhir -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Berakhir <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       name="end_date" 
                       value="{{ old('end_date') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       required>
            </div>

            <!-- Kursus yang Diizinkan -->
            <div class="md:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Kursus yang Diizinkan (Opsional)
                    </label>
                    @if($courses->count() > 0)
                    <div class="flex gap-2">
                        <button type="button" 
                                onclick="selectAllCourses(true)"
                                class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">
                            <i class="fas fa-check-square"></i> Pilih Semua
                        </button>
                        <button type="button" 
                                onclick="selectAllCourses(false)"
                                class="text-xs text-red-600 hover:text-red-800 font-medium">
                            <i class="fas fa-times-square"></i> Hapus Semua
                        </button>
                    </div>
                    @endif
                </div>
                <div class="border border-gray-300 rounded-lg p-4 max-h-60 overflow-y-auto">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
                        <p class="text-xs text-blue-700 flex items-start gap-2">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            <span><strong>Kosongkan semua</strong> jika voucher berlaku untuk <strong>semua kursus</strong>. Centang kursus tertentu jika voucher hanya berlaku untuk kursus yang dipilih saja.</span>
                        </p>
                    </div>
                    @if($courses->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2" id="coursesList">
                        @foreach($courses as $course)
                        <label class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded cursor-pointer border border-transparent hover:border-emerald-200">
                            <input type="checkbox" 
                                   name="allowed_courses[]" 
                                   value="{{ $course->id }}"
                                   {{ in_array($course->id, old('allowed_courses', [])) ? 'checked' : '' }}
                                   class="course-checkbox rounded text-emerald-500 focus:ring-emerald-500">
                            <span class="text-sm text-gray-700">{{ $course->judul }}</span>
                        </label>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-500 text-center py-4">Belum ada kursus tersedia</p>
                    @endif
                </div>
            </div>

            <!-- Status Aktif -->
            <div class="md:col-span-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm font-medium text-gray-700">Aktifkan voucher sekarang</span>
                </label>
                <p class="text-xs text-gray-500 mt-1 ml-6">Voucher yang tidak aktif tidak dapat digunakan meskipun masih dalam periode berlaku</p>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-200">
            <button type="submit" 
                    class="px-6 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Simpan Voucher</span>
            </button>
            <a href="{{ route('admin.vouchers.index') }}" 
               class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
// Discount type handler
document.getElementById('discountType').addEventListener('change', function() {
    const unit = document.getElementById('discountUnit');
    const hint = document.getElementById('discountHint');
    const maxDiscountDiv = document.getElementById('maxDiscountDiv');
    const valueInput = document.querySelector('input[name="value"]');
    
    if (this.value === 'percentage') {
        unit.textContent = '%';
        hint.textContent = 'Maksimal 100 untuk persentase';
        maxDiscountDiv.style.display = 'block';
        valueInput.max = '100';
    } else {
        unit.textContent = 'Rp';
        hint.textContent = 'Nominal potongan dalam Rupiah';
        maxDiscountDiv.style.display = 'none';
        valueInput.removeAttribute('max');
    }
});

// Select/Deselect all courses
function selectAllCourses(select) {
    const checkboxes = document.querySelectorAll('.course-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = select;
    });
}

// Trigger on page load
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('discountType').dispatchEvent(new Event('change'));
});
</script>
@endsection
