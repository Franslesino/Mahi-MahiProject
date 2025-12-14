<?php $__env->startSection('content'); ?>
<div class="p-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kursus Saya</h2>
        <p class="text-gray-600 mt-1">Kelola materi dan konten kursus Anda</p>
    </div>

    <?php if($courses->isEmpty()): ?>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <i class="fas fa-book text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Kursus</h3>
            <p class="text-gray-500 mb-2">Anda belum memiliki kursus yang terdaftar.</p>
            <p class="text-sm text-gray-400">Hubungi admin untuk menambahkan kursus ke akun Anda.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                    <div class="h-48 bg-gradient-to-br from-blue-500 to-blue-600 relative">
                        <?php if($course->image_url): ?>
                            <img src="<?php echo e($course->image_url); ?>" alt="<?php echo e($course->judul); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white text-6xl opacity-20"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo e($course->status === 'active' ? 'bg-green-100 text-green-700' : ($course->status === 'draft' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700')); ?>">
                                <?php echo e(ucfirst($course->status)); ?>

                            </span>
                        </div>
                        <?php if($course->badge): ?>
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-<?php echo e($course->badge_color); ?>-100 text-<?php echo e($course->badge_color); ?>-700">
                                    <?php echo e($course->badge); ?>

                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="p-6">
                        <div class="mb-2">
                            <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded">
                                <?php echo e($course->kategori); ?>

                            </span>
                            <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-1 rounded ml-2">
                                <?php echo e($course->mode); ?>

                            </span>
                        </div>

                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                            <?php echo e($course->judul); ?>

                        </h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            <?php echo e($course->deskripsi); ?>

                        </p>

                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <div class="flex items-center gap-1">
                                <i class="fas fa-file-alt"></i>
                                <span><?php echo e($course->materi_count ?? 0); ?> Materi</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fas fa-users"></i>
                                <span><?php echo e($course->enrollments_count ?? 0); ?> Peserta</span>
                            </div>
                        </div>

                        <?php if($course->harga > 0): ?>
                            <div class="flex items-center gap-2 mb-4">
                                <?php if($course->discount_price > 0): ?>
                                    <span class="text-lg font-bold text-green-600">Rp <?php echo e(number_format($course->discount_price, 0, ',', '.')); ?></span>
                                    <span class="text-sm text-gray-500 line-through">Rp <?php echo e(number_format($course->harga, 0, ',', '.')); ?></span>
                                <?php else: ?>
                                    <span class="text-lg font-bold text-gray-900">Rp <?php echo e(number_format($course->harga, 0, ',', '.')); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="mb-4">
                                <span class="text-lg font-bold text-blue-600">GRATIS</span>
                            </div>
                        <?php endif; ?>

                        <a href="<?php echo e(route('instructor.courses.show', $course)); ?>" 
                           class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Kelola Materi
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.instructor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/instructor/courses.blade.php ENDPATH**/ ?>