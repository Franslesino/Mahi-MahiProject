<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Back Button -->
                <a href="<?php echo e(route('instructor.courses.show', $course)); ?>" 
                   class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
                    <i class="fas fa-arrow-left"></i>
                    <span class="hidden sm:inline">Kembali ke Kursus</span>
                </a>

                <!-- Course Title -->
                <div class="flex-1 text-center px-4">
                    <h1 class="text-lg font-semibold text-gray-900 truncate"><?php echo e($course->title); ?></h1>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <!-- Placeholder for spacing -->
                    <div class="w-20"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Material Title -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                <?php echo e($material->type === 'video' ? 'bg-blue-100' : ''); ?>

                                <?php echo e($material->type === 'pdf' ? 'bg-red-100' : ''); ?>

                                <?php echo e($material->type === 'quiz' ? 'bg-yellow-100' : ''); ?>

                                <?php echo e($material->type === 'text' ? 'bg-green-100' : ''); ?>">
                                <?php if($material->type === 'video'): ?>
                                    <i class="fas fa-video text-blue-600"></i>
                                <?php elseif($material->type === 'pdf'): ?>
                                    <i class="fas fa-file-pdf text-red-600"></i>
                                <?php elseif($material->type === 'quiz'): ?>
                                    <i class="fas fa-question-circle text-yellow-600"></i>
                                <?php else: ?>
                                    <i class="fas fa-align-left text-green-600"></i>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-2xl font-bold text-gray-900"><?php echo e($material->judul); ?></h2>
                                <?php if($material->description): ?>
                                    <p class="text-gray-600 mt-2"><?php echo e($material->description); ?></p>
                                <?php endif; ?>
                                <div class="flex items-center gap-4 mt-3 text-sm text-gray-500">
                                    <span class="px-2 py-1 bg-gray-100 rounded"><?php echo e(ucfirst($material->type)); ?></span>
                                    <?php if($material->duration): ?>
                                        <span><i class="far fa-clock mr-1"></i><?php echo e($material->duration); ?> menit</span>
                                    <?php endif; ?>
                                    <span class="px-2 py-1 rounded <?php echo e($material->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'); ?>">
                                        <?php echo e($material->status === 'published' ? 'Published' : 'Draft'); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Material Content -->
                    <div class="p-6">
                        <?php if($material->type === 'video'): ?>
                            <!-- Video Player -->
                            <?php
                                $videoSrc = $material->file_url_full ?? $material->url_konten;
                                $mime = 'video/mp4';
                                if ($videoSrc && \Illuminate\Support\Str::endsWith(strtolower($videoSrc), ['.mov'])) {
                                    $mime = 'video/quicktime';
                                }
                            ?>
                            <?php if($videoSrc): ?>
                                <div class="aspect-video bg-black rounded-lg overflow-hidden">
                                    <video controls class="w-full h-full" controlsList="nodownload">
                                        <source src="<?php echo e($videoSrc); ?>" type="<?php echo e($mime); ?>">
                                        Browser Anda tidak mendukung video player.
                                    </video>
                                </div>
                            <?php else: ?>
                                <div class="aspect-video bg-gray-100 rounded-lg flex items-center justify-center">
                                    <div class="text-center">
                                        <i class="fas fa-video text-gray-400 text-5xl mb-3"></i>
                                        <p class="text-gray-500">Video belum diupload</p>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php elseif($material->type === 'pdf'): ?>
                            <!-- PDF Viewer -->
                            <?php if($material->file_url || $material->url_konten): ?>
                                <div class="border border-gray-200 rounded-lg overflow-hidden" style="height: 600px;">
                                    <iframe 
                                        src="<?php echo e($material->file_url ? $material->file_url_full : $material->url_konten); ?>" 
                                        class="w-full h-full"
                                        frameborder="0">
                                    </iframe>
                                </div>
                                <div class="mt-4">
                                    <a href="<?php echo e($material->file_url ? $material->file_url_full : $material->url_konten); ?>" 
                                       target="_blank"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                        <i class="fas fa-download"></i>
                                        <span>Download PDF</span>
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="bg-gray-100 rounded-lg p-12 text-center">
                                    <i class="fas fa-file-pdf text-gray-400 text-5xl mb-3"></i>
                                    <p class="text-gray-500">PDF belum diupload</p>
                                </div>
                            <?php endif; ?>

                        <?php elseif($material->type === 'text'): ?>
                            <!-- Text Content -->
                            <div class="prose max-w-none">
                                <?php if($material->content || $material->isi): ?>
                                    <?php echo nl2br(e($material->content ?? $material->isi)); ?>

                                <?php else: ?>
                                    <div class="bg-gray-100 rounded-lg p-12 text-center">
                                        <i class="fas fa-align-left text-gray-400 text-5xl mb-3"></i>
                                        <p class="text-gray-500">Konten teks belum ditambahkan</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                        <?php elseif($material->type === 'quiz'): ?>
                            <?php $assignment = $material->assignment; ?>
                            <?php if(!$assignment): ?>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                                    <div class="flex items-start gap-3">
                                        <i class="fas fa-exclamation-circle text-yellow-600 text-xl mt-1"></i>
                                        <div>
                                            <h3 class="font-semibold text-yellow-900 mb-2">Quiz belum terhubung</h3>
                                            <p class="text-yellow-700">Quiz ini belum memiliki assignment. Buat ulang lewat halaman kursus.</p>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                                            <p class="text-xs text-blue-700 uppercase mb-1">Jumlah Soal</p>
                                            <p class="text-xl font-bold text-blue-900"><?php echo e($assignment->questions->count()); ?></p>
                                        </div>
                                        <div class="p-4 bg-green-50 rounded-lg border border-green-100">
                                            <p class="text-xs text-green-700 uppercase mb-1">Passing Score</p>
                                            <p class="text-xl font-bold text-green-900"><?php echo e($assignment->passing_score ?? 0); ?>%</p>
                                        </div>
                                        <div class="p-4 bg-purple-50 rounded-lg border border-purple-100">
                                            <p class="text-xs text-purple-700 uppercase mb-1">Durasi</p>
                                            <p class="text-xl font-bold text-purple-900"><?php echo e($assignment->duration_minutes ?? '-'); ?> menit</p>
                                        </div>
                                        <div class="p-4 bg-amber-50 rounded-lg border border-amber-100">
                                            <p class="text-xs text-amber-700 uppercase mb-1">Status</p>
                                            <p class="text-sm font-semibold text-amber-900">
                                                <?php echo e($assignment->is_published ? 'Published' : 'Draft'); ?>

                                                <?php if($assignment->randomize_questions): ?>
                                                    • Acak soal
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>

                                    <?php if($assignment->questions->isEmpty()): ?>
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                                            <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                                            <p class="text-gray-600 mb-4">Belum ada soal pada quiz ini</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="border border-gray-200 rounded-lg">
                                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b">
                                                <h4 class="font-semibold text-gray-800">Soal</h4>
                                                <a href="<?php echo e(route('instructor.assignments.edit-questions', $assignment)); ?>"
                                                   class="text-sm text-blue-600 hover:text-blue-700">Kelola Soal</a>
                                            </div>
                                            <div class="divide-y divide-gray-100">
                                                <?php $__currentLoopData = $assignment->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="px-4 py-3">
                                                        <div class="flex items-start gap-2">
                                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">#<?php echo e($idx+1); ?></span>
                                                            <div class="flex-1">
                                                                <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded"><?php echo e(ucfirst(str_replace('_',' ', $question->type))); ?></span>
                                                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded"><?php echo e($question->pivot->points ?? $question->points); ?> poin</span>
                                                                </div>
                                                                <p class="text-sm text-gray-800"><?php echo e($question->question_text); ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if($assignment): ?>
                                        <div class="mt-4">
                                            <a href="<?php echo e(route('instructor.assignments.edit-questions', $assignment)); ?>"
                                               class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">
                                                <i class="fas fa-cog"></i> Kelola Soal
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Navigation -->
                    <div class="p-6 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                        <?php if($prevMaterial): ?>
                            <a href="<?php echo e(route('instructor.materials.preview', [$course, $prevMaterial])); ?>" 
                               class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                <i class="fas fa-chevron-left"></i>
                                <span>Materi Sebelumnya</span>
                            </a>
                        <?php else: ?>
                            <div></div>
                        <?php endif; ?>

                        <?php if($nextMaterial): ?>
                            <a href="<?php echo e(route('instructor.materials.preview', [$course, $nextMaterial])); ?>" 
                               class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <span>Materi Selanjutnya</span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <div></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden sticky top-20">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-semibold text-gray-900">Daftar Materi</h3>
                        <?php if($material->section): ?>
                            <p class="text-sm text-gray-600 mt-1"><?php echo e($material->section->title); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="max-h-[600px] overflow-y-auto">
                        <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('instructor.materials.preview', [$course, $mat])); ?>" 
                               class="block p-4 border-b border-gray-100 hover:bg-gray-50 transition <?php echo e($mat->id === $material->id ? 'bg-blue-50 border-l-4 border-l-blue-600' : ''); ?>">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded flex items-center justify-center flex-shrink-0
                                        <?php echo e($mat->type === 'video' ? 'bg-blue-100' : ''); ?>

                                        <?php echo e($mat->type === 'pdf' ? 'bg-red-100' : ''); ?>

                                        <?php echo e($mat->type === 'quiz' ? 'bg-yellow-100' : ''); ?>

                                        <?php echo e($mat->type === 'text' ? 'bg-green-100' : ''); ?>">
                                        <?php if($mat->type === 'video'): ?>
                                            <i class="fas fa-play text-blue-600 text-xs"></i>
                                        <?php elseif($mat->type === 'pdf'): ?>
                                            <i class="fas fa-file-pdf text-red-600 text-xs"></i>
                                        <?php elseif($mat->type === 'quiz'): ?>
                                            <i class="fas fa-question-circle text-yellow-600 text-xs"></i>
                                        <?php else: ?>
                                            <i class="fas fa-align-left text-green-600 text-xs"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-sm text-gray-900 truncate <?php echo e($mat->id === $material->id ? 'text-blue-600' : ''); ?>">
                                            <?php echo e($mat->judul); ?>

                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <?php echo e(ucfirst($mat->type)); ?>

                                            <?php if($mat->duration): ?>
                                                • <?php echo e($mat->duration); ?> menit
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <?php if($mat->id === $material->id): ?>
                                        <i class="fas fa-play-circle text-blue-600"></i>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.instructor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/instructor/material-preview.blade.php ENDPATH**/ ?>