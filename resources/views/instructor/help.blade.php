{{-- resources/views/instructor/help.blade.php --}}
@extends('layouts.instructor')

@section('content')
<div class="p-8 space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-start gap-3 mb-4">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                <i class="fas fa-life-ring text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Pusat Bantuan Instruktur</h1>
                <p class="text-gray-600">Ringkasan langkah cepat dan jawaban pertanyaan umum.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h2 class="font-semibold text-gray-800 mb-2">Langkah Cepat</h2>
                <ol class="text-sm text-gray-700 space-y-2 list-decimal list-inside">
                    <li>Buat kursus di menu <strong>Kursus Saya</strong>.</li>
                    <li>Tambah section dan materi (video/PDF/quiz).</li>
                    <li>Terbitkan kursus dan bagikan ke siswa.</li>
                </ol>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h2 class="font-semibold text-gray-800 mb-2">Materi & Quiz</h2>
                <ul class="text-sm text-gray-700 space-y-2 list-disc list-inside">
                    <li>Unggah video/PDF via <strong>Materials</strong>.</li>
                    <li>Buat bank soal, lalu assignment/quiz dari bank soal.</li>
                    <li>Pastikan status materi/quiz sudah <strong>Published</strong>.</li>
                </ul>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h2 class="font-semibold text-gray-800 mb-2">Pendapatan</h2>
                <ul class="text-sm text-gray-700 space-y-2 list-disc list-inside">
                    <li>Pendapatan dihitung dari transaksi berstatus <strong>paid</strong>.</li>
                    <li>Cek ringkasan di dashboard revenue dan performa kursus.</li>
                </ul>
            </div>
        </div>

        <div class="mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Pertanyaan Umum</h2>
            <div class="space-y-3">
                <details class="bg-white border border-gray-200 rounded-lg p-4">
                    <summary class="cursor-pointer font-medium text-gray-800">Materi saya tidak tampil ke siswa?</summary>
                    <p class="text-sm text-gray-700 mt-2">Pastikan kursus dan materi berstatus aktif/published, dan siswa sudah terdaftar (enrollment) pada kursus tersebut.</p>
                </details>
                <details class="bg-white border border-gray-200 rounded-lg p-4">
                    <summary class="cursor-pointer font-medium text-gray-800">Quiz tidak muncul?</summary>
                    <p class="text-sm text-gray-700 mt-2">Pastikan assignment memiliki soal dan statusnya published. Hubungkan assignment ke materi bertipe quiz.</p>
                </details>
                <details class="bg-white border border-gray-200 rounded-lg p-4">
                    <summary class="cursor-pointer font-medium text-gray-800">Pendapatan masih Rp 0?</summary>
                    <p class="text-sm text-gray-700 mt-2">Pendapatan dihitung dari transaksi paid untuk kursus Anda. Cek status transaksi dan pastikan kursus memiliki harga.</p>
                </details>
            </div>
        </div>
    </div>
</div>
@endsection
