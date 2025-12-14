@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Result Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center py-5">
                    @if($attempt->is_passed)
                        <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                        <h2 class="text-success mb-3">Selamat! Anda Lulus!</h2>
                    @else
                        <i class="fas fa-times-circle fa-5x text-danger mb-3"></i>
                        <h2 class="text-danger mb-3">Belum Berhasil</h2>
                    @endif
                    
                    <h1 class="display-4 mb-3">{{ number_format($attempt->score, 2) }}%</h1>
                    
                    <p class="text-muted mb-4">
                        Percobaan ke-{{ $attempt->attempt_number }} | 
                        {{ $attempt->completed_at->format('d M Y, H:i') }}
                    </p>

                    @if($attempt->is_passed)
                        <div class="alert alert-success d-inline-block">
                            <i class="fas fa-trophy"></i>
                            Nilai Anda melebihi batas minimum <strong>{{ $kursus->min_passing_score }}%</strong>
                        </div>
                    @else
                        <div class="alert alert-danger d-inline-block">
                            <i class="fas fa-exclamation-triangle"></i>
                            Nilai minimum: <strong>{{ $kursus->min_passing_score }}%</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-list-check fa-2x text-primary mb-2"></i>
                            <h4>{{ $attempt->jawabanPeserta->count() }}</h4>
                            <p class="text-muted mb-0">Total Soal</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-check fa-2x text-success mb-2"></i>
                            <h4>{{ $attempt->jawabanPeserta->where('nilai_tercapai', 1)->count() }}</h4>
                            <p class="text-muted mb-0">Jawaban Benar</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-times fa-2x text-danger mb-2"></i>
                            <h4>{{ $attempt->jawabanPeserta->where('nilai_tercapai', 0)->count() }}</h4>
                            <p class="text-muted mb-0">Jawaban Salah</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-percent fa-2x text-info mb-2"></i>
                            <h4>{{ number_format($attempt->score, 1) }}%</h4>
                            <p class="text-muted mb-0">Persentase</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Answers -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Review Jawaban</h5>
                </div>
                <div class="card-body">
                    @foreach($attempt->jawabanPeserta as $index => $jawaban)
                        @php
                            $question = $jawaban->bankSoal;
                            $correctOption = $question->opsiJawaban->where('is_correct', true)->first();
                            $selectedOption = $jawaban->opsiJawaban;
                            $isCorrect = $jawaban->nilai_tercapai == 1;
                        @endphp

                        <div class="mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <!-- Question -->
                            <div class="d-flex align-items-start mb-3">
                                <span class="badge {{ $isCorrect ? 'bg-success' : 'bg-danger' }} me-2" style="font-size: 1rem;">
                                    {{ $index + 1 }}
                                </span>
                                <div class="flex-grow-1">
                                    <h6 class="mb-2">{{ $question->pertanyaan }}</h6>
                                    @if($question->gambar)
                                        <img src="{{ asset('storage/' . $question->gambar) }}" 
                                             alt="Question Image" 
                                             class="img-fluid rounded mb-2"
                                             style="max-height: 200px;">
                                    @endif
                                </div>
                            </div>

                            <!-- Options -->
                            <div class="ms-4">
                                @foreach($question->opsiJawaban as $option)
                                    @php
                                        $isSelected = $selectedOption && $selectedOption->id == $option->id;
                                        $isCorrectOption = $option->is_correct;
                                    @endphp

                                    <div class="p-2 mb-2 rounded
                                        {{ $isCorrectOption ? 'bg-success bg-opacity-10 border border-success' : '' }}
                                        {{ $isSelected && !$isCorrectOption ? 'bg-danger bg-opacity-10 border border-danger' : '' }}
                                        {{ !$isSelected && !$isCorrectOption ? 'bg-light' : '' }}">
                                        
                                        <div class="d-flex align-items-center">
                                            @if($isCorrectOption)
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                            @elseif($isSelected)
                                                <i class="fas fa-times-circle text-danger me-2"></i>
                                            @else
                                                <i class="far fa-circle text-muted me-2"></i>
                                            @endif
                                            
                                            <span class="{{ $isCorrectOption ? 'fw-bold' : '' }}">
                                                {{ $option->opsi_jawaban }}
                                            </span>

                                            @if($isSelected && !$isCorrectOption)
                                                <span class="badge bg-danger ms-2">Jawaban Anda</span>
                                            @endif
                                            @if($isCorrectOption)
                                                <span class="badge bg-success ms-2">Jawaban Benar</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Explanation (if available) -->
                            @if($question->penjelasan)
                                <div class="alert alert-info mt-3 ms-4">
                                    <strong><i class="fas fa-lightbulb"></i> Penjelasan:</strong><br>
                                    {{ $question->penjelasan }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow-sm">
                <div class="card-body text-center py-4">
                    <a href="{{ route('student.courses.final-quiz.show', $kursus->id) }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-arrow-left"></i> Kembali ke Final Quiz
                    </a>
                    
                    @if(!$attempt->is_passed)
                        @php
                            $totalAttempts = \App\Models\QuizAttempt::where('user_id', auth()->id())
                                ->where('quiz_id', $quiz->id)
                                ->where('kursus_id', $kursus->id)
                                ->count();
                            $canRetake = $totalAttempts < $kursus->max_quiz_attempts;
                        @endphp
                        
                        @if($canRetake)
                            <a href="{{ route('student.courses.final-quiz.show', $kursus->id) }}" class="btn btn-warning btn-lg">
                                <i class="fas fa-redo"></i> Coba Lagi
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-opacity-10 {
    opacity: 0.1;
}
</style>
@endsection
