<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1"><?php echo e($quiz->judul_quiz); ?></h3>
                            <p class="text-muted mb-0">Percobaan ke-<?php echo e($attempt->attempt_number); ?></p>
                        </div>
                        <div class="text-end">
                            <?php if($quiz->durasi_quiz): ?>
                                <div class="alert alert-warning mb-0 py-2 px-3">
                                    <i class="fas fa-clock"></i>
                                    <strong id="timer"><?php echo e($quiz->durasi_quiz); ?>:00</strong>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Form -->
            <form id="quizForm" onsubmit="submitQuiz(event)">
                <?php echo csrf_field(); ?>
                
                <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <span class="badge bg-primary me-2"><?php echo e($index + 1); ?></span>
                                <?php echo e($question->question_text); ?>

                                <span class="badge bg-info ms-2"><?php echo e($question->points); ?> pts</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if($question->image): ?>
                                <div class="mb-3">
                                    <img src="<?php echo e(asset('storage/' . $question->image)); ?>" 
                                         alt="Question Image" 
                                         class="img-fluid rounded"
                                         style="max-height: 300px;">
                                </div>
                            <?php endif; ?>

                            <div class="options-container">
                                <?php
                                    $isEssay = $question->type === 'essay';
                                    $hasOptions = $question->options && $question->options->count() > 0;
                                ?>

                                <?php if($isEssay || !$hasOptions): ?>
                                    <div class="mb-2">
                                        <textarea name="answers[<?php echo e($question->id); ?>]" rows="4" class="form-control"
                                                  placeholder="Tuliskan jawaban Anda di sini..." required></textarea>
                                    </div>
                                <?php else: ?>
                                    <?php $__currentLoopData = $question->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="form-check option-item p-3 mb-2 border rounded">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="answers[<?php echo e($question->id); ?>]" 
                                                   id="option_<?php echo e($question->id); ?>_<?php echo e($option->id); ?>"
                                                   value="<?php echo e($option->id); ?>"
                                                   required>
                                            <label class="form-check-label w-100" 
                                                   for="option_<?php echo e($question->id); ?>_<?php echo e($option->id); ?>">
                                                <?php echo e($option->option_text); ?>

                                            </label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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
let timeLeft = <?php echo e($quiz->durasi_quiz ?? 0); ?> * 60; // Convert to seconds
let timerInterval;

// Start timer if quiz has duration
<?php if($quiz->durasi_quiz): ?>
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
<?php endif; ?>

function submitQuiz(event) {
    event.preventDefault();
    
    // Check if all questions are answered (termasuk essay)
    const totalQuestions = <?php echo e($questions->count()); ?>;
    const answeredRadios = document.querySelectorAll('input[type="radio"]:checked').length;
    const answeredEssays = Array.from(document.querySelectorAll('textarea[name^="answers["]'))
        .filter(t => t.value.trim() !== '').length;
    const answeredQuestions = answeredRadios + answeredEssays;
    
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
    fetch('<?php echo e(route("courses.final-quiz.submit", [$kursusId, $attempt->id])); ?>', {
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/student/courses/take-final-quiz.blade.php ENDPATH**/ ?>