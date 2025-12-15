@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap');
    
    :root {
        --primary: #0d9488;
        --primary-dark: #0f766e;
        --primary-light: #14b8a6;
        --accent: #06b6d4;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
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
    
    .quiz-take-page {
        font-family: 'Outfit', sans-serif;
        background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 50%, #e0f2fe 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }
    
    .quiz-header {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(13, 148, 136, 0.1);
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.5rem;
        position: sticky;
        top: 1rem;
        z-index: 100;
        border-top: 4px solid var(--primary);
    }
    
    .quiz-info h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0 0 0.25rem 0;
        letter-spacing: -0.02em;
    }
    
    .quiz-info p {
        color: var(--neutral-600);
        margin: 0;
        font-size: 0.9375rem;
    }
    
    .timer-box {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        padding: 1rem 1.5rem;
        border-radius: 12px;
        border: 2px solid #f59e0b;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
    }
    
    .timer-box i {
        color: #92400e;
        margin-right: 0.5rem;
    }
    
    #timer {
        font-family: 'Space Mono', monospace;
        font-weight: 700;
        font-size: 1.5rem;
        color: #78350f;
    }
    
    .question-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(13, 148, 136, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
    }
    
    .question-card:hover {
        box-shadow: 0 12px 32px rgba(13, 148, 136, 0.15);
        border-color: var(--primary);
    }
    
    .question-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid var(--teal-100);
    }
    
    .question-number {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        color: white;
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
    }
    
    .question-content {
        flex: 1;
    }
    
    .question-text {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--neutral-900);
        margin: 0 0 0.75rem 0;
        line-height: 1.6;
    }
    
    .points-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        background: var(--teal-100);
        color: var(--primary-dark);
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        font-family: 'Space Mono', monospace;
    }
    
    .question-image {
        margin: 1.5rem 0;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.15);
    }
    
    .question-image img {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .options-container {
        margin-top: 1.5rem;
    }
    
    .option-item {
        background: var(--teal-50);
        border: 2px solid var(--teal-100);
        border-radius: 14px;
        padding: 1.25rem;
        margin-bottom: 0.875rem;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .option-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--primary);
        transform: scaleY(0);
        transition: transform 0.2s ease;
    }
    
    .option-item:hover {
        background: white;
        border-color: var(--primary);
        box-shadow: 0 6px 16px rgba(13, 148, 136, 0.15);
        transform: translateX(4px);
    }
    
    .option-item:hover::before {
        transform: scaleY(1);
    }
    
    .option-item input[type="radio"] {
        width: 20px;
        height: 20px;
        margin-right: 1rem;
        cursor: pointer;
        accent-color: var(--primary);
    }
    
    .option-item.selected {
        background: var(--teal-100);
        border-color: var(--primary);
        box-shadow: 0 6px 20px rgba(13, 148, 136, 0.2);
    }
    
    .option-item.selected::before {
        transform: scaleY(1);
    }
    
    .option-item label {
        cursor: pointer;
        margin: 0;
        font-size: 1rem;
        color: var(--neutral-800);
        line-height: 1.6;
        flex: 1;
    }
    
    .option-item.selected label {
        font-weight: 600;
        color: var(--primary-dark);
    }
    
    .essay-textarea {
        width: 100%;
        padding: 1.25rem;
        border: 2px solid var(--teal-100);
        border-radius: 14px;
        font-size: 1rem;
        font-family: 'Outfit', sans-serif;
        color: var(--neutral-800);
        background: var(--teal-50);
        transition: all 0.2s ease;
        resize: vertical;
        min-height: 150px;
    }
    
    .essay-textarea:focus {
        outline: none;
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1);
    }
    
    .submit-section {
        background: white;
        border-radius: 20px;
        padding: 3rem 2rem;
        box-shadow: 0 6px 20px rgba(13, 148, 136, 0.12);
        text-align: center;
        margin-top: 3rem;
        border-top: 4px solid var(--primary);
    }
    
    .submit-info {
        background: #fef3c7;
        border-left: 4px solid var(--warning);
        padding: 1rem 1.5rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        display: inline-block;
    }
    
    .submit-info i {
        color: #92400e;
        margin-right: 0.5rem;
    }
    
    .submit-info p {
        color: #78350f;
        margin: 0;
        font-weight: 500;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
        color: white;
        padding: 1.25rem 3rem;
        font-size: 1.125rem;
        font-weight: 700;
        border: none;
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(16, 185, 129, 0.4);
    }
    
    .btn-submit:active {
        transform: translateY(0);
    }
    
    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .question-card {
        animation: fadeInUp 0.4s cubic-bezier(0.4, 0, 0.2, 1) backwards;
    }
    
    @media (max-width: 768px) {
        .quiz-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .question-header {
            flex-direction: column;
        }
    }
</style>

