@extends('layouts.app')

@section('title', 'Hasil Quiz - ' . ($material->judul ?? $material->title))

@section('content')
<!-- ================================
     HALAMAN HASIL QUIZ
     Menampilkan skor dan ringkasan jawaban dari quiz yang telah dikerjakan
     ================================ -->
<div class="min-h-screen bg-[#f4f2f0] py-8">
    <div class="max-w-6xl mx-auto px-4">
        <!-- HEADER: Tombol Kembali, Skor, dan Aksi -->
        <div class="flex items-center justify-between mb-6">
            <!-- Tombol Kembali ke Kursus -->
            <a href="{{ route('student.course.learn', $course) }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900 transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Kursus</span>
            </a>
            
            <!-- Tampilkan Skor Quiz -->
            <div class="flex items-center gap-4 text-gray-700">
                <span class="font-semibold">Skor:</span>
                <div class="flex items-center gap-2">
                    <!-- Jika skor sudah dihitung, tampilkan dalam persentase, jika belum tampilkan "Pending" -->
                    <span class="text-xl font-bold">{{ $score !== null ? $score . '%' : 'Pending' }}</span>
                </div>
            </div>
            
            <!-- Tombol Aksi: Coba Lagi (jika diizinkan) dan Lanjut Materi -->
            <div class="flex items-center gap-3">
                @if(!empty($canRetake))
                    <!-- Tombol "Coba Lagi": Membuka modal konfirmasi untuk retake quiz -->
                    <button type="button"
                       onclick="openQuizModal('{{ route('courses.materials.quiz', [$course, $material->id]) }}?retake=1')"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-amber-100 text-amber-800 rounded-lg text-sm font-semibold hover:bg-amber-200 transition">
                        <i class="fas fa-redo"></i>
                        Coba Lagi
                    </button>
                @endif
                
                <!-- Tombol "Lanjut Materi Berikutnya": Membawa user ke halaman course learning -->
                <a href="{{ route('student.course.learn', $course) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-700 text-white rounded-lg text-sm font-semibold hover:bg-emerald-800 transition">
                    <span>Lanjut Materi Berikutnya</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- CONTAINER HASIL QUIZ -->
        <div class="bg-white rounded-2xl shadow p-6">
            <!-- Judul dan Deskripsi Halaman Hasil -->
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Quiz {{ $material->judul ?? $material->title }}</h2>
                <p class="text-sm text-gray-600">Ringkasan jawaban Anda beserta koreksi</p>
            </div>

            <!-- LIST SOAL DAN JAWABAN -->
            <div class="divide-y divide-gray-200">
                <!-- Loop setiap soal dari hasil quiz -->
                @foreach($results as $idx => $res)
                    <!-- Kontainer Setiap Soal -->
                    <div class="py-4 flex flex-col sm:flex-row sm:items-start sm:gap-4">
                        <!-- NOMOR SOAL: Indikator visual apakah jawaban benar, salah, atau belum dijawab -->
                        <div class="w-12 h-12 rounded-full {{ $res['is_correct'] === true ? 'bg-green-100' : ($res['is_correct'] === false ? 'bg-red-100' : 'bg-gray-100') }} flex items-center justify-center font-semibold {{ $res['is_correct'] === true ? 'text-green-800' : ($res['is_correct'] === false ? 'text-red-800' : 'text-gray-800') }} mb-3 sm:mb-0">
                            <!-- Tampilkan nomor soal mulai dari 1 -->
                            {{ $idx + 1 }}
                        </div>
                        
                        <!-- KONTEN SOAL DAN JAWABAN -->
                        <div class="flex-1 min-w-0 space-y-2">
                            <!-- Teks Soal -->
                            <p class="text-gray-900 font-medium">{{ $res['text'] }}</p>
                            
                            <!-- BAGIAN JAWABAN USER -->
                            @if($res['user_answer'])
                                <!-- Tampilkan jawaban user dengan indikator benar/salah -->
                                <p class="text-sm {{ $res['is_correct'] === true ? 'text-green-600' : 'text-red-600' }}">
                                    <span class="font-medium">Jawaban Anda:</span> {{ $res['user_answer'] }}
                                    @if($res['is_correct'] === true)
                                        <!-- Icon centang jika benar -->
                                        <i class="fas fa-check-circle ml-1"></i>
                                    @else
                                        <!-- Icon silang jika salah -->
                                        <i class="fas fa-times-circle ml-1"></i>
                                    @endif
                                </p>
                            @else
                                <!-- Tampilkan peringatan jika soal belum dijawab -->
                                <p class="text-sm text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>Anda belum menjawab soal ini.
                                </p>
                            @endif
                            
                            <!-- BAGIAN KOREKSI: Tampilkan jawaban benar jika user menjawab salah -->
                            @if($res['is_correct'] === false && $res['correct_answer'])
                                <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <p class="text-sm text-green-800">
                                        <i class="fas fa-lightbulb mr-1 text-green-600"></i>
                                        <span class="font-semibold">Jawaban yang benar:</span> {{ $res['correct_answer'] }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- ICON STATUS JAWABAN (di sebelah kanan) -->
                        <div class="mt-3 sm:mt-0">
                            @if($res['is_correct'] === true)
                                <!-- Icon centang besar untuk jawaban benar -->
                                <span class="text-green-600 text-2xl"><i class="fas fa-check-circle"></i></span>
                            @elseif($res['is_correct'] === false)
                                <!-- Icon silang besar untuk jawaban salah -->
                                <span class="text-red-500 text-2xl"><i class="fas fa-times-circle"></i></span>
                            @else
                                <!-- Icon tanda tanya untuk soal belum dijawab -->
                                <span class="text-gray-400 text-2xl"><i class="fas fa-question-circle"></i></span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- ================================
     MODAL KONFIRMASI RETAKE QUIZ
     Modal ini muncul ketika user klik tombol "Coba Lagi"
     ================================ -->
<div id="quizInfoModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <!-- HEADER MODAL -->
        <div class="text-center mb-6">
            <!-- Icon Retake Quiz -->
            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-redo text-amber-600 text-2xl"></i>
            </div>
            <!-- Judul Modal -->
            <h3 class="text-xl font-bold text-gray-900 mb-2">Coba Lagi Quiz</h3>
            <!-- Deskripsi Modal -->
            <p class="text-gray-600 text-sm">Anda akan mengerjakan ulang quiz ini dari awal</p>
        </div>
        
        <!-- INFO BOX: Menampilkan detail quiz -->
        <div class="bg-gray-50 rounded-xl p-4 mb-6 space-y-3">
            <!-- Tampilkan jumlah total soal dalam quiz -->
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600"><i class="fas fa-list mr-2 text-purple-500"></i>Jumlah Soal</span>
                <span class="font-semibold text-gray-900">{{ count($results) }} soal</span>
            </div>
        </div>
        
        <!-- TOMBOL AKSI: Batal atau Mulai Quiz -->
        <div class="flex gap-3">
            <!-- Tombol Batal: Menutup modal tanpa mengerjakan quiz -->
            <button type="button" onclick="closeQuizModal()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                Batal
            </button>
            <!-- Tombol Mulai Quiz: Mengarahkan ke halaman quiz dengan parameter retake=1 -->
            <a id="quizStartLink" href="#" class="flex-1 px-4 py-3 bg-amber-600 text-white rounded-xl font-semibold hover:bg-amber-700 transition text-center">
                Mulai Quiz
            </a>
        </div>
    </div>
</div>

<!-- SCRIPT: Fungsi untuk membuka dan menutup modal -->
<script>
    /**
     * Fungsi openQuizModal: Membuka modal konfirmasi retake quiz
     * @param {string} url - URL quiz yang akan dikerjakan ulang
     */
    function openQuizModal(url) {
        // Set href pada tombol "Mulai Quiz" dengan URL quiz
        document.getElementById('quizStartLink').href = url;
        // Tampilkan modal dengan menghapus class 'hidden' dan menambah class 'flex'
        document.getElementById('quizInfoModal').classList.remove('hidden');
        document.getElementById('quizInfoModal').classList.add('flex');
    }

    /**
     * Fungsi closeQuizModal: Menutup modal konfirmasi retake quiz
     */
    function closeQuizModal() {
        // Sembunyikan modal dengan menambah class 'hidden' dan menghapus class 'flex'
        document.getElementById('quizInfoModal').classList.add('hidden');
        document.getElementById('quizInfoModal').classList.remove('flex');
    }
</script>
@endsection

