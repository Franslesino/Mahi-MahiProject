{{-- resources/views/student/terms.blade.php --}}
@extends('layouts.app')

@section('title', 'Syarat & Ketentuan')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
            Syarat & Ketentuan Penggunaan Platform
        </h1>
        <p class="text-sm text-gray-500">
            Terakhir diperbarui: {{ now()->format('d F Y') }}
        </p>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 sm:px-8 py-6 border-b bg-gray-50">
            <p class="text-gray-700 text-sm">
                Dengan membuat akun dan menggunakan platform ini, kamu dianggap telah membaca, memahami,
                dan menyetujui Syarat & Ketentuan berikut.
            </p>
        </div>

        <div class="px-6 sm:px-8 py-6 space-y-6 text-sm leading-relaxed text-gray-700">

            {{-- 1. Akun Pengguna --}}
            <section>
                <h2 class="font-semibold text-gray-900 mb-2">1. Akun Pengguna</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Kamu wajib memberikan data yang benar, lengkap, dan dapat dipertanggungjawabkan.</li>
                    <li>Satu akun hanya boleh digunakan oleh satu orang dan tidak boleh dipinjamkan atau dipindahtangankan.</li>
                    <li>Kamu bertanggung jawab penuh atas aktivitas yang terjadi di dalam akunmu.</li>
                </ul>
            </section>

            {{-- 2. Akses & Penggunaan Platform --}}
            <section>
                <h2 class="font-semibold text-gray-900 mb-2">2. Akses & Penggunaan Platform</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Platform digunakan hanya untuk keperluan belajar dan aktivitas yang sesuai dengan hukum yang berlaku.</li>
                    <li>Kamu dilarang melakukan tindakan yang dapat merusak sistem, mengganggu kenyamanan pengguna lain, atau
                        mencoba mengakses area yang tidak diizinkan.</li>
                    <li>Admin berhak membatasi atau menghentikan akses jika ditemukan pelanggaran terhadap ketentuan ini.</li>
                </ul>
            </section>

            {{-- 3. Konten & Hak Cipta --}}
            <section>
                <h2 class="font-semibold text-gray-900 mb-2">3. Konten & Hak Cipta</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Seluruh materi (video, teks, kuis, modul, dan konten lain) dilindungi oleh hak cipta.</li>
                    <li>Kamu tidak diperbolehkan mengunduh, merekam, menyalin, membagikan, menjual, atau
                        mendistribusikan materi tanpa izin tertulis dari pemilik konten.</li>
                    <li>Pelanggaran hak cipta dapat berakibat pada penonaktifan akun dan/atau tindakan hukum.</li>
                </ul>
            </section>

            {{-- 4. Pembayaran & Langganan (jika ada) --}}
            <section>
                <h2 class="font-semibold text-gray-900 mb-2">4. Pembayaran & Langganan</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Biaya kursus, paket, atau langganan (jika ada) akan diinformasikan dengan jelas sebelum kamu melakukan pembayaran.</li>
                    <li>Transaksi yang sudah berhasil umumnya tidak dapat dibatalkan atau dikembalikan (non-refundable),
                        kecuali jika terdapat kesalahan dari pihak kami.</li>
                    <li>Kamu wajib menggunakan metode pembayaran yang sah dan legal.</li>
                </ul>
            </section>

            {{-- 5. Privasi & Data Pribadi --}}
            <section>
                <h2 class="font-semibold text-gray-900 mb-2">5. Privasi & Data Pribadi</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Kami mengumpulkan data seperti nama, email, dan aktivitas belajar untuk keperluan operasional platform.</li>
                    <li>Data pribadi akan dikelola sesuai dengan Kebijakan Privasi yang berlaku.</li>
                    <li>Kami tidak akan menjual data pribadimu kepada pihak ketiga tanpa persetujuan.</li>
                </ul>
            </section>

            {{-- 6. Perilaku Pengguna --}}
            <section>
                <h2 class="font-semibold text-gray-900 mb-2">6. Perilaku Pengguna</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Dilarang menggunakan bahasa yang tidak sopan, menghina, atau melecehkan pihak lain.</li>
                    <li>Dilarang mengunggah konten yang mengandung SARA, pornografi, kekerasan, atau melanggar hukum.</li>
                    <li>Pelanggaran dapat berakibat pada peringatan, pembatasan fitur, atau penutupan akun.</li>
                </ul>
            </section>

            {{-- 7. Perubahan Layanan --}}
            <section>
                <h2 class="font-semibold text-gray-900 mb-2">7. Perubahan Layanan</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Kami dapat melakukan pembaruan, penyesuaian, atau penghentian fitur tertentu sewaktu-waktu.</li>
                    <li>Perubahan akan diinformasikan melalui platform atau kanal komunikasi resmi lainnya.</li>
                </ul>
            </section>

            {{-- 8. Batasan Tanggung Jawab --}}
            <section>
                <h2 class="font-semibold text-gray-900 mb-2">8. Batasan Tanggung Jawab</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Kami berupaya menjaga platform tetap stabil dan aman, namun tidak menjamin 100% bebas dari gangguan teknis.</li>
                    <li>Kami tidak bertanggung jawab atas kerugian yang timbul akibat penyalahgunaan akun atau
                        pelanggaran yang dilakukan pengguna.</li>
                </ul>
            </section>

            {{-- 9. Perubahan Syarat & Ketentuan --}}
            <section>
                <h2 class="font-semibold text-gray-900 mb-2">9. Perubahan Syarat & Ketentuan</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Syarat & Ketentuan ini dapat diperbarui sewaktu-waktu untuk menyesuaikan dengan kebijakan atau regulasi baru.</li>
                    <li>Versi terbaru akan selalu ditampilkan pada halaman ini dan berlaku sejak tanggal pembaruan.</li>
                </ul>
            </section>

            {{-- 10. Kontak & Bantuan --}}
            <section>
                <h2 class="font-semibold text-gray-900 mb-2">10. Kontak & Bantuan</h2>
                <p>
                    Jika kamu memiliki pertanyaan terkait Syarat & Ketentuan ini, silakan hubungi tim kami melalui
                    email <span class="font-medium">support@upgreenius.test</span> atau menu bantuan yang tersedia di platform.
                </p>
            </section>

        </div>

        <div class="px-6 sm:px-8 py-4 border-t bg-gray-50 flex justify-end">
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-pnj-teal hover:text-pnj-blue">
                Kembali ke halaman sebelumnya
            </a>
        </div>
    </div>

</div>
@endsection
