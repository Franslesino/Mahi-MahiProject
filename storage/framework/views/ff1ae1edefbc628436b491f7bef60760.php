<?php $__env->startSection('title', 'Belajar - ' . ($course->judul ?? $course->title)); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-[#f4f2f0]">
    <div class="max-w-6xl mx-auto px-4 py-6">
        <div class="grid grid-cols-2 items-center mb-6 px-4 md:px-8 lg:px-10">
            <div class="relative">
                <a id="learn-back-btn" href="<?php echo e(route('my-courses')); ?>" class="fixed top-24 md:top-20 z-50 inline-flex items-center gap-2 text-gray-700 hover:text-gray-900 transition bg-white px-3 py-2 rounded-md shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
            <div class="text-sm text-gray-600 text-right">
                Progress: <span class="font-semibold text-gray-800"><?php echo e($progress ?? 0); ?>%</span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- Outline -->
            <div>
                <div class="bg-white rounded-2xl shadow p-5">
                    <?php if($sections->isEmpty()): ?>
                        <div class="text-center py-12 text-gray-500">
                            Materi belum tersedia.
                        </div>
                    <?php else: ?>
                        <div class="space-y-5">
                            <?php $index=1; ?>
                            <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-sm text-gray-600 font-semibold">
                                        <span><?php echo e($section->title); ?></span>
                                        <span><?php echo e($section->materials->count()); ?> Materi</span>
                                    </div>
                                    <div class="rounded-xl border border-gray-200 divide-y divide-gray-100 bg-white">
                                        <?php $__empty_1 = true; $__currentLoopData = $section->materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <?php
                                                $isCompleted = !empty($completedIds) && in_array($material->id, $completedIds);
                                                $isActive = ($currentMaterial && $currentMaterial->id === $material->id);
                                                $rowClass = 'w-full text-left px-4 py-3 flex items-center gap-3 hover:bg-gray-50 transition ';
                                                if ($isCompleted) {
                                                    $rowClass .= 'bg-gray-100 ';
                                                } elseif ($isActive) {
                                                    $rowClass .= 'bg-gray-50 ';
                                                }
                                                $targetUrl = $material->type === 'quiz'
                                                    ? route('courses.materials.quiz', [$course, $material->id])
                                                    : route('courses.materials.view', [$course, $material->id]);
                                            ?>
                                            <a href="<?php echo e($targetUrl); ?>"
                                                    class="<?php echo e(trim($rowClass)); ?>">
                                                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100 text-gray-700 font-semibold">
                                                    <?php echo e(sprintf('%02d', $index)); ?>

                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-800 truncate"><?php echo e($material->judul ?? $material->title); ?></p>
                                                    <p class="text-xs text-gray-500 truncate">
                                                        <?php echo e($material->duration ? $material->duration . ' menit' : ' '); ?>

                                                    </p>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs text-gray-400 capitalize"><?php echo e($material->type); ?></span>
                                                    <?php if(!empty($completedIds) && in_array($material->id, $completedIds)): ?>
                                                        <i class="fas fa-check-circle text-emerald-500"></i>
                                                    <?php endif; ?>
                                                </div>
                                            </a>
                                            <?php $index++; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <div class="px-4 py-3 text-sm text-gray-500">Belum ada materi.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if(!empty($finalExam)): ?>
                    <?php
                        $finalStatus = $finalExamStatus ?? [];
                        $finalPassed = $finalStatus['passed'] ?? false;
                        $finalScore = $finalStatus['score'] ?? null;
                        $finalLocked = empty($materialsComplete);
                    ?>
                    <div class="bg-white rounded-2xl shadow p-5 mt-4 border border-gray-100">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm text-gray-500 font-semibold uppercase">Final Quiz</p>
                                <h3 class="text-lg font-bold text-gray-900"><?php echo e($finalExam->title ?? 'Final Quiz'); ?></h3>
                                <p class="text-xs text-gray-500 mt-1">Harus diselesaikan untuk mendapatkan sertifikat kursus.</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Passing grade</p>
                                <p class="text-xl font-bold text-emerald-700"><?php echo e($finalExam->passing_score ?? 70); ?>%</p>
                                <?php if($finalScore !== null): ?>
                                    <p class="text-xs text-gray-500 mt-1">Skor terakhir: <span class="font-semibold"><?php echo e($finalScore); ?>%</span></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            <?php if($finalLocked): ?>
                                <button class="px-4 py-2 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed" disabled>
                                    Selesaikan semua materi untuk membuka final quiz
                                </button>
                            <?php elseif($finalPassed): ?>
                                <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg font-semibold">
                                    <i class="fas fa-check-circle"></i> Final quiz lulus
                                </span>
                                <p class="text-sm text-gray-600">Sertifikat akan tersedia setelah proses selesai.</p>
                            <?php else: ?>
                                <a href="<?php echo e(route('courses.final-quiz.show', $course)); ?>"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-700 text-white rounded-lg hover:bg-emerald-800 transition font-semibold shadow">
                                    <i class="fas fa-flag-checkered"></i>
                                    <?php echo e($finalScore !== null ? 'Ulangi Final Quiz' : 'Mulai Final Quiz'); ?>

                                </a>
                                <?php if(($finalExamStatus['canRetake'] ?? false) === false && $finalScore !== null): ?>
                                    <span class="text-xs text-red-600">Batas percobaan tercapai.</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script>
// Geser tombol back saat sidebar terbuka (kelas sidebar-open dari components/sidebar)
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('learn-back-btn');
    if (!btn) return;

    const updatePos = () => {
        const open = document.body.classList.contains('sidebar-open');
        let left = '3rem';
        if (window.innerWidth >= 1024) { // desktop
            left = open ? '22rem' : '3rem';
        } else if (window.innerWidth >= 768) { // tablet
            left = open ? '14rem' : '3rem';
        }
        btn.style.left = left;
    };

    updatePos();
    window.addEventListener('resize', updatePos);
    const observer = new MutationObserver(updatePos);
    observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/student/learn.blade.php ENDPATH**/ ?>