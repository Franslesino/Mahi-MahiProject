@extends('layouts.app')

@section('title', 'Hubungi Kami - UpGrennius')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-16">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-4">Hubungi Kami</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">Ada pertanyaan, saran, atau kendala? Kami di sini untuk mendengarkan dan membantu Anda sukses belajar.</p>
    </div>

    <div class="grid lg:grid-cols-3 gap-12">
        <!-- Contact Info Cards -->
        <div class="space-y-6">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center text-center group hover:border-blue-500 transition-colors">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 border border-blue-100 group-hover:scale-110 transition-transform">
                    <i class="fas fa-map-marker-alt text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Lokasi Kantor</h3>
                <p class="text-gray-600 leading-relaxed">Jl. Prof. DR. G.A. Siwabessy, Kampus PNJ, Depok 16425</p>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center text-center group hover:border-pnj-teal transition-colors">
                <div class="w-16 h-16 bg-teal-50 text-pnj-teal rounded-2xl flex items-center justify-center mb-6 border border-teal-100 group-hover:scale-110 transition-transform">
                    <i class="fas fa-envelope text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Email Support</h3>
                <p class="text-gray-600 leading-relaxed">info@upgreenius.com<br>support@upgreenius.test</p>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center text-center group hover:border-emerald-500 transition-colors">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 border border-emerald-100 group-hover:scale-110 transition-transform">
                    <i class="fab fa-whatsapp text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Customer Service</h3>
                <p class="text-gray-600 leading-relaxed">+62 812-3456-7890<br>Senin - Jumat (08:00 - 17:00)</p>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-100 p-8 md:p-12 border border-blue-50">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Kirim Pesan Langsung</h2>
                
                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="name" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-pnj-teal focus:bg-white transition-all outline-none" placeholder="Masukkan nama Anda" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Email</label>
                            <input type="email" name="email" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-pnj-teal focus:bg-white transition-all outline-none" placeholder="email@contoh.com" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Subjek</label>
                        <select name="subject" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-pnj-teal focus:bg-white transition-all outline-none" required>
                            <option value="">Pilih alasan menghubungi</option>
                            <option value="technical">Masalah Teknis</option>
                            <option value="payment">Pertanyaan Pembayaran</option>
                            <option value="partnership">Kerjasama & Instruktur</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pesan Anda</label>
                        <textarea name="message" rows="6" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-pnj-teal focus:bg-white transition-all outline-none resize-none" placeholder="Tuliskan pesan Anda di sini..." required></textarea>
                    </div>

                    <button type="submit" class="w-full bg-pnj-teal hover:bg-teal-700 text-white font-bold py-5 rounded-2xl shadow-lg shadow-teal-100 transition-all flex items-center justify-center gap-2 transform hover:-translate-y-1">
                        <i class="fas fa-paper-plane"></i>
                        <span>Kirim Pesan</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
