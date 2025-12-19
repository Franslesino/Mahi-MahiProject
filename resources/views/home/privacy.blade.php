@extends('layouts.app')

@section('title', 'Kebijakan Privasi - UpGrennius')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-4">Kebijakan Privasi</h1>
        <p class="text-lg text-gray-600">Bagaimana kami melindungi dan mengelola data Anda di UpGrennius.</p>
        <div class="mt-4 text-sm text-gray-400">Terakhir diperbarui: {{ now()->format('d F Y') }}</div>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-gray-100 border border-gray-100 overflow-hidden">
        <div class="p-8 md:p-12 space-y-10">
            <section>
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-shield text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">1. Informasi yang Kami Kumpulkan</h2>
                </div>
                <p class="text-gray-600 leading-relaxed mb-4">Kami mengumpulkan informasi untuk memberikan layanan yang lebih baik kepada semua pengguna kami:</p>
                <ul class="list-disc pl-6 space-y-2 text-gray-600">
                    <li><strong>Data Profil:</strong> Nama lengkap, alamat email, nomor telepon, dan institusi.</li>
                    <li><strong>Aktivitas Belajar:</strong> Kursus yang diikuti, progres belajar, nilai kuis, dan sertifikat yang diraih.</li>
                    <li><strong>Informasi Teknis:</strong> Alamat IP, jenis perangkat, dan data penggunaan aplikasi melalui cookie.</li>
                </ul>
            </section>

            <section>
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 bg-teal-100 text-teal-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-magic text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">2. Bagaimana Kami Menggunakan Informasi</h2>
                </div>
                <ul class="list-disc pl-6 space-y-2 text-gray-600">
                    <li>Menyediakan, memelihara, dan meningkatkan kualitas platform kami.</li>
                    <li>Mengirimkan notifikasi terkait akun, progres kursus, dan pengumuman penting.</li>
                    <li>Menganalisis tren penggunaan untuk fitur pengembangan di masa depan.</li>
                    <li>Mengelola proses pendaftaran dan verifikasi sertifikat.</li>
                </ul>
            </section>

            <section>
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-lock text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">3. Keamanan Data</h2>
                </div>
                <p class="text-gray-600 leading-relaxed">Keamanan data Anda adalah prioritas kami. Kami menerapkan enkripsi data standar industri dan protokol keamanan yang ketat untuk mencegah akses, pengubahan, pengungkapan, atau penghancuran informasi yang tidak sah.</p>
            </section>

            <section>
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-cookie-bite text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">4. Penggunaan Cookie</h2>
                </div>
                <p class="text-gray-600 leading-relaxed">Kami menggunakan cookie untuk mengingat preferensi Anda dan meningkatkan pengalaman navigasi. Anda dapat mengatur browser Anda untuk menolak cookie, namun hal ini mungkin memengaruhi beberapa fungsi pada platform.</p>
            </section>

            <section>
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-edit text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">5. Perubahan Kebijakan</h2>
                </div>
                <p class="text-gray-600 leading-relaxed">Kebijakan Privasi ini dapat diperbarui sewaktu-waktu. Perubahan akan berlaku segera setelah dipublikasikan di halaman ini. Kami menyarankan Anda untuk meninjau halaman ini secara berkala.</p>
            </section>
        </div>
        <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex justify-center">
            <a href="{{ route('home') }}" class="text-pnj-teal font-bold hover:underline">Kembali ke Beranda</a>
        </div>
    </div>
</div>
@endsection