<div class="quiz-take-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="quiz-header">
                    <div class="quiz-info">
                        <h3>{{ $quiz->judul_quiz }}</h3>
                        <p>Percobaan ke-{{ $attempt->attempt_number }} • {{ $questions->count() }} Pertanyaan</p>
                    </div>
                    @if($quiz->durasi_quiz)
                        <div class="timer-box">
                            <i class="fas fa-clock"></i>
                            <span id="timer">{{ $quiz->durasi_quiz }}:00</span>
                        </div>
                    @endif
                </div>

                <!-- Quiz Form -->
                <form id="quizForm" onsubmit="submitQuiz(event)">
                    @csrf
                    
                    @foreach($questions as $index => $question)
                        <div class="question-card" style="animation-delay: {{ $index * 0.05 }}s">
                            <div class="question-header">
                                <div class="question-number">{{ $index + 1 }}</div>
                                <div class="question-content">
                                    <h4 class="question-text">{{ $question->question_text }}</h4>
                                    <span class="points-badge">
                                        <i class="fas fa-star"></i>
                                        {{ $question->points }} poin
                                    </span>
                                </div>
                            </div>

                            @if($question->image)
                                <div class="question-image">
                                    <img src="{{ asset('storage/' . $question->image) }}" 
                                         alt="Question Image">
                                </div>
                            @endif

                            <div class="options-container">
                                @php
                                    $isEssay = $question->type === 'essay';
                                    $hasOptions = $question->options && $question->options->count() > 0;
                                @endphp

                                @if($isEssay || !$hasOptions)
                                    <textarea name="answers[{{ $question->id }}]" 
                                              class="essay-textarea"
                                              placeholder="Tuliskan jawaban Anda di sini..." 
                                              required></textarea>
                                @else
                                    @foreach($question->options as $option)
                                        <div class="option-item" onclick="selectOption(this)">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="answers[{{ $question->id }}]" 
                                                   id="option_{{ $question->id }}_{{ $option->id }}"
                                                   value="{{ $option->id }}"
                                                   required>
                                            <label for="option_{{ $question->id }}_{{ $option->id }}">
                                                {{ $option->option_text }}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Submit Section -->
                    <div class="submit-section">
                        <div class="submit-info">
                            <i class="fas fa-info-circle"></i>
                            <p>Pastikan semua pertanyaan telah dijawab sebelum submit.</p>
                        </div>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <i class="fas fa-paper-plane"></i>
                            Submit Jawaban
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Select option visual feedback
function selectOption(element) {
    const radio = element.querySelector('input[type="radio"]');
    const container = element.closest('.options-container');
    
    // Remove selected class from all options in this question
    container.querySelectorAll('.option-item').forEach(opt => {
        opt.classList.remove('selected');
    });
    
    // Add selected class to clicked option
    if (radio) {
        radio.checked = true;
        element.classList.add('selected');
    }
}

// Timer functionality
let timeLeft = {{ $quiz->durasi_quiz ?? 0 }} * 60;
let timerInterval;

@if($quiz->durasi_quiz)
    document.addEventListener('DOMContentLoaded', function() {
        startTimer();
    });

    function startTimer() {
        timerInterval = setInterval(function() {
            timeLeft--;
            
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            const timerElement = document.getElementById('timer');
            timerElement.textContent = 
                String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            
            // Change color when time is running out
            const timerBox = timerElement.closest('.timer-box');
            if (timeLeft <= 300 && timeLeft > 60) {
                timerBox.style.background = 'linear-gradient(135deg, #fecaca 0%, #fca5a5 100%)';
                timerBox.style.borderColor = '#dc2626';
            } else if (timeLeft <= 60) {
                timerBox.style.background = 'linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)';
                timerBox.style.borderColor = '#b91c1c';
                timerBox.style.animation = 'pulse 1s ease-in-out infinite';
            }
            
            // Warning when 5 minutes left
            if (timeLeft === 300) {
                alert('⚠️ Perhatian! Waktu tersisa 5 menit lagi.');
            }
            
            // Auto submit when time's up
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                alert('⏰ Waktu habis! Quiz akan disubmit otomatis.');
                document.getElementById('quizForm').dispatchEvent(new Event('submit'));
            }
        }, 1000);
    }
@endif

function submitQuiz(event) {
    event.preventDefault();
    
    // Check if all questions are answered
    const totalQuestions = {{ $questions->count() }};
    const answeredRadios = document.querySelectorAll('input[type="radio"]:checked').length;
    const answeredEssays = Array.from(document.querySelectorAll('textarea[name^="answers["]'))
        .filter(t => t.value.trim() !== '').length;
    const answeredQuestions = answeredRadios + answeredEssays;
    
    if (answeredQuestions < totalQuestions) {
        if (!confirm(`⚠️ Anda baru menjawab ${answeredQuestions} dari ${totalQuestions} soal. Yakin ingin submit?`)) {
            return;
        }
    }
    
    if (!confirm('Apakah Anda yakin ingin submit jawaban? Jawaban tidak dapat diubah setelah submit.')) {
        return;
    }
    
    // Disable submit button
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan Jawaban...';
    
    // Clear timer
    if (timerInterval) {
        clearInterval(timerInterval);
    }
    
    // Get form data
    const formData = new FormData(event.target);
    const answers = {};
    
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('answers[')) {
            const questionId = key.match(/\d+/)[0];
            answers[questionId] = value;
        }
    }
    
    // Submit via AJAX
    fetch('{{ route("courses.final-quiz.submit", [$kursusId, $attempt->id]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({ answers: answers })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        } else {
            alert(data.error || 'Terjadi kesalahan');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Jawaban';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan jawaban');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Jawaban';
    });
}

// Add pulse animation for timer
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
`;
document.head.appendChild(style);

// Prevent accidental page close
window.addEventListener('beforeunload', function(e) {
    e.preventDefault();
    e.returnValue = '';
});
</script>
@endsection