@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-4xl">
    <!-- Result Header -->
    <div class="bg-white rounded-xl shadow-sm p-8 mb-6">
        <div class="text-center mb-6">
            @if($submission->isGraded())
                @if($submission->isPassed())
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-green-600 text-4xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-green-600 mb-2">Selamat! Anda Lulus</h2>
                @else
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-times text-red-600 text-4xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-red-600 mb-2">Belum Lulus</h2>
                @endif
                
                <div class="text-6xl font-bold text-gray-800 mb-2">{{ number_format($submission->percentage, 1) }}%</div>
                <p class="text-gray-600">Skor Anda: {{ number_format($submission->score, 1) }} / {{ $assignment->total_points }}</p>
            @else
            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clock text-yellow-600 text-4xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-yellow-600 mb-2">Menunggu Penilaian</h2>
            <p class="text-gray-600">Jawaban Anda sedang dinilai oleh instruktur</p>
            @endif
        </div>

        <div class="grid grid-cols-3 gap-4 pt-6 border-t">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-800">{{ $assignment->questions->count() }}</div>
                <div class="text-sm text-gray-600">Total Soal</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-800">{{ $submission->submitted_at->format('d M Y') }}</div>
                <div class="text-sm text-gray-600">Tanggal Submit</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-800">Percobaan #{{ $submission->attempt_number }}</div>
                <div class="text-sm text-gray-600">Attempt</div>
            </div>
        </div>
    </div>

    @if($submission->feedback)
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-6">
        <h3 class="font-semibold text-blue-900 mb-2">
            <i class="fas fa-comment mr-2"></i>Feedback dari Instruktur
        </h3>
        <p class="text-blue-800">{{ $submission->feedback }}</p>
    </div>
    @endif

    <!-- Questions Review -->
    @if($assignment->show_results_immediately || $submission->isGraded())
    <div class="space-y-6">
        @foreach($questions as $index => $question)
        @php
            $userAnswer = $submission->getAnswerForQuestion($question->id);
            $isCorrect = false;
            
            if ($question->type === 'multiple_choice' || $question->type === 'true_false') {
                $correctOption = $question->options->where('is_correct', true)->first();
                $isCorrect = $correctOption && $userAnswer == $correctOption->id;
            } elseif ($question->type === 'short_answer') {
                $isCorrect = $userAnswer && $question->correct_answer && 
                            strtolower(trim($userAnswer)) === strtolower(trim($question->correct_answer));
            }
        @endphp
        
        <div class="bg-white rounded-xl shadow-sm p-6 @if($question->type !== 'essay' && $isCorrect) border-l-4 border-green-500 @elseif($question->type !== 'essay') border-l-4 border-red-500 @endif">
            <!-- Question Header -->
            <div class="flex justify-between items-start mb-4">
                <div>
                    <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full font-medium">
                        Soal #{{ $index + 1 }}
                    </span>
                    @if($question->type !== 'essay')
                        @if($isCorrect)
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full font-medium ml-2">
                            <i class="fas fa-check"></i> Benar
                        </span>
                        @else
                        <span class="inline-block px-3 py-1 bg-red-100 text-red-700 text-sm rounded-full font-medium ml-2">
                            <i class="fas fa-times"></i> Salah
                        </span>
                        @endif
                    @endif
                </div>
                <span class="text-sm font-medium text-gray-600">{{ $question->pivot->points ?? $question->points }} poin</span>
            </div>

            <!-- Question Text -->
            <p class="text-lg text-gray-800 font-medium mb-4">{{ $question->question_text }}</p>

            <!-- User Answer -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Jawaban Anda:</h4>
                
                @if($question->type === 'multiple_choice' || $question->type === 'true_false')
                    @php $selectedOption = $question->options->firstWhere('id', $userAnswer); @endphp
                    <p class="text-gray-800">{{ $selectedOption ? $selectedOption->option_text : 'Tidak dijawab' }}</p>
                
                @elseif($question->type === 'short_answer')
                    <p class="text-gray-800">{{ $userAnswer ?? 'Tidak dijawab' }}</p>
                
                @elseif($question->type === 'essay')
                    <p class="text-gray-800 whitespace-pre-wrap">{{ $userAnswer ?? 'Tidak dijawab' }}</p>
                @endif
            </div>

            <!-- Correct Answer -->
            @if(($question->type === 'multiple_choice' || $question->type === 'true_false') && !$isCorrect)
            <div class="bg-green-50 rounded-lg p-4 mb-4">
                <h4 class="text-sm font-semibold text-green-700 mb-2">Jawaban yang Benar:</h4>
                @php $correctOption = $question->options->where('is_correct', true)->first(); @endphp
                <p class="text-green-800">{{ $correctOption->option_text }}</p>
            </div>
            @endif

            @if($question->type === 'short_answer' && !$isCorrect)
            <div class="bg-green-50 rounded-lg p-4 mb-4">
                <h4 class="text-sm font-semibold text-green-700 mb-2">Jawaban yang Benar:</h4>
                <p class="text-green-800">{{ $question->correct_answer }}</p>
            </div>
            @endif

            <!-- Explanation -->
            @if($question->explanation)
            <div class="bg-blue-50 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-blue-700 mb-2">
                    <i class="fas fa-lightbulb mr-1"></i>Penjelasan:
                </h4>
                <p class="text-blue-800">{{ $question->explanation }}</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
        <div class="flex justify-center gap-4">
            <a href="{{ route('student.assignments.show', $assignment) }}" 
               class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Detail
            </a>
            
            <a href="{{ route('student.assignments.index') }}" 
               class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                <i class="fas fa-list mr-2"></i>Lihat Semua Assignment
            </a>
        </div>
    </div>
</div>
@endsection
