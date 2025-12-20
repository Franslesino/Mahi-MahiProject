@extends('layouts.app')

@section('title', 'Hasil Quiz - ' . ($material->judul ?? $material->title))

@section('content')
<div class="min-h-screen bg-[#f4f2f0] py-8">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('student.course.learn', $course) }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900 transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Kursus</span>
            </a>
            <div class="flex items-center gap-4 text-gray-700">
                <span class="font-semibold">Skor:</span>
                <div class="flex items-center gap-2">
                    <span class="text-xl font-bold">{{ $score !== null ? $score . '%' : 'Pending' }}</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if(!empty($canRetake))
                    <button type="button"
                       onclick="openQuizModal('{{ route('courses.materials.quiz', [$course, $material->id]) }}?retake=1')"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-amber-100 text-amber-800 rounded-lg text-sm font-semibold hover:bg-amber-200 transition">
                        <i class="fas fa-redo"></i>
                        Coba Lagi
                    </button>
                @endif
                <a href="{{ route('student.course.learn', $course) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-700 text-white rounded-lg text-sm font-semibold hover:bg-emerald-800 transition">
                    <span>Lanjut Materi Berikutnya</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-6">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Quiz {{ $material->judul ?? $material->title }}</h2>
                <p class="text-sm text-gray-600">Ringkasan jawaban Anda beserta koreksi</p>
            </div>

            <div class="divide-y divide-gray-200">
                @foreach($results as $idx => $res)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-start sm:gap-4">
                        <div class="w-12 h-12 rounded-full {{ $res['is_correct'] === true ? 'bg-green-100' : ($res['is_correct'] === false ? 'bg-red-100' : 'bg-gray-100') }} flex items-center justify-center font-semibold {{ $res['is_correct'] === true ? 'text-green-800' : ($res['is_correct'] === false ? 'text-red-800' : 'text-gray-800') }} mb-3 sm:mb-0">
                            {{ $idx + 1 }}
                        </div>
                        <div class="flex-1 min-w-0 space-y-2">
                            <p class="text-gray-900 font-medium">{{ $res['text'] }}</p>
                            @if($res['user_answer'])
                                <p class="text-sm {{ $res['is_correct'] === true ? 'text-green-600' : 'text-red-600' }}">
                                    <span class="font-medium">Jawaban Anda:</span> {{ $res['user_answer'] }}
                                    @if($res['is_correct'] === true)
                                        <i class="fas fa-check-circle ml-1"></i>
                                    @else
                                        <i class="fas fa-times-circle ml-1"></i>
                                    @endif
                                </p>
                            @else
                                <p class="text-sm text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>Anda belum menjawab soal ini.
                                </p>
                            @endif
                            @if($res['is_correct'] === false && $res['correct_answer'])
                                <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <p class="text-sm text-green-800">
                                        <i class="fas fa-lightbulb mr-1 text-green-600"></i>
                                        <span class="font-semibold">Jawaban yang benar:</span> {{ $res['correct_answer'] }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        <div class="mt-3 sm:mt-0">
                            @if($res['is_correct'] === true)
                                <span class="text-green-600 text-2xl"><i class="fas fa-check-circle"></i></span>
                            @elseif($res['is_correct'] === false)
                                <span class="text-red-500 text-2xl"><i class="fas fa-times-circle"></i></span>
                            @else
                                <span class="text-gray-400 text-2xl"><i class="fas fa-question-circle"></i></span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal Quiz Info -->
<div id="quizInfoModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-redo text-amber-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Coba Lagi Quiz</h3>
            <p class="text-gray-600 text-sm">Anda akan mengerjakan ulang quiz ini dari awal</p>
        </div>
        
        <div class="bg-gray-50 rounded-xl p-4 mb-6 space-y-3">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600"><i class="fas fa-list mr-2 text-purple-500"></i>Jumlah Soal</span>
                <span class="font-semibold text-gray-900">{{ count($results) }} soal</span>
            </div>
        </div>
        
        <div class="flex gap-3">
            <button type="button" onclick="closeQuizModal()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                Batal
            </button>
            <a id="quizStartLink" href="#" class="flex-1 px-4 py-3 bg-amber-600 text-white rounded-xl font-semibold hover:bg-amber-700 transition text-center">
                Mulai Quiz
            </a>
        </div>
    </div>
</div>

<script>
function openQuizModal(url) {
    document.getElementById('quizStartLink').href = url;
    document.getElementById('quizInfoModal').classList.remove('hidden');
    document.getElementById('quizInfoModal').classList.add('flex');
}

function closeQuizModal() {
    document.getElementById('quizInfoModal').classList.add('hidden');
    document.getElementById('quizInfoModal').classList.remove('flex');
}
</script>
@endsection

