@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#f4f2f0] py-8">
    <div class="max-w-4xl mx-auto px-6">
        <!-- Header Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Final Quiz</h1>
            <p class="text-gray-600 mb-4">{{ $kursus->nama_kursus }}</p>
            <a href="{{ route('courses.final-quiz.show', $kursus->id) }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition font-semibold">
                Kembali
            </a>
        </div>

        <!-- Result Card - Teal Gradient -->
        <div class="bg-gradient-to-r from-teal-600 to-teal-500 rounded-2xl shadow-lg p-8 mb-6 text-white">
            <h2 class="text-2xl font-bold mb-2">Hasil Quiz</h2>
            <p class="text-teal-100 mb-6">Percobaan ke-{{ $attempt->attempt_number }} • {{ $attempt->completed_at->format('d M Y, H:i') }}</p>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Score -->
                <div class="bg-white/20 backdrop-blur rounded-xl p-5 text-center">
                    <p class="text-teal-100 text-xs font-semibold uppercase tracking-wider mb-2">Nilai Anda</p>
                    <p class="text-3xl font-bold font-mono">{{ number_format($attempt->score, 0) }}%</p>
                </div>
                
                <!-- Correct Answers -->
                <div class="bg-white/20 backdrop-blur rounded-xl p-5 text-center">
                    <p class="text-teal-100 text-xs font-semibold uppercase tracking-wider mb-2">Jawaban Benar</p>
                    <p class="text-3xl font-bold font-mono">{{ $attempt->jawabanPeserta->where('nilai_tercapai', 1)->count() }}/{{ $attempt->jawabanPeserta->count() }}</p>
                </div>
                
                <!-- Minimum Score -->
                <div class="bg-white/20 backdrop-blur rounded-xl p-5 text-center">
                    <p class="text-teal-100 text-xs font-semibold uppercase tracking-wider mb-2">Nilai Minimum</p>
                    <p class="text-3xl font-bold font-mono">{{ $kursus->min_passing_score }}%</p>
                </div>
                
                <!-- Status -->
                <div class="bg-white/20 backdrop-blur rounded-xl p-5 text-center">
                    <p class="text-teal-100 text-xs font-semibold uppercase tracking-wider mb-2">Status</p>
                    @if($attempt->is_passed)
                        <span class="inline-block px-4 py-1.5 bg-white text-teal-700 rounded-lg text-lg font-bold">Lulus</span>
                    @else
                        <span class="inline-block px-4 py-1.5 bg-red-500 text-white rounded-lg text-lg font-bold">Tidak Lulus</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Review Answers Card -->
        @php
            $totalAttempts = \App\Models\QuizAttempt::where('user_id', auth()->id())
                ->where('quiz_id', $quiz->id)
                ->where('kursus_id', $kursus->id)
                ->count();
            $canRetake = $totalAttempts < $kursus->max_quiz_attempts;
        @endphp

        @if($attempt->is_passed)
            {{-- Review tersembunyi, muncul setelah klik tombol Review --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6 hidden" id="review-section">
                <div class="bg-gradient-to-r from-teal-600 to-teal-500 px-8 py-4">
                    <h3 class="text-xl font-bold text-white">
                        <i class="fas fa-clipboard-list mr-2"></i>Review Jawaban
                    </h3>
                </div>
                <div class="p-6">
                    @foreach($attempt->jawabanPeserta as $index => $jawaban)
                        @php
                            $question = $jawaban->question;
                            $options = $question?->options ?? collect();
                            $correctOption = $options->where('is_correct', true)->first();
                            $selectedOption = $options->firstWhere('id', $jawaban->selected_option_id);
                            $isEssay = ($question?->type === 'essay') || $options->count() === 0;
                            $isCorrect = !$isEssay && $selectedOption && $selectedOption->is_correct;
                        @endphp

                        <div class="py-5 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-white flex-shrink-0 {{ $isCorrect ? 'bg-emerald-500' : 'bg-red-500' }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-900 font-medium mb-3">{{ $question?->question_text ?? 'Pertanyaan' }}</p>
                                    
                                    @if($question?->image)
                                        <img src="{{ asset('storage/' . $question->image) }}" alt="Question Image" class="max-w-md rounded-lg mb-3">
                                    @endif

                                    @if($isEssay)
                                        <div class="bg-gray-100 rounded-lg p-4">
                                            <p class="text-sm text-gray-600 font-semibold mb-1">Jawaban Anda:</p>
                                            <p class="text-gray-800">{{ $jawaban->answer_text ?: '-' }}</p>
                                        </div>
                                    @else
                                        <div class="space-y-2">
                                            @foreach($options as $option)
                                                @php
                                                    $isSelected = $selectedOption && $selectedOption->id == $option->id;
                                                    $isCorrectOption = $option->is_correct;
                                                @endphp
                                                <div class="flex items-center gap-3 p-3 rounded-lg {{ $isCorrectOption ? 'bg-emerald-100 border-2 border-emerald-500' : ($isSelected ? 'bg-red-100 border-2 border-red-500' : 'bg-gray-50') }}">
                                                    @if($isCorrectOption)
                                                        <i class="fas fa-check-circle text-emerald-600"></i>
                                                    @elseif($isSelected)
                                                        <i class="fas fa-times-circle text-red-600"></i>
                                                    @else
                                                        <i class="far fa-circle text-gray-400"></i>
                                                    @endif
                                                    <span class="{{ $isCorrectOption ? 'text-emerald-700 font-semibold' : ($isSelected ? 'text-red-700' : 'text-gray-700') }}">
                                                        {{ $option->option_text }}
                                                    </span>
                                                    @if($isCorrectOption)
                                                        <span class="ml-auto px-2 py-0.5 bg-emerald-500 text-white text-xs font-bold rounded">Benar</span>
                                                    @elseif($isSelected && !$isCorrectOption)
                                                        <span class="ml-auto px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded">Jawaban Anda</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            {{-- Jika TIDAK LULUS --}}
            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-2xl p-6 mb-6">
                <div class="flex items-center gap-3 text-yellow-800">
                    <i class="fas fa-info-circle text-xl"></i>
                    <p class="font-medium">Review jawaban akan tersedia setelah Anda mencapai nilai minimum dan memutuskan untuk tidak mengerjakan ulang.</p>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('student.course.learn', $kursus->id) }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition font-semibold">
                    <i class="fas fa-book-open"></i>
                    Kembali ke Kursus
                </a>

                @php
                    $canReview = $attempt->is_passed || (!$attempt->is_passed && ($quiz->allow_review ?? true));
                @endphp

                @if($canReview)
                    <button type="button"
                            onclick="showReview(); document.getElementById('review-section')?.scrollIntoView({behavior:'smooth'});"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-amber-500 text-white rounded-xl hover:bg-amber-600 transition font-semibold">
                        <i class="fas fa-clipboard-list"></i>
                        Lihat Review Jawaban
                    </button>
                @else
                    <span class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 text-gray-500 rounded-xl cursor-not-allowed">
                        <i class="fas fa-ban"></i>
                        Review belum tersedia
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
