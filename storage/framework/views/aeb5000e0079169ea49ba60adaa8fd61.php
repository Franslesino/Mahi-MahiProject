

<?php $__env->startSection('content'); ?>
<div class="p-8" data-loaded="false">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Dashboard Instruktur</h2>
        <p class="text-gray-600 mt-2">Selamat datang kembali, <?php echo e(auth()->user()->name); ?>!</p>
    </div>

    <style>
        .skeleton {
            position: relative;
            overflow: hidden;
            background-color: #e5e7eb;
        }
        .skeleton::after {
            content: '';
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, rgba(255,255,255,0) 0, rgba(255,255,255,0.6) 50%, rgba(255,255,255,0) 100%);
            animation: shimmer 1.3s infinite;
        }
        @keyframes shimmer {
            100% { transform: translateX(100%); }
        }
        [data-loaded="false"] .loaded-content { display: none; }
        [data-loaded="true"] .loading-skeleton { display: none; }
    </style>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="loading-skeleton space-y-3">
            <div class="h-28 rounded-lg skeleton"></div>
        </div>
        <div class="loading-skeleton space-y-3">
            <div class="h-28 rounded-lg skeleton"></div>
        </div>
        <div class="loading-skeleton space-y-3">
            <div class="h-28 rounded-lg skeleton"></div>
        </div>
        <div class="loading-skeleton space-y-3">
            <div class="h-28 rounded-lg skeleton"></div>
        </div>

        <div class="loaded-content grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 col-span-4">
            <!-- Total Kursus -->
            <a href="<?php echo e(route('instructor.courses')); ?>" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-blue-300 block">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Kursus</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['totalCourses']); ?></p>
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-full mt-2">
                            <i class="fas fa-arrow-up"></i> Update terbaru
                        </span>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-blue-600 text-xl"></i>
                    </div>
                </div>
            </a>

            <!-- Kursus Aktif -->
            <a href="<?php echo e(route('instructor.courses')); ?>" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-emerald-300 block">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Kursus Aktif</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['activeCourses']); ?></p>
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 text-emerald-700 text-xs rounded-full mt-2">
                            <i class="fas fa-bolt"></i> Siap dijalankan
                        </span>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-emerald-600 text-xl"></i>
                    </div>
                </div>
            </a>

            <!-- Total Siswa -->
            <a href="<?php echo e(route('instructor.courses')); ?>" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-purple-300 block">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Siswa</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['totalStudents']); ?></p>
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 text-purple-700 text-xs rounded-full mt-2">
                            <i class="fas fa-user-check"></i> Aktif belajar
                        </span>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-purple-600 text-xl"></i>
                    </div>
                </div>
            </a>

            <!-- Total Materi -->
            <a href="<?php echo e(route('instructor.courses')); ?>" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-amber-300 block">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Materi</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['totalMaterials']); ?></p>
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-50 text-amber-700 text-xs rounded-full mt-2">
                            <i class="fas fa-layer-group"></i> Konten siap
                        </span>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-amber-600 text-xl"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Charts & Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Course Performance -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6" data-card="performance">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                    Performa Kursus
                </h3>
            </div>

            <div class="loading-skeleton space-y-3 mb-4 hidden">
                <div class="h-6 w-48 skeleton rounded"></div>
                <div class="h-20 skeleton rounded"></div>
                <div class="h-20 skeleton rounded"></div>
            </div>

            <?php if($courseStats->isEmpty()): ?>
                <div class="text-center py-12 loaded-content">
                    <i class="fas fa-chart-bar text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-500">Belum ada data performa kursus</p>
                    <a href="<?php echo e(route('instructor.courses')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-lg border border-blue-100 mt-4 hover:bg-blue-100 transition">
                        <i class="fas fa-book"></i> Kelola kursus
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-4 loaded-content">
                    <?php $__currentLoopData = $courseStats->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $materialsCount = $course->materials_count
                            ?? $course->materials?->count()
                            ?? $course->materi_count
                            ?? 0;
                    ?>
                    <a href="<?php echo e(route('instructor.courses.show', $course)); ?>"
                       class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition border border-transparent hover:border-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-200">
                        <div class="flex items-center gap-4 flex-1">
                            <?php if($course->image_url): ?>
                                <img src="<?php echo e($course->image_url); ?>" 
                                     alt="<?php echo e($course->title ?? $course->judul); ?>"
                                     class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                            <?php else: ?>
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-graduation-cap text-white text-xl"></i>
                                </div>
                            <?php endif; ?>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-800 mb-1 truncate"><?php echo e($course->title ?? $course->judul); ?></h4>
                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span><i class="fas fa-users mr-1"></i><?php echo e($course->students_count ?? 0); ?> siswa</span>
                                    <span><i class="fas fa-file-alt mr-1"></i><?php echo e($materialsCount); ?> materi</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>

            <!-- Recent Activities -->
        <div class="bg-white rounded-xl shadow-sm p-6" data-card="activity">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">
                <i class="fas fa-bell text-blue-500 mr-2"></i>
                Aktivitas Terbaru
            </h3>
            
            <div class="loading-skeleton space-y-3 mb-4 hidden">
                <div class="h-6 w-40 skeleton rounded"></div>
                <div class="h-16 skeleton rounded"></div>
                <div class="h-16 skeleton rounded"></div>
            </div>
            
            <?php if($recentEnrollments->isEmpty()): ?>
                <div class="text-center py-12 loaded-content">
                    <i class="fas fa-bell-slash text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500 text-sm">Belum ada aktivitas</p>
                    <a href="<?php echo e(route('instructor.courses')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-700 rounded-lg border border-gray-200 mt-4 hover:bg-gray-100 transition">
                        <i class="fas fa-book-open"></i> Lihat kursus
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-4 loaded-content">
                    <?php $__currentLoopData = $recentEnrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-start gap-3 pb-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 px-2 -mx-2 rounded-lg transition">
                        <?php if($enrollment->user->avatar): ?>
                            <img src="<?php echo e($enrollment->user->avatar); ?>" 
                                 alt="<?php echo e($enrollment->user->name); ?>"
                                 class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                        <?php else: ?>
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-sm font-bold"><?php echo e(substr($enrollment->user->name, 0, 1)); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800"><?php echo e($enrollment->user->name); ?></p>
                            <p class="text-xs text-gray-500 truncate">
                                Mendaftar <?php echo e($enrollment->kursus ? Str::limit($enrollment->kursus->judul, 25) : 'Kursus tidak ditemukan'); ?>

                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="fas fa-clock mr-1"></i>
                                <?php echo e($enrollment->created_at->diffForHumans()); ?>

                            </p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <?php if($enrollment->status === 'paid' || $enrollment->status === 'completed'): ?>
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full font-medium flex-shrink-0">Lunas</span>
                            <?php endif; ?>
                            <?php if($enrollment->kursus): ?>
                            <a href="<?php echo e(route('instructor.courses.show', $enrollment->kursus)); ?>" class="text-xs text-blue-600 hover:underline">Buka kursus</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
</div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-loaded]').forEach(el => el.setAttribute('data-loaded', 'true'));
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.instructor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/instructor/dashboard.blade.php ENDPATH**/ ?>