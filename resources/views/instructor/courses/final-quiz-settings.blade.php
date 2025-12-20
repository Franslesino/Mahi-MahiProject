@extends('layouts.instructor')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex-1">
            <a href="{{ route('instructor.courses.show', $kursus->id) }}" class="text-blue-600 hover:text-blue-700 mb-2 inline-flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <h2 class="text-2xl font-bold text-gray-800 mt-2">Pengaturan Final Quiz</h2>
            <p class="text-gray-600 mt-1">{{ $kursus->judul }}</p>
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

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Item #23: Warning message for deactivating quiz with active participants -->
    @if(session('warning'))
        <div class="bg-amber-100 border border-amber-400 text-amber-800 px-4 py-3 rounded relative mb-4" role="alert">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-amber-600 text-xl mt-0.5"></i>
                <div>
                    <p class="font-semibold">Perhatian!</p>
                    <p class="text-sm">{{ session('warning') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg">
            <h5 class="text-lg font-semibold mb-0"><i class="fas fa-cog"></i> Konfigurasi Final Quiz</h5>
        </div>
        <div class="p-6">
            <form action="{{ route('instructor.courses.final-quiz.update', $kursus->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Require Final Quiz -->
                <div class="mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="require_final_quiz" 
                               name="require_final_quiz" value="1"
                               {{ old('require_final_quiz', $kursus->require_final_quiz) ? 'checked' : '' }}
                               onchange="toggleFinalQuizSettings()"
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="require_final_quiz" class="ml-2 text-sm font-bold text-gray-900">
                            Aktifkan Final Quiz untuk Kursus Ini
                        </label>
                    </div>
                    <small class="text-gray-600 text-sm ml-6 block mt-1">
                        Jika diaktifkan, peserta harus lulus final quiz untuk mendapatkan sertifikat.
                    </small>
                </div>

                <!-- Selalu tampilkan blok pengaturan agar instruktur jelas melihat dropdown quiz -->
                <div id="finalQuizSettings" class="space-y-6" style="display:block;">
                    <!-- Pilih Quiz -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="final_quiz_id" class="block text-sm font-bold text-gray-700">
                                Pilih Quiz sebagai Final Quiz <span class="text-red-500">*</span>
                            </label>
                            @if(!$kursus->final_quiz_id)
                                <a href="{{ route('instructor.courses.final-quiz.create-quiz', $kursus->id) }}" 
                                   class="px-4 py-2 bg-teal-600 text-white text-sm rounded-lg hover:bg-teal-700 transition inline-flex items-center gap-2">
                                    <i class="fas fa-plus"></i> Buat Quiz Baru
                                </a>
                            @else
                                <span class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg inline-flex items-center gap-2">
                                    <i class="fas fa-info-circle"></i> 1 Quiz Sudah Dibuat
                                </span>
                            @endif
                        </div>
                        <select id="final_quiz_id" name="final_quiz_id"
                                {{ $kursus->final_quiz_id ? 'disabled' : '' }}
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('final_quiz_id') border-red-500 @enderror disabled:bg-gray-100 disabled:cursor-not-allowed">
                            <option value="">-- Pilih Quiz --</option>
                            @forelse($availableQuizzes as $quiz)
                                <option value="{{ $quiz->id }}" 
                                        {{ old('final_quiz_id', $kursus->final_quiz_id) == $quiz->id ? 'selected' : '' }}>
                                    {{ $quiz->judul_quiz }}
                                    @if($quiz->is_final_quiz)
                                        (Final Quiz Saat Ini)
                                    @endif
                                </option>
                            @empty
                                <option value="" disabled>Belum ada quiz tersedia</option>
                            @endforelse
                        </select>
                        @if($kursus->final_quiz_id)
                            <!-- Hidden input to preserve value when dropdown is disabled -->
                            <input type="hidden" name="final_quiz_id" value="{{ $kursus->final_quiz_id }}">
                            <p class="text-gray-600 text-sm mt-1">
                                <i class="fas fa-lock"></i> Quiz sudah dipilih dan tidak dapat diubah. Gunakan fitur tambah/hapus soal untuk mengelola pertanyaan.
                            </p>
                        @endif
                        @error('final_quiz_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <p class="text-sm text-blue-700 mb-2">
                                <i class="fas fa-lightbulb"></i> <strong>Informasi Penting:</strong>
                            </p>
                            <ul class="text-sm text-blue-600 ml-5 space-y-1">
                                @if(!$kursus->final_quiz_id)
                                    <li>• Setiap kursus hanya dapat memiliki <strong>1 final quiz</strong></li>
                                    <li>• Klik tombol <strong>"Buat Quiz Baru"</strong> untuk membuat final quiz dengan soal dari bank soal</li>
                                    <li>• Atau pilih quiz yang sudah ada dari dropdown di atas</li>
                                @else
                                    <li>• Setiap kursus hanya dapat memiliki <strong>1 final quiz</strong></li>
                                    <li>• Gunakan tombol <strong>"Tambah Soal dari Bank Soal"</strong> untuk menambah pertanyaan</li>
                                    <li>• Nonaktifkan quiz terlebih dahulu untuk mengubah soal</li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <!-- Nilai Minimum Kelulusan -->
                    <div>
                        <label for="min_passing_score" class="block text-sm font-bold text-gray-700 mb-2">
                            Nilai Minimum Kelulusan (%) <span class="text-red-500">*</span>
                        </label>
                        <div class="flex">
                            <input type="number" id="min_passing_score" name="min_passing_score" 
                                   min="0" max="100" step="0.01"
                                   value="{{ old('min_passing_score', $kursus->min_passing_score ?? 70) }}"
                                   required
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('min_passing_score') border-red-500 @enderror">
                            <span class="inline-flex items-center px-4 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-gray-700">%</span>
                        </div>
                        @error('min_passing_score')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <small class="text-gray-600 text-sm block mt-2">
                            Nilai minimum yang harus dicapai peserta untuk dinyatakan lulus (0-100).
                        </small>
                    </div>

                    <!-- Maksimal Percobaan -->
                    <div>
                        <label for="max_quiz_attempts" class="block text-sm font-bold text-gray-700 mb-2">
                            Maksimal Percobaan <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="max_quiz_attempts" name="max_quiz_attempts" 
                               min="1" max="10"
                               value="{{ old('max_quiz_attempts', $kursus->max_quiz_attempts ?? 3) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('max_quiz_attempts') border-red-500 @enderror">
                        @error('max_quiz_attempts')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <small class="text-gray-600 text-sm block mt-2">
                            Berapa kali peserta dapat mengulang quiz jika belum lulus (1-10 kali).
                        </small>
                    </div>

                    <!-- Durasi Quiz -->
                    <div>
                        <label for="durasi_quiz" class="block text-sm font-bold text-gray-700 mb-2">
                            Durasi Quiz (menit)
                        </label>
                        <div class="flex">
                            <input type="number" id="durasi_quiz" name="durasi_quiz" 
                                   min="1"
                                   value="{{ old('durasi_quiz', optional($kursus->finalQuiz)->durasi_quiz) }}"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('durasi_quiz') border-red-500 @enderror"
                                   placeholder="Contoh: 60">
                            <span class="inline-flex items-center px-4 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-gray-700">menit</span>
                        </div>
                        @error('durasi_quiz')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <small class="text-gray-600 text-sm block mt-2">
                            Batasan waktu pengerjaan quiz. Kosongkan jika tidak ada batas waktu.
                        </small>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('instructor.courses.show', $kursus->id) }}" 
                       class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-save"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Card -->
    @if($kursus->require_final_quiz && $kursus->final_quiz_id)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
            <div class="bg-teal-600 text-white px-6 py-4 rounded-t-lg">
                <h5 class="text-lg font-semibold mb-0"><i class="fas fa-info-circle"></i> Informasi Final Quiz</h5>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-700"><strong>Quiz:</strong> {{ $kursus->finalQuiz->judul_quiz }}</p>
                        <p class="text-gray-700"><strong>Nilai Minimum:</strong> {{ $kursus->min_passing_score }}%</p>
                    </div>
                    <div>
                        <p class="text-gray-700"><strong>Maksimal Percobaan:</strong> {{ $kursus->max_quiz_attempts }}x</p>
                        <p class="text-gray-700"><strong>Durasi:</strong> 
                            @if(optional($kursus->finalQuiz)->durasi_quiz)
                                {{ $kursus->finalQuiz->durasi_quiz }} menit
                            @else
                                <span class="text-gray-500 italic">Tidak ada batas waktu</span>
                            @endif
                        </p>
                        <p class="text-gray-700"><strong>Status Quiz:</strong> 
                            @if(optional($kursus->finalQuiz)->is_active)
                                <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-semibold">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            @else
                                <span class="inline-block bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm font-semibold">
                                    <i class="fas fa-pause-circle"></i> Nonaktif
                                </span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Warning if Quiz is Active -->
                @if(optional($kursus->finalQuiz)->is_active)
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mt-1"></i>
                            <div class="flex-1">
                                <p class="text-yellow-800 font-semibold mb-1">Quiz Sedang Aktif</p>
                                <p class="text-yellow-700 text-sm">
                                    Soal tidak dapat diubah saat quiz aktif. Nonaktifkan terlebih dahulu untuk menambah/menghapus soal.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-4 flex flex-wrap gap-3">
                    <!-- Toggle Activation Button -->
                    <form action="{{ route('instructor.courses.final-quiz.toggle-activation', $kursus->id) }}" method="POST" class="inline"
                          data-confirm-title="{{ optional($kursus->finalQuiz)->is_active ? 'Nonaktifkan final quiz?' : 'Aktifkan final quiz?' }}"
                          data-confirm="Apakah Anda yakin ingin {{ optional($kursus->finalQuiz)->is_active ? 'menonaktifkan' : 'mengaktifkan' }} final quiz?">
                        @csrf
                        <button type="submit" 
                                class="px-6 py-2 {{ optional($kursus->finalQuiz)->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition inline-flex items-center gap-2">
                            <i class="fas fa-{{ optional($kursus->finalQuiz)->is_active ? 'pause' : 'play' }}-circle"></i>
                            {{ optional($kursus->finalQuiz)->is_active ? 'Nonaktifkan Quiz' : 'Aktifkan Quiz' }}
                        </button>
                    </form>

                    <a href="{{ route('instructor.courses.final-quiz.statistics', $kursus->id) }}" 
                       class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition inline-flex items-center gap-2">
                        <i class="fas fa-chart-bar"></i> Lihat Statistik
                    </a>
                    
                    <button type="button" 
                            onclick="openCreateQuestionModal(this)"
                            {{ optional($kursus->finalQuiz)->is_active ? 'disabled' : '' }}
                            class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-plus-circle"></i> Tambah Soal Baru
                    </button>
                    
                    <button type="button" 
                            onclick="openImportModal(this)"
                            {{ optional($kursus->finalQuiz)->is_active ? 'disabled' : '' }}
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-file-import"></i> Tambah Soal dari Bank Soal
                    </button>
                </div>
            </div>
        </div>

        <!-- Soal yang Sudah Ada di Final Quiz -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
            <div class="bg-purple-600 text-white px-6 py-4 rounded-t-lg flex items-center justify-between">
                <h5 class="text-lg font-semibold mb-0">
                    <i class="fas fa-list-ol"></i> Soal Final Quiz ({{ $kursus->finalQuiz->soal->count() }} soal)
                </h5>
                <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm">
                    Total Soal: {{ $kursus->finalQuiz->soal->count() }}
                </span>
            </div>
            <div class="p-6">
                @if($kursus->finalQuiz->soal->count() > 0)
                    <div class="space-y-4">
                        @foreach($kursus->finalQuiz->soal->sortBy(function($q) { return $q->pivot->urutan; }) as $index => $question)
                            <div class="border-2 border-gray-200 rounded-xl p-5 hover:border-purple-400 transition-all bg-gradient-to-r from-white to-gray-50">
                                <div class="flex items-start gap-4">
                                    <!-- Nomor Urut -->
                                    <div class="flex-shrink-0">
                                        <span class="flex items-center justify-center w-12 h-12 bg-purple-600 text-white rounded-full font-bold text-lg shadow-md">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>
                                    
                                    <!-- Konten Soal -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center flex-wrap gap-2 mb-3">
                                            <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full uppercase">
                                                {{ str_replace('_', ' ', $question->type) }}
                                            </span>
                                            @if($question->points)
                                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                                    <i class="fas fa-star"></i> {{ $question->points }} poin
                                                </span>
                                            @endif
                                            @if($question->questionBank)
                                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                                    <i class="fas fa-folder"></i> {{ $question->questionBank->title }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="text-gray-800 font-medium text-base leading-relaxed mb-4">
                                            {!! nl2br(e($question->question_text)) !!}
                                        </div>

                                        <!-- Opsi Jawaban -->
                                        @if($question->options && $question->options->count() > 0)
                                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                                <p class="text-sm font-semibold text-gray-700 mb-3">
                                                    <i class="fas fa-list-ul"></i> Opsi Jawaban:
                                                </p>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    @foreach($question->options as $option)
                                                        <div class="flex items-start gap-2 text-sm {{ $option->is_correct ? 'text-green-700 font-semibold bg-green-50 p-2 rounded' : 'text-gray-600 p-2' }}">
                                                            @if($option->is_correct)
                                                                <i class="fas fa-check-circle text-green-600 mt-0.5 flex-shrink-0"></i>
                                                            @else
                                                                <i class="far fa-circle text-gray-400 mt-0.5 flex-shrink-0"></i>
                                                            @endif
                                                            <span class="flex-1 break-words">{{ $option->option_text }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex-shrink-0">
                                        @if(optional($kursus->finalQuiz)->is_active)
                                            <button type="button" 
                                                    disabled
                                                    title="Quiz sedang aktif. Nonaktifkan terlebih dahulu untuk menghapus soal"
                                                    class="px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed text-sm font-semibold">
                                                <i class="fas fa-lock"></i> Terkunci
                                            </button>
                                        @else
                                            <form action="{{ route('instructor.courses.final-quiz.remove-question', [$kursus->id, $question->id]) }}" 
                                                  method="POST" 
                                                  data-confirm-title="Hapus soal dari final quiz?"
                                                  data-confirm="Yakin ingin menghapus soal ini dari final quiz?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-semibold">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <i class="fas fa-inbox text-6xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 text-lg font-semibold mb-2">Belum ada soal di final quiz</p>
                        <p class="text-gray-500 mb-4">Tambahkan soal dari Question Banks untuk memulai</p>
                        <button type="button" 
                                onclick="window.location.href='{{ route('instructor.courses.final-quiz.create-quiz', $kursus->id) }}'"
                                class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                            <i class="fas fa-plus"></i> Tambah Soal Sekarang
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Modal Import Soal dari Bank Soal -->
    @if($kursus->require_final_quiz && $kursus->final_quiz_id)
        <div id="importModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-file-import text-blue-600"></i> Import Soal dari Bank Soal
                    </h3>
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <form action="{{ route('instructor.courses.final-quiz.import-from-bank', $kursus->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-3">
                            <p class="text-sm text-gray-600">
                                Pilih soal dari bank soal untuk ditambahkan ke final quiz ini
                            </p>
                            <div class="flex gap-2">
                                <button type="button" onclick="selectAllImport()" class="text-sm text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-check-double"></i> Pilih Semua
                                </button>
                                <button type="button" onclick="deselectAllImport()" class="text-sm text-red-600 hover:text-red-700">
                                    <i class="fas fa-times"></i> Batal Pilih
                                </button>
                            </div>
                        </div>

                        <input type="text" id="searchImportQuestion" placeholder="Cari soal..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 mb-3"
                               onkeyup="filterImportQuestions()">

                        <div id="importQuestionList" class="space-y-4 max-h-96 overflow-y-auto border border-gray-300 rounded-lg p-4">
                            @php
                                // Ambil semua question banks dengan soal-soalnya
                                $questionBanksWithQuestions = \App\Models\QuestionBank::with(['questions.options'])
                                    ->where(function($q) {
                                        $q->where('created_by', Auth::id())
                                          ->orWhere('is_public', true);
                                    })
                                    ->whereHas('questions') // Hanya bank yang punya soal
                                    ->orderBy('title')
                                    ->get();
                            @endphp

                            @forelse($questionBanksWithQuestions as $bank)
                                <div class="question-bank-group border-2 border-gray-200 rounded-xl overflow-hidden" data-bank-name="{{ strtolower($bank->title) }}">
                                    <!-- Bank Header -->
                                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-4 py-3 border-b border-gray-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-emerald-600 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-folder-open text-white"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-gray-800">{{ $bank->title }}</h4>
                                                    <p class="text-xs text-gray-500">{{ $bank->questions->count() }} soal tersedia</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button type="button" onclick="selectBankQuestions({{ $bank->id }})" 
                                                        class="text-xs px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full hover:bg-emerald-200 transition">
                                                    <i class="fas fa-check-double mr-1"></i> Pilih Semua
                                                </button>
                                                <button type="button" onclick="toggleBankCollapse({{ $bank->id }})"
                                                        class="text-gray-500 hover:text-gray-700 transition">
                                                    <i class="fas fa-chevron-down" id="bank-collapse-icon-{{ $bank->id }}"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Bank Questions -->
                                    <div class="p-3 space-y-2 bg-white" id="bank-questions-{{ $bank->id }}">
                                        @foreach($bank->questions as $question)
                                            <div class="import-question-item bg-gray-50 border border-gray-200 rounded-lg p-3 hover:border-emerald-400 hover:bg-emerald-50/30 transition" 
                                                 data-bank-id="{{ $bank->id }}">
                                                <div class="flex items-start gap-3">
                                                    <input type="checkbox" name="questions[]" value="{{ $question->id }}" 
                                                           id="import_question_{{ $question->id }}"
                                                           data-bank="{{ $bank->id }}"
                                                           class="mt-1 w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                                    <label for="import_question_{{ $question->id }}" class="flex-1 cursor-pointer">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">
                                                                {{ strtoupper(str_replace('_', ' ', $question->type)) }}
                                                            </span>
                                                            @if($question->points)
                                                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs rounded-full">
                                                                    {{ $question->points }} poin
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="text-gray-800 text-sm import-question-text line-clamp-2">
                                                            {{ Str::limit(strip_tags($question->question_text), 150) }}
                                                        </div>
                                                        @if($question->options->count() > 0)
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                <i class="fas fa-list-ul mr-1"></i>
                                                                {{ $question->options->count() }} opsi jawaban
                                                            </div>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>Belum ada soal di Question Banks</p>
                                    <p class="text-sm mt-1">Buat bank soal dan tambahkan soal terlebih dahulu</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4 border-t">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-check"></i> Import Soal Terpilih
                        </button>
                        <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                                class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Tambah Soal Baru -->
    @if($kursus->require_final_quiz && $kursus->final_quiz_id)
        <div id="createQuestionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-5 mx-auto p-6 border w-11/12 max-w-5xl shadow-lg rounded-lg bg-white my-10">
                <div class="flex justify-between items-center pb-4 border-b mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-plus-circle text-purple-600"></i> Tambah Soal Baru ke Final Quiz
                    </h3>
                    <button type="button" onclick="closeCreateQuestionModal()"
                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <form action="{{ route('instructor.courses.final-quiz.store-new-question', $kursus->id) }}" method="POST" id="createQuestionForm">
                    @csrf

                    <div class="space-y-6 max-h-[65vh] overflow-y-auto px-2">
                        <!-- Tipe Soal -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Tipe Soal <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="question_type" required
                                    onchange="updateOptionsCount()"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-base">
                                <option value="multiple_choice">Multiple Choice (Pilihan Ganda)</option>
                                <option value="true_false">True/False (Benar/Salah)</option>
                                <option value="essay">Essay (Esai)</option>
                            </select>
                        </div>

                        <!-- Pertanyaan -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Pertanyaan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="question_text" required rows="5"
                                      placeholder="Tulis pertanyaan di sini..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-base"></textarea>
                        </div>

                        <!-- Poin -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Poin <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="points" value="1" min="1" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-base">
                            <small class="text-gray-600 text-sm">Nilai poin untuk soal ini</small>
                        </div>

                        <!-- Opsi Jawaban (untuk multiple choice dan true/false) -->
                        <div id="optionsContainer">
                            <label class="block text-sm font-bold text-gray-700 mb-3">
                                Opsi Jawaban <span class="text-red-500">*</span>
                            </label>
                            <div id="optionsList" class="space-y-3 mb-3">
                                <!-- Options will be dynamically added here -->
                            </div>
                            <button type="button" onclick="addOption()" id="addOptionBtn"
                                    class="mt-2 px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium inline-flex items-center gap-2">
                                <i class="fas fa-plus"></i> Tambah Opsi
                            </button>
                        </div>

                        <!-- Simpan ke Bank Soal -->
                        <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-5">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" name="save_to_bank" value="1" id="save_to_bank"
                                       onchange="toggleBankSelection()"
                                       class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="flex-1">
                                    <label for="save_to_bank" class="font-semibold text-gray-800 cursor-pointer text-base">
                                        <i class="fas fa-save text-blue-600"></i> Simpan soal ini ke Bank Soal
                                    </label>
                                    <p class="text-sm text-gray-600 mt-2">
                                        Jika dicentang, soal ini akan disimpan ke Question Bank dan dapat digunakan kembali untuk quiz lain
                                    </p>
                                </div>
                            </div>

                            <!-- Pilih Bank Soal (muncul jika checkbox dicentang) -->
                            <div id="bankSelectionContainer" class="hidden mt-4 pt-4 border-t border-blue-200">
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Pilih Bank Soal <span class="text-red-500">*</span>
                                </label>
                                <select name="question_bank_id" id="question_bank_id"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                                    <option value="">-- Pilih Bank Soal --</option>
                                    @php
                                        $myQuestionBanks = \App\Models\QuestionBank::where('created_by', Auth::id())
                                            ->orderBy('title')
                                            ->get();
                                    @endphp
                                    @foreach($myQuestionBanks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->title }}</option>
                                    @endforeach
                                </select>
                                <small class="text-gray-600">Pilih bank soal untuk menyimpan pertanyaan ini</small>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-6 mt-6 border-t">
                        <button type="submit" class="px-8 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold text-base inline-flex items-center gap-2">
                            <i class="fas fa-check"></i> Simpan & Tambahkan ke Quiz
                        </button>
                        <button type="button" onclick="closeCreateQuestionModal()"
                                class="px-8 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-semibold text-base inline-flex items-center gap-2">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
@endif
</div>

<script>
const finalQuizActive = @json(optional($kursus->finalQuiz)->is_active ?? false);
function openImportModal(btn) {
    if (btn?.hasAttribute('disabled')) return;
    document.getElementById('importModal').classList.remove('hidden');
}

function selectAllImport() {
    document.querySelectorAll('#importQuestionList input[name="questions[]"]').forEach(cb => cb.checked = true);
}

function deselectAllImport() {
    document.querySelectorAll('#importQuestionList input[name="questions[]"]').forEach(cb => cb.checked = false);
}

// Item #27: Pilih semua soal dari bank tertentu
function selectBankQuestions(bankId) {
    document.querySelectorAll(`#importQuestionList input[data-bank="${bankId}"]`).forEach(cb => cb.checked = true);
}

// Item #27: Toggle collapse bank soal
function toggleBankCollapse(bankId) {
    const questionsContainer = document.getElementById(`bank-questions-${bankId}`);
    const icon = document.getElementById(`bank-collapse-icon-${bankId}`);
    
    if (questionsContainer.style.display === 'none') {
        questionsContainer.style.display = 'block';
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');
    } else {
        questionsContainer.style.display = 'none';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');
    }
}

function filterImportQuestions() {
    const searchTerm = document.getElementById('searchImportQuestion').value.toLowerCase();
    const bankGroups = document.querySelectorAll('.question-bank-group');
    
    bankGroups.forEach(bankGroup => {
        const bankName = bankGroup.dataset.bankName || '';
        const questions = bankGroup.querySelectorAll('.import-question-item');
        let visibleCount = 0;
        
        questions.forEach(question => {
            const text = question.querySelector('.import-question-text').textContent.toLowerCase();
            if (text.includes(searchTerm) || bankName.includes(searchTerm)) {
                question.style.display = 'block';
                visibleCount++;
            } else {
                question.style.display = 'none';
            }
        });
        
        // Sembunyikan seluruh bank jika tidak ada soal yang cocok
        if (visibleCount === 0 && searchTerm !== '') {
            bankGroup.style.display = 'none';
        } else {
            bankGroup.style.display = 'block';
        }
    });
}

function toggleFinalQuizSettings() {
    const checkbox = document.getElementById('require_final_quiz');
    const settings = document.getElementById('finalQuizSettings');
    
    if (checkbox.checked) {
        settings.style.display = 'block';
        settings.classList.remove('hidden');
    } else {
        settings.style.display = 'none';
        settings.classList.add('hidden');
    }
}

// Blok pengaturan selalu ditampilkan; fungsi ini dipertahankan jika ingin sembunyikan/ tampilkan secara manual
document.addEventListener('DOMContentLoaded', () => {});

// Create Question Modal Functions
let optionCounter = 0;

function openCreateQuestionModal(btn) {
    if (btn?.hasAttribute('disabled')) return;
    document.getElementById('createQuestionModal').classList.remove('hidden');
    optionCounter = 0;
    updateOptionsCount();
}

function closeCreateQuestionModal() {
    document.getElementById('createQuestionModal').classList.add('hidden');
    document.getElementById('createQuestionForm').reset();
    document.getElementById('optionsList').innerHTML = '';
    document.getElementById('bankSelectionContainer').classList.add('hidden');
}

function updateOptionsCount() {
    const type = document.getElementById('question_type').value;
    const optionsContainer = document.getElementById('optionsContainer');
    const optionsList = document.getElementById('optionsList');
    const addOptionBtn = document.getElementById('addOptionBtn');
    
    optionsList.innerHTML = '';
    optionCounter = 0;
    
    if (type === 'essay') {
        optionsContainer.style.display = 'none';
    } else {
        optionsContainer.style.display = 'block';
        
        if (type === 'true_false') {
            // Add True and False options
            addTrueFalseOptions();
            addOptionBtn.style.display = 'none';
        } else {
            // Multiple choice - add 4 default options
            for (let i = 0; i < 4; i++) {
                addOption();
            }
            addOptionBtn.style.display = 'block';
        }
    }
}

function addTrueFalseOptions() {
    const optionsList = document.getElementById('optionsList');
    
    // True option
    const trueDiv = createOptionElement('Benar', true);
    optionsList.appendChild(trueDiv);
    
    // False option
    const falseDiv = createOptionElement('Salah', false);
    optionsList.appendChild(falseDiv);
}

function addOption() {
    optionCounter++;
    const optionsList = document.getElementById('optionsList');
    const optionDiv = createOptionElement('', false);
    optionsList.appendChild(optionDiv);
}

function createOptionElement(text = '', isFixed = false) {
    const div = document.createElement('div');
    div.className = 'flex items-center gap-3 bg-white p-4 rounded-lg border-2 border-gray-200 hover:border-purple-300 transition';
    div.id = `option_${optionCounter}`;
    
    const radioName = 'correct_option';
    const optionId = `opt_${optionCounter}`;
    
    div.innerHTML = `
        <input type="radio" name="${radioName}" value="${optionCounter}" required
               class="w-5 h-5 text-purple-600 border-gray-300 focus:ring-purple-500 cursor-pointer">
        <input type="text" name="options[]" value="${text}" ${isFixed ? 'readonly' : ''} required
               placeholder="Tulis opsi jawaban..."
               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-base ${isFixed ? 'bg-gray-50' : ''}">
        ${!isFixed ? `<button type="button" onclick="removeOption(${optionCounter})"
                class="px-4 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm font-medium inline-flex items-center gap-2">
            <i class="fas fa-trash"></i> Hapus
        </button>` : ''}
    `;
    
    optionCounter++;
    return div;
}

function removeOption(id) {
    const element = document.getElementById(`option_${id}`);
    if (element) {
        element.remove();
    }
}

function toggleBankSelection() {
    const checkbox = document.getElementById('save_to_bank');
    const container = document.getElementById('bankSelectionContainer');
    const select = document.getElementById('question_bank_id');
    
    if (checkbox.checked) {
        container.classList.remove('hidden');
        select.required = true;
    } else {
        container.classList.add('hidden');
        select.required = false;
    }
}

</script>
@endsection
