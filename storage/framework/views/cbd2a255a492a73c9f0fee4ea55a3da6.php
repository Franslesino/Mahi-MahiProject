<?php $__env->startSection('title', $material->judul ?? $material->title); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-[#f4f2f0] py-8">
    <div class="max-w-5xl mx-auto px-4 space-y-6">
        <div class="flex items-center justify-between">
            <a href="<?php echo e(route('student.course.learn', $course)); ?>" class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900 transition bg-white px-3 py-2 rounded-md shadow-sm">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke daftar materi</span>
            </a>
            <?php if($completed): ?>
                <span class="text-sm text-green-700 font-semibold">Selesai</span>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-2xl shadow p-6">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1"><?php echo e(ucfirst($material->type)); ?></p>
                    <h2 class="text-2xl font-semibold text-gray-900"><?php echo e($material->judul ?? $material->title); ?></h2>
                    <?php if($material->description): ?>
                        <p class="text-sm text-gray-600 mt-1"><?php echo e($material->description); ?></p>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-3">
                    <?php
                        $downloadUrl = $material->file_url_full ?? $material->url_konten;
                    ?>
                    <?php if(in_array($material->type, ['video', 'pdf']) && $downloadUrl): ?>
                        <div class="flex items-center gap-2">
                            <button type="button"
                                    class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-semibold"
                                    onclick="toggleFullscreen('#materialViewer')">
                                <i class="fas fa-expand mr-2"></i>Fullscreen
                            </button>
                            <a href="<?php echo e($downloadUrl); ?>"
                               download
                               class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-semibold">
                                <i class="fas fa-download mr-2"></i>Download
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if(!$completed): ?>
                        <form action="<?php echo e(route('courses.materials.complete', [$course, $material->id])); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                                Tandai Selesai
                            </button>
                        </form>
                    <?php else: ?>
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">Sudah Selesai</span>
                    <?php endif; ?>
                </div>
            </div>

            <div id="materialViewer" class="rounded-xl border border-gray-200 bg-black overflow-hidden min-h-[400px] flex items-center justify-center relative">
                <?php if($material->type === 'video'): ?>
                    <?php
                        $videoSrc = $material->video_url ?? $material->file_url_full ?? $material->url_konten;
                        $mime = 'video/mp4';
                        if ($videoSrc && \Illuminate\Support\Str::endsWith(strtolower($videoSrc), ['.mov'])) {
                            $mime = 'video/quicktime';
                        }
                    ?>
                    <?php if($videoSrc): ?>
                        <video class="w-full h-full" controls playsinline>
                            <source src="<?php echo e($videoSrc); ?>" type="<?php echo e($mime); ?>">
                            Browser tidak mendukung video.
                        </video>
                    <?php else: ?>
                        <p class="text-gray-400">Video belum tersedia</p>
                    <?php endif; ?>
                <?php elseif($material->type === 'pdf'): ?>
                    <?php $pdfSrc = $material->file_url_full ?? $material->url_konten; ?>
                    <?php if($pdfSrc): ?>
                        <iframe id="pdfFrame" src="<?php echo e($pdfSrc); ?>" class="w-full h-full" frameborder="0"></iframe>
                    <?php else: ?>
                        <p class="text-gray-400">PDF belum tersedia</p>
                    <?php endif; ?>
                <?php elseif($material->type === 'quiz'): ?>
                    <div class="flex-1 flex items-center justify-center">
                        <a href="<?php echo e(route('courses.materials.quiz', [$course, $material->id])); ?>" class="px-6 py-3 bg-emerald-700 text-white rounded-lg hover:bg-emerald-800 transition shadow">
                            Mulai Quiz
                        </a>
                    </div>
                <?php else: ?>
                    <div class="p-6 text-gray-800 leading-relaxed w-full bg-white min-h-[300px]">
                        <?php echo nl2br(e($material->content ?? $material->description ?? 'Konten belum tersedia')); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleFullscreen(selector) {
        const el = document.querySelector(selector);
        if (!el) return;

        if (!document.fullscreenElement) {
            if (el.requestFullscreen) {
                el.requestFullscreen();
            } else if (el.webkitRequestFullscreen) {
                el.webkitRequestFullscreen();
            } else if (el.msRequestFullscreen) {
                el.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/student/material-view.blade.php ENDPATH**/ ?>