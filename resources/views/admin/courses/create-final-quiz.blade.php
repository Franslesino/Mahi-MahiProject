@extends('layouts.admin')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.courses.final-quiz.edit', $kursus->id) }}" class="text-blue-600 hover:text-blue-700 mb-2 inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Pengaturan Final Quiz</span>
        </a>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Buat Final Quiz Baru</h2>
        <p class="text-gray-600 mt-1">{{ $kursus->judul }}</p>
        <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3 inline-flex items-start gap-2">
            <i class="fas fa-info-circle text-yellow-600 mt-0.5"></i>
            <p class="text-sm text-yellow-800">
                <strong>Catatan:</strong> Setiap kursus hanya dapat memiliki 1 final quiz. Setelah dibuat, Anda tidak dapat membuat quiz baru lagi, tetapi dapat menambah/mengurangi soal.
            </p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <ul class="mb-0 ml-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="bg-teal-600 text-white px-6 py-4 rounded-t-lg">
            <h5 class="text-lg font-semibold mb-0"><i class="fas fa-graduation-cap"></i> Informasi Quiz</h5>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.courses.final-quiz.store-quiz', $kursus->id) }}" method="POST" id="createQuizForm">
                @csrf

                <!-- Judul Quiz -->
                <div class="mb-6">
                    <label for="judul_quiz" class="block text-sm font-bold text-gray-700 mb-2">
                        Judul Quiz <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="judul_quiz" name="judul_quiz" 
                           value="{{ old('judul_quiz', 'Final Quiz - ' . $kursus->judul) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('judul_quiz') border-red-500 @enderror"
                           placeholder="Contoh: Final Quiz - Dasar Programming">
                    @error('judul_quiz')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="mb-6">
                    <label for="deskripsi" class="block text-sm font-bold text-gray-700 mb-2">
                        Deskripsi Quiz
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('deskripsi') border-red-500 @enderror"
                              placeholder="Berikan deskripsi singkat tentang final quiz ini...">{{ old('deskripsi', 'Ujian akhir untuk menguji pemahaman keseluruhan materi ' . $kursus->judul) }}</textarea>
                    @error('deskripsi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <small class="text-gray-600 text-sm">Deskripsi ini akan ditampilkan kepada peserta sebelum mengerjakan quiz.</small>
                </div>

                <!-- Durasi Quiz -->
                <div class="mb-6">
                    <label for="durasi_quiz" class="block text-sm font-bold text-gray-700 mb-2">
                        Durasi Quiz (menit)
                    </label>
                    <input type="number" id="durasi_quiz" name="durasi_quiz" 
                           value="{{ old('durasi_quiz', 60) }}" min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('durasi_quiz') border-red-500 @enderror"
                           placeholder="60">
                    <small class="text-gray-600 text-sm">Kosongkan jika tidak ada batas waktu. Peserta akan memiliki waktu terbatas untuk menyelesaikan quiz jika diisi.</small>
                    @error('durasi_quiz')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Information Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h6 class="font-semibold text-blue-800 mb-2">
                        <i class="fas fa-info-circle"></i> Langkah Selanjutnya
                    </h6>
                    <ul class="text-sm text-blue-700 space-y-1 ml-5">
                        <li>✓ Setelah quiz dibuat, Anda akan diarahkan ke halaman pengaturan</li>
                        <li>✓ Tambahkan soal-soal dari Bank Soal atau buat soal baru</li>
                        <li>✓ Atur nilai minimum kelulusan dan maksimal percobaan</li>
                        <li>✓ Aktifkan quiz saat siap untuk digunakan peserta</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.courses.final-quiz.edit', $kursus->id) }}" 
                       class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">
                        <i class="fas fa-check"></i> Buat Final Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
