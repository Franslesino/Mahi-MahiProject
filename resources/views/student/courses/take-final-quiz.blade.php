@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ $quiz->judul_quiz }}</h3>
                            <p class="text-muted mb-0">Percobaan ke-{{ $attempt->attempt_number }}</p>
                        </div>
                        <div class="text-end">
                            @if($quiz->durasi_quiz)
                                <div class="alert alert-warning mb-0 py-2 px-3">
                                    <i class="fas fa-clock"></i>
                                    <strong id="timer">{{ $quiz->durasi_quiz }}:00</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Form -->
            <form id="quizForm" onsubmit="submitQuiz(event)">
                @csrf
                
                @foreach($questions as $index => $question)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                {{ $question->question_text }}
                                <span class="badge bg-info ms-2">{{ $question->points }} pts</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($question->image)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $question->image) }}" 
                                         alt="Question Image" 
                                         class="img-fluid rounded"
                                         style="max-height: 300px;">
                                </div>
                            @endif

                            <div class="options-container">
                                @foreach($question->options as $option)
                                    <div class="form-check option-item p-3 mb-2 border rounded">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               id="option_{{ $question->id }}_{{ $option->id }}"
                                               value="{{ $option->id }}"
                                               required>
                                        <label class="form-check-label w-100" 
                                               for="option_{{ $question->id }}_{{ $option->id }}">
                                            {{ $option->option_text }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Submit Button -->
                <div class="card shadow-sm">
                    <div class="card-body text-center py-4">
                        <p class="text-muted mb-3">
                            <i class="fas fa-info-circle"></i>
                            Pastikan semua pertanyaan telah dijawab sebelum submit.
                        </p>
                        <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                            <i class="fas fa-paper-plane"></i> Submit Jawaban
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.option-item {
    transition: all 0.3s ease;
    cursor: pointer;
}

.option-item:hover {
    background-color: #f8f9fa;
    border-color: #007bff !important;
}

.option-item:has(input:checked) {
    background-color: #e7f3ff;
    border-color: #007bff !important;
    font-weight: 500;
}
</style>

<script>
let timeLeft = {{ $quiz->durasi_quiz ?? 0 }} * 60; // Convert to seconds
let timerInterval;

// Start timer if quiz has duration
@if($quiz->durasi_quiz)
    document.addEventListener('DOMContentLoaded', function() {
        startTimer();
    });

    function startTimer() {
        timerInterval = setInterval(function() {
            timeLeft--;
            
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            document.getElementById('timer').textContent = 
                String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            
            // Warning when 5 minutes left
            if (timeLeft === 300) {
                alert('Perhatian! Waktu tersisa 5 menit lagi.');
            }
            
            // Auto submit when time's up
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                alert('Waktu habis! Quiz akan disubmit otomatis.');
                document.getElementById('quizForm').dispatchEvent(new Event('submit'));
            }
        }, 1000);
    }
@endif

function submitQuiz(event) {
    event.preventDefault();
    
    // Check if all questions are answered
    const totalQuestions = {{ $questions->count() }};
    const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
    
    if (answeredQuestions < totalQuestions) {
        if (!confirm(`Anda baru menjawab ${answeredQuestions} dari ${totalQuestions} soal. Yakin ingin submit?`)) {
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
    fetch('{{ route("student.courses.final-quiz.submit", [$kursusId, $attempt->id]) }}', {
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

// Prevent accidental page close
window.addEventListener('beforeunload', function(e) {
    e.preventDefault();
    e.returnValue = '';
});
</script>
@endsection
