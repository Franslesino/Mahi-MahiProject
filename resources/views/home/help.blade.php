@extends('layouts.app')

@section('title', 'Pusat Bantuan - UpGrennius')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-4">Pusat Bantuan & FAQ</h1>
        <p class="text-lg text-gray-600">Temukan jawaban untuk pertanyaan yang paling sering diajukan.</p>
    </div>

    <div x-data="{ active: null }" class="space-y-4">
        {{-- FAQ Item 1 --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <button @click="active = active === 1 ? null : 1" 
                    class="w-full px-6 py-5 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                <span class="font-bold text-gray-900">Bagaimana cara mendaftar kursus?</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform" :class="active === 1 ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="active === 1" x-collapse class="px-6 py-5 border-t border-gray-50 text-gray-600 leading-relaxed">
                Pilih kursus yang Anda minati di halaman Beranda atau Cari Kursus, lalu klik tombol "Daftar Sekarang" atau "Beli Kursus". Ikuti langkah pembayaran hingga selesai, dan kursus akan otomatis muncul di menu "Kursus Saya".
            </div>
        </div>

        {{-- FAQ Item 2 --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <button @click="active = active === 2 ? null : 2" 
                    class="w-full px-6 py-5 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                <span class="font-bold text-gray-900">Kapan saya akan mendapatkan sertifikat?</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform" :class="active === 2 ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="active === 2" x-collapse class="px-6 py-5 border-t border-gray-50 text-gray-600 leading-relaxed">
                Sertifikat akan diterbitkan secara otomatis setelah Anda menyelesaikan seluruh materi kursus dan mencapai skor minimum pada Final Quiz yang ditentukan oleh instruktur.
            </div>
        </div>

        {{-- FAQ Item 3 --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <button @click="active = active === 3 ? null : 3" 
                    class="w-full px-6 py-5 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                <span class="font-bold text-gray-900">Apakah saya bisa mengakses kursus selamanya?</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform" :class="active === 3 ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="active === 3" x-collapse class="px-6 py-5 border-t border-gray-50 text-gray-600 leading-relaxed">
                Tergantung pada kebijakan instruktur, sebagian besar kursus kami memberikan akses selamanya (Lifetime Access). Namun, ada beberapa program pelatihan intensif yang memiliki batas waktu akses tertentu yang akan diinformasikan di halaman kursus.
            </div>
        </div>

        {{-- FAQ Item 4 --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <button @click="active = active === 4 ? null : 4" 
                    class="w-full px-6 py-5 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                <span class="font-bold text-gray-900">Metode pembayaran apa saja yang tersedia?</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform" :class="active === 4 ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="active === 4" x-collapse class="px-6 py-5 border-t border-gray-50 text-gray-600 leading-relaxed">
                Kami mendukung berbagai metode pembayaran melalui Payment Gateway (Midtrans), termasuk Transfer Bank (Virtual Account), E-Wallet (OVO, GoPay, Dana, ShopeePay), dan Kartu Kredit.
            </div>
        </div>
    </div>

    <div class="mt-16 bg-pnj-teal rounded-3xl p-8 md:p-12 text-center text-white shadow-xl shadow-teal-100">
        <h3 class="text-2xl font-bold mb-4">Masih butuh bantuan?</h3>
        <p class="mb-8 opacity-90 text-lg">Tim support kami siap membantu Anda menyelesaikan kendala teknis maupun pertanyaan lainnya.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact') }}" class="bg-white text-pnj-teal font-bold py-4 px-10 rounded-2xl shadow-lg hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-envelope"></i> Hubungi Kami
            </a>
            <a href="https://wa.me/yournumber" class="bg-emerald-500 text-white font-bold py-4 px-10 rounded-2xl shadow-lg hover:bg-emerald-600 transition-all flex items-center justify-center gap-2">
                <i class="fab fa-whatsapp text-xl"></i> WhatsApp Support
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
@endsection
