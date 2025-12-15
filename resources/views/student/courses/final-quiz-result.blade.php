@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap');
    
    :root {
        --primary: #0d9488;
        --primary-dark: #0f766e;
        --primary-light: #14b8a6;
        --accent: #06b6d4;
        --success: #10b981;
        --success-dark: #059669;
        --danger: #ef4444;
        --danger-dark: #dc2626;
        --warning: #f59e0b;
        --info: #06b6d4;
        --neutral-50: #fafafa;
        --neutral-100: #f5f5f5;
        --neutral-200: #e5e5e5;
        --neutral-600: #525252;
        --neutral-700: #404040;
        --neutral-800: #262626;
        --neutral-900: #171717;
        --teal-50: #f0fdfa;
        --teal-100: #ccfbf1;
    }
    
    .results-page {
        font-family: 'Outfit', sans-serif;
        background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 50%, #e0f2fe 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }
    
    .result-hero {
        background: white;
        border-radius: 24px;
        padding: 4rem 3rem;
        text-align: center;
        box-shadow: 0 8px 32px rgba(13, 148, 136, 0.12);
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        border-top: 6px solid var(--primary);
    }
    
    .result-hero.passed {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 50%, #ccfbf1 100%);
    }
    
    .result-hero.failed {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 50%, #fed7aa 100%);
    }
    
    .result-icon {
        font-size: 5rem;
        margin-bottom: 1.5rem;
        animation: scaleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .result-icon.success {
        color: var(--success);
    }
    
    .result-icon.danger {
        color: var(--danger);
    }
    
    .result-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        letter-spacing: -0.02em;
    }
    
    .result-title.success {
        color: var(--success-dark);
    }
    
    .result-title.danger {
        color: var(--danger-dark);
    }
    
    .score-display {
        font-size: 4.5rem;
        font-weight: 800;
        font-family: 'Space Mono', monospace;
        margin: 1.5rem 0;
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: fadeInUp 0.6s ease 0.2s backwards;
    }
    
    .progress-container {
        max-width: 600px;
        margin: 2rem auto;
        animation: fadeInUp 0.6s ease 0.3s backwards;
    }
    
    .progress-bar-custom {
        height: 18px;
        border-radius: 100px;
        background: var(--neutral-200);
        overflow: hidden;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .progress-fill {
        height: 100%;
        border-radius: 100px;
        transition: width 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .progress-fill.success {
        background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 50%, var(--success) 100%);
    }
    
    .progress-fill.danger {
        background: linear-gradient(90deg, var(--danger) 0%, #f87171 100%);
    }
    
    .progress-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255,255,255,0.4),
            transparent
        );
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    .result-meta {
        color: var(--neutral-600);
        font-size: 1rem;
        margin-top: 1.5rem;
        animation: fadeInUp 0.6s ease 0.4s backwards;
    }
    
    .status-alert {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        margin-top: 2rem;
        animation: fadeInUp 0.6s ease 0.5s backwards;
    }
    
    .status-alert.success {
        background: white;
        color: var(--success-dark);
        border: 2px solid var(--success);
    }
    
    .status-alert.danger {
        background: white;
        color: var(--danger-dark);
        border: 2px solid var(--danger);
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(13, 148, 136, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: fadeInUp 0.6s ease backwards;
        border: 2px solid var(--teal-100);
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(13, 148, 136, 0.18);
        border-color: var(--primary);
    }
    
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
    
    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .stat-icon.primary { color: var(--primary); }
    .stat-icon.success { color: var(--success); }
    .stat-icon.danger { color: var(--danger); }
    .stat-icon.info { color: var(--accent); }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        font-family: 'Space Mono', monospace;
        color: var(--neutral-900);
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        color: var(--neutral-600);
        font-size: 0.9375rem;
        font-weight: 500;
    }
    
    .review-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(13, 148, 136, 0.08);
        margin-bottom: 2rem;
        overflow: hidden;
        animation: fadeInUp 0.6s ease 0.5s backwards;
        border: 2px solid var(--teal-100);
    }
    
    .review-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 50%, var(--accent) 100%);
        color: white;
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .review-header h5 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
    }
    
    .review-body {
        padding: 2rem;
    }
    
    .answer-item {
        padding: 2rem;
        border-bottom: 2px solid var(--teal-100);
        transition: background 0.2s ease;
    }
    
    .answer-item:hover {
        background: var(--teal-50);
    }
    
    .answer-item:last-child {
        border-bottom: none;
    }
    
    .answer-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .answer-number {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .answer-number.correct {
        background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .answer-number.incorrect {
        background: linear-gradient(135deg, var(--danger) 0%, var(--danger-dark) 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    
    .question-text {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--neutral-900);
        margin-bottom: 0.5rem;
        line-height: 1.6;
    }
    
    .question-image {
        margin: 1.5rem 0;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.15);
        max-width: 100%;
    }
    
    .question-image img {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .option-review {
        padding: 1rem 1.25rem;
        margin-bottom: 0.875rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.2s ease;
    }
    
    .option-review.correct {
        background: #d1fae5;
        border: 2px solid var(--success);
    }
    
    .option-review.incorrect {
        background: #fee2e2;
        border: 2px solid var(--danger);
    }
    
    .option-review.neutral {
        background: var(--teal-50);
        border: 2px solid var(--teal-100);
    }
    
    .option-icon {
        font-size: 1.25rem;
    }
    
    .option-icon.correct { color: var(--success-dark); }
    .option-icon.incorrect { color: var(--danger-dark); }
    .option-icon.neutral { color: var(--neutral-600); }
    
    .option-text {
        flex: 1;
        color: var(--neutral-800);
        font-weight: 500;
    }
    
    .option-review.correct .option-text {
        font-weight: 700;
        color: var(--success-dark);
    }
    
    .option-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-size: 0.8125rem;
        font-weight: 700;
    }
    
    .option-badge.your-answer {
        background: var(--danger);
        color: white;
    }
    
    .option-badge.correct-answer {
        background: var(--success);
        color: white;
    }
    
    .essay-answer {
        padding: 1.25rem;
        background: var(--teal-50);
        border: 2px solid var(--teal-100);
        border-radius: 12px;
        margin-top: 1rem;
    }
    
    .essay-label {
        font-weight: 700;
        color: var(--neutral-800);
        margin-bottom: 0.75rem;
        display: block;
    }
    
    .essay-text {
        color: var(--neutral-700);
        line-height: 1.7;
    }
    
    .explanation-box {
        background: var(--teal-100);
        border-left: 4px solid var(--primary);
        padding: 1.25rem;
        border-radius: 12px;
        margin-top: 1.5rem;
    }
    
    .explanation-box strong {
        color: var(--primary-dark);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    
    .action-buttons {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(13, 148, 136, 0.08);
        animation: fadeInUp 0.6s ease 0.6s backwards;
        border: 2px solid var(--teal-100);
    }
    
    .btn-action {
        padding: 1rem 2rem;
        font-weight: 700;
        border-radius: 14px;
        border: none;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin: 0.5rem;
    }
    
    .btn-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.18);
    }
    
    .btn-action.primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
    }
    
    .btn-action.outline {
        background: white;
        color: var(--primary);
        border: 2px solid var(--primary);
    }
    
    .btn-action.warning {
        background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    
    @keyframes scaleIn {
        0% {
            transform: scale(0);
            opacity: 0;
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @media (max-width: 768px) {
        .result-hero {
            padding: 3rem 2rem;
        }
        
        .result-title {
            font-size: 2rem;
        }
        
        .score-display {
            font-size: 3.5rem;
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .btn-action {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="results-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">
                <!-- Result Hero -->
                <div class="result-hero {{ $attempt->is_passed ? 'passed' : 'failed' }}">
                    @if($attempt->is_passed)
                        <div class="result-icon success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h1 class="result-title success">Selamat! Anda Lulus!</h1>
                    @else
                        <div class="result-icon danger">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <h1 class="result-title danger">Belum Berhasil</h1>
                    @endif
                    
                    <div class="score-display">{{ number_format($attempt->score, 2) }}%</div>
                    
                    <div class="progress-container">
                        <div class="progress-bar-custom">
                            <div class="progress-fill {{ $attempt->is_passed ? 'success' : 'danger' }}"
                                 data-progress="{{ round($attempt->score) }}"
                                 style="width: 0%">
                            </div>
                        </div>
                    </div>
                    
                    <p class="result-meta">
                        Percobaan ke-{{ $attempt->attempt_number }} • 
                        {{ $attempt->completed_at->format('d M Y, H:i') }}
                    </p>

                    @if($attempt->is_passed)
                        <div class="status-alert success">
                            <i class="fas fa-trophy"></i>
                            <span>Nilai Anda melebihi batas minimum <strong>{{ $kursus->min_passing_score }}%</strong></span>
                        </div>
                    @else
                        <div class="status-alert danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Nilai minimum: <strong>{{ $kursus->min_passing_score }}%</strong></span>
                        </div>
                    @endif
                </div>

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fas fa-list-check"></i>
                        </div>
                        <div class="stat-value">{{ $attempt->jawabanPeserta->count() }}</div>
                        <div class="stat-label">Total Soal</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="stat-value">{{ $attempt->jawabanPeserta->where('nilai_tercapai', 1)->count() }}</div>
                        <div class="stat-label">Jawaban Benar</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon danger">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="stat-value">{{ $attempt->jawabanPeserta->where('nilai_tercapai', 0)->count() }}</div>
                        <div class="stat-label">Jawaban Salah</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon info">
                            <i class="fas fa-percent"></i>
                        </div>
                        <div class="stat-value">{{ number_format($attempt->score, 1) }}%</div>
                        <div class="stat-label">Persentase</div>
                    </div>
                </div>

                <!-- Detailed Answers -->
                @if($attempt->is_passed)
                    <div class="review-card">
                        <div class="review-header">
                            <i class="fas fa-clipboard-list"></i>
                            <h5>Review Jawaban</h5>
                        </div>
                        <div class="review-body">
                            @foreach($attempt->jawabanPeserta as $index => $jawaban)
                                @php
                                    $question = $jawaban->question;
                                    $options = $question?->options ?? collect();
                                    $correctOption = $options->where('is_correct', true)->first();
                                    $selectedOption = $options->firstWhere('id', $jawaban->selected_option_id);
                                    $isEssay = ($question?->type === 'essay') || $options->count() === 0;
                                    $isCorrect = !$isEssay && $selectedOption && $selectedOption->is_correct;
                                @endphp

                                <div class="answer-item">
                                    <div class="answer-header">
                                        <div class="answer-number {{ $isCorrect ? 'correct' : 'incorrect' }}">
                                            {{ $index + 1 }}
                                        </div>
                                        <div style="flex: 1;">
                                            <div class="question-text">{{ $question?->question_text ?? 'Pertanyaan' }}</div>
                                            @if($question?->image)
                                                <div class="question-image">
                                                    <img src="{{ asset('storage/' . $question->image) }}" alt="Question Image">
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if($isEssay)
                                        <div class="essay-answer">
                                            <strong class="essay-label">Jawaban Anda:</strong>
                                            <div class="essay-text">{{ $jawaban->answer_text ?: '-' }}</div>
                                        </div>
                                    @else
                                        <div style="margin-left: 68px;">
                                            @foreach($options as $option)
                                                @php
                                                    $isSelected = $selectedOption && $selectedOption->id == $option->id;
                                                    $isCorrectOption = $option->is_correct;
                                                    $optionClass = $isCorrectOption ? 'correct' : ($isSelected ? 'incorrect' : 'neutral');
                                                @endphp

                                                <div class="option-review {{ $optionClass }}">
                                                    @if($isCorrectOption)
                                                        <i class="fas fa-check-circle option-icon correct"></i>
                                                    @elseif($isSelected)
                                                        <i class="fas fa-times-circle option-icon incorrect"></i>
                                                    @else
                                                        <i class="far fa-circle option-icon neutral"></i>
                                                    @endif
                                                    
                                                    <span class="option-text">{{ $option->option_text }}</span>

                                                    @if($isSelected && !$isCorrectOption)
                                                        <span class="option-badge your-answer">Jawaban Anda</span>
                                                    @endif
                                                    @if($isCorrectOption)
                                                        <span class="option-badge correct-answer">Jawaban Benar</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($question?->penjelasan)
                                        <div class="explanation-box" style="margin-left: 68px;">
                                            <strong>
                                                <i class="fas fa-lightbulb"></i>
                                                Penjelasan:
                                            </strong>
                                            <div>{{ $question->penjelasan }}</div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning d-flex align-items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        <div>Review jawaban akan muncul setelah Anda mencapai nilai minimum. Silakan coba lagi.</div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="{{ route('courses.final-quiz.show', $kursus->id) }}" class="btn-action primary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Final Quiz
                    </a>

                    @if($attempt->is_passed)
                        <a href="{{ route('student.course.learn', $kursus->id) }}" class="btn-action outline">
                            <i class="fas fa-book-open"></i>
                            Review Materi Terkait
                        </a>
                    @endif
                    
                    @if(!$attempt->is_passed)
                        @php
                            $totalAttempts = \App\Models\QuizAttempt::where('user_id', auth()->id())
                                ->where('quiz_id', $quiz->id)
                                ->where('kursus_id', $kursus->id)
                                ->count();
                            $canRetake = $totalAttempts < $kursus->max_quiz_attempts;
                        @endphp
                        
                        @if($canRetake)
                            <a href="{{ route('courses.final-quiz.show', $kursus->id) }}" class="btn-action warning">
                                <i class="fas fa-redo"></i>
                                Coba Lagi
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Animate progress bar
    const progressBar = document.querySelector('.progress-fill');
    if (progressBar) {
        const targetWidth = progressBar.dataset.progress;
        setTimeout(() => {
            progressBar.style.width = targetWidth + '%';
        }, 300);
    }
});
</script>
@endsection
