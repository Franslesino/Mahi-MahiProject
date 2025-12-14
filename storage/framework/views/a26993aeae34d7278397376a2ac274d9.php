<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Result Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center py-5">
                    <?php if($attempt->is_passed): ?>
                        <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                        <h2 class="text-success mb-3">Selamat! Anda Lulus!</h2>
                    <?php else: ?>
                        <i class="fas fa-times-circle fa-5x text-danger mb-3"></i>
                        <h2 class="text-danger mb-3">Belum Berhasil</h2>
                    <?php endif; ?>
                    
                    <h1 class="display-4 mb-3"><?php echo e(number_format($attempt->score, 2)); ?>%</h1>
                    
                    <p class="text-muted mb-4">
                        Percobaan ke-<?php echo e($attempt->attempt_number); ?> | 
                        <?php echo e($attempt->completed_at->format('d M Y, H:i')); ?>

                    </p>

                    <?php if($attempt->is_passed): ?>
                        <div class="alert alert-success d-inline-block">
                            <i class="fas fa-trophy"></i>
                            Nilai Anda melebihi batas minimum <strong><?php echo e($kursus->min_passing_score); ?>%</strong>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger d-inline-block">
                            <i class="fas fa-exclamation-triangle"></i>
                            Nilai minimum: <strong><?php echo e($kursus->min_passing_score); ?>%</strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-list-check fa-2x text-primary mb-2"></i>
                            <h4><?php echo e($attempt->jawabanPeserta->count()); ?></h4>
                            <p class="text-muted mb-0">Total Soal</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-check fa-2x text-success mb-2"></i>
                            <h4><?php echo e($attempt->jawabanPeserta->where('nilai_tercapai', 1)->count()); ?></h4>
                            <p class="text-muted mb-0">Jawaban Benar</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-times fa-2x text-danger mb-2"></i>
                            <h4><?php echo e($attempt->jawabanPeserta->where('nilai_tercapai', 0)->count()); ?></h4>
                            <p class="text-muted mb-0">Jawaban Salah</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-percent fa-2x text-info mb-2"></i>
                            <h4><?php echo e(number_format($attempt->score, 1)); ?>%</h4>
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
                    <?php $__currentLoopData = $attempt->jawabanPeserta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $jawaban): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $question = $jawaban->question; // memakai relasi question
                            $options = $question?->options ?? collect();
                            $correctOption = $options->where('is_correct', true)->first();
                            $selectedOption = $options->firstWhere('id', $jawaban->selected_option_id);
                            $isEssay = ($question?->type === 'essay') || $options->count() === 0;
                            $isCorrect = !$isEssay && $selectedOption && $selectedOption->is_correct;
                        ?>

                        <div class="mb-4 pb-4 <?php echo e(!$loop->last ? 'border-bottom' : ''); ?>">
                            <!-- Question -->
                            <div class="d-flex align-items-start mb-3">
                                <span class="badge <?php echo e($isCorrect ? 'bg-success' : 'bg-danger'); ?> me-2" style="font-size: 1rem;">
                                    <?php echo e($index + 1); ?>

                                </span>
                                <div class="flex-grow-1">
                                    <h6 class="mb-2"><?php echo e($question?->question_text ?? 'Pertanyaan'); ?></h6>
                                    <?php if($question?->image): ?>
                                        <img src="<?php echo e(asset('storage/' . $question->image)); ?>" 
                                             alt="Question Image" 
                                             class="img-fluid rounded mb-2"
                                             style="max-height: 200px;">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Options -->
                            <div class="ms-4">
                                <?php if($isEssay): ?>
                                    <div class="p-3 mb-2 bg-light rounded border">
                                        <strong>Jawaban Anda:</strong>
                                        <div class="mt-1"><?php echo e($jawaban->answer_text ?: '-'); ?></div>
                                    </div>
                                <?php else: ?>
                                    <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $isSelected = $selectedOption && $selectedOption->id == $option->id;
                                            $isCorrectOption = $option->is_correct;
                                        ?>

                                        <div class="p-2 mb-2 rounded
                                            <?php echo e($isCorrectOption ? 'bg-success bg-opacity-10 border border-success' : ''); ?>

                                            <?php echo e($isSelected && !$isCorrectOption ? 'bg-danger bg-opacity-10 border border-danger' : ''); ?>

                                            <?php echo e(!$isSelected && !$isCorrectOption ? 'bg-light' : ''); ?>">
                                            
                                            <div class="d-flex align-items-center">
                                                <?php if($isCorrectOption): ?>
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                <?php elseif($isSelected): ?>
                                                    <i class="fas fa-times-circle text-danger me-2"></i>
                                                <?php else: ?>
                                                    <i class="far fa-circle text-muted me-2"></i>
                                                <?php endif; ?>
                                                
                                                <span class="<?php echo e($isCorrectOption ? 'fw-bold' : ''); ?>">
                                                    <?php echo e($option->option_text); ?>

                                                </span>

                                                <?php if($isSelected && !$isCorrectOption): ?>
                                                    <span class="badge bg-danger ms-2">Jawaban Anda</span>
                                                <?php endif; ?>
                                                <?php if($isCorrectOption): ?>
                                                    <span class="badge bg-success ms-2">Jawaban Benar</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>

                            <!-- Explanation (if available) -->
                            <?php if($question?->penjelasan): ?>
                                <div class="alert alert-info mt-3 ms-4">
                                    <strong><i class="fas fa-lightbulb"></i> Penjelasan:</strong><br>
                                    <?php echo e($question->penjelasan); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow-sm">
                <div class="card-body text-center py-4">
                    <a href="<?php echo e(route('courses.final-quiz.show', $kursus->id)); ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-arrow-left"></i> Kembali ke Final Quiz
                    </a>
                    
                    <?php if(!$attempt->is_passed): ?>
                        <?php
                            $totalAttempts = \App\Models\QuizAttempt::where('user_id', auth()->id())
                                ->where('quiz_id', $quiz->id)
                                ->where('kursus_id', $kursus->id)
                                ->count();
                            $canRetake = $totalAttempts < $kursus->max_quiz_attempts;
                        ?>
                        
                        <?php if($canRetake): ?>
                            <a href="<?php echo e(route('courses.final-quiz.show', $kursus->id)); ?>" class="btn btn-warning btn-lg">
                                <i class="fas fa-redo"></i> Coba Lagi
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/student/courses/final-quiz-result.blade.php ENDPATH**/ ?>