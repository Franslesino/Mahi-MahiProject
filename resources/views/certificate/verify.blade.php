@extends('layouts.app')

@section('title', 'Verifikasi Sertifikat - UpGreenius')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-20 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Logo & Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-600 text-white rounded-3xl shadow-xl shadow-blue-200 mb-6 transform hover:rotate-12 transition-transform duration-300">
                <i class="fas fa-certificate text-4xl"></i>
            </div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight mb-4">
                Verifikasi Keaslian Sertifikat
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Validasi keaslian sertifikat kursus UpGreenius dengan memasukkan kode unik yang tertera pada dokumen sertifikat Anda.
            </p>
        </div>

        <!-- Search Card -->
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-blue-100/50 p-8 md:p-12 mb-12 border border-blue-50 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50 rounded-full -mr-32 -mt-32 opacity-50 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-50 rounded-full -ml-32 -mb-32 opacity-50 blur-3xl"></div>

            <form action="{{ route('certificate.check') }}" method="POST" class="relative z-10">
                @csrf
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-qrcode text-blue-500"></i>
                        </div>
                        <input type="text" 
                               name="kode" 
                               value="{{ old('kode', isset($certificate) ? ($certificate->kode_sertifikat ?? $certificate->nomor_sertifikat) : '') }}"
                               class="block w-full pl-11 pr-4 py-4 text-gray-900 border-2 border-blue-100 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-300 text-lg sm:text-base font-medium placeholder-gray-400"
                               placeholder="Masukkan Kode Sertifikat (Contoh: CERT-2023-XXXX)"
                               required>
                    </div>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-10 rounded-2xl transition-all duration-300 shadow-lg shadow-blue-200 flex items-center justify-center gap-2 transform hover:-translate-y-1">
                        <i class="fas fa-search"></i>
                        <span>Cek Sertifikat</span>
                    </button>
                </div>
                @error('kode')
                    <p class="mt-3 text-red-500 text-sm font-semibold flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </form>
        </div>

        <!-- Results Section -->
        @if(isset($certificate))
            <div class="animate-in fade-in slide-in-from-bottom-8 duration-700">
                <div class="bg-white border-2 border-emerald-100 rounded-[2.5rem] overflow-hidden shadow-2xl shadow-emerald-100/50">
                    <div class="bg-emerald-500 px-8 py-4 flex items-center justify-between text-white">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-xl"></i>
                            <span class="font-bold tracking-wide uppercase text-sm">Sertifikat Valid & Terdaftar</span>
                        </div>
                        <span class="text-xs bg-white/20 px-3 py-1 rounded-full font-medium">Verify ID: {{ $certificate->kode_sertifikat }}</span>
                    </div>
                    
                    <div class="p-8 md:p-12">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                            <!-- Left: Details -->
                            <div class="space-y-8">
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nama Penerima</label>
                                    <h3 class="text-2xl font-extrabold text-gray-900">{{ $certificate->enrollment->user->name ?? 'N/A' }}</h3>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Program / Kursus</label>
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-graduation-cap text-xl"></i>
                                        </div>
                                        <h4 class="text-xl font-bold text-gray-800 leading-tight">
                                            {{ $certificate->enrollment->kursus->judul ?? ($certificate->enrollment->course->judul ?? 'N/A') }}
                                        </h4>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Tanggal Terbit</label>
                                        <p class="font-bold text-gray-800">
                                            {{ $certificate->tanggal_diterbitkan ? $certificate->tanggal_diterbitkan->format('d F Y') : ($certificate->tanggal_terbit ? $certificate->tanggal_terbit->format('d F Y') : 'N/A') }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Nomor Seri</label>
                                        <p class="font-bold text-gray-800">{{ $certificate->kode_sertifikat ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Download Button -->
                            <div class="flex flex-col justify-center bg-gray-50 rounded-3xl p-8 border border-gray-100">
                                <div class="text-center mb-6">
                                    <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-certificate text-3xl"></i>
                                    </div>
                                    <h4 class="text-lg font-bold text-gray-900 mb-2">Sertifikat Terverifikasi</h4>
                                    <p class="text-sm text-gray-600">Sertifikat ini asli dan terdaftar dalam sistem kami</p>
                                </div>
                                <a href="{{ route('student.certificate.download', $certificate->enrollment_id) }}" 
                                   class="inline-flex items-center justify-center gap-2 w-full bg-blue-600 text-white hover:bg-blue-700 font-bold py-4 rounded-xl transition-all duration-300 shadow-lg shadow-blue-200">
                                    <i class="fas fa-download"></i>
                                    <span>Unduh Sertifikat</span>
                                </a>
                                <p class="text-center text-xs text-gray-500 mt-4">
                                    <i class="fas fa-lock text-[10px] mr-1"></i> Data diverifikasi secara digital oleh UpGreenius Learning System.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(session('error') || (isset($error) && $error))
            <div class="animate-in fade-in slide-in-from-bottom-8 duration-700 bg-white border-2 border-red-100 rounded-[2rem] p-8 md:p-12 text-center shadow-2xl shadow-red-100/50">
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-times text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Sertifikat Tidak Ditemukan</h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    {{ session('error') ?? $error }} Silakan hubungi pusat bantuan kami jika Anda yakin ini adalah kesalahan.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('certificate.verify') }}" class="inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-8 rounded-xl transition-all">
                        Coba Lagi
                    </a>
                    <a href="https://wa.me/yournumber" class="inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-8 rounded-xl transition-all gap-2 shadow-lg shadow-emerald-100">
                        <i class="fab fa-whatsapp"></i>
                        <span>Bantuan Customer Service</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- Footer Info -->
        <div class="mt-16 text-center text-gray-400 text-sm">
            <p>© {{ date('Y') }} UpGreenius. Seluruh hak cipta dilindungi.</p>
        </div>
    </div>
</div>

<style>
    @keyframes slide-in-from-bottom-8 {
        from { transform: translateY(2rem); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .animate-in {
        animation: slide-in-from-bottom-8 0.7s ease-out forwards;
    }
</style>
@endsection
