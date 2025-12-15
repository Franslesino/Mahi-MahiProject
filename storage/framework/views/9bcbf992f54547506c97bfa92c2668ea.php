<?php $__env->startSection('content'); ?>
<div class="p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kelola Voucher</h2>
        <a href="<?php echo e(route('admin.vouchers.create')); ?>" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Tambah Voucher</span>
        </a>
    </div>

    <?php if(session('success')): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
        <span><?php echo e(session('success')); ?></span>
        <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
        <span><?php echo e(session('error')); ?></span>
        <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-sm">
        <?php if($vouchers->count() > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Berlaku Untuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penggunaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $vouchers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $voucher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-mono font-bold text-emerald-600"><?php echo e($voucher->code); ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800"><?php echo e($voucher->name); ?></p>
                            <?php if($voucher->description): ?>
                            <p class="text-xs text-gray-500 mt-1"><?php echo e(Str::limit($voucher->description, 50)); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($voucher->type === 'percentage'): ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                <i class="fas fa-percent"></i> Persentase
                            </span>
                            <?php else: ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">
                                <i class="fas fa-money-bill"></i> Tetap
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($voucher->type === 'percentage'): ?>
                            <span class="font-bold text-gray-800"><?php echo e($voucher->value); ?>%</span>
                            <?php else: ?>
                            <span class="font-bold text-gray-800">Rp <?php echo e(number_format($voucher->value, 0, ',', '.')); ?></span>
                            <?php endif; ?>
                            <?php if($voucher->max_discount): ?>
                            <p class="text-xs text-gray-500">Max: Rp <?php echo e(number_format($voucher->max_discount, 0, ',', '.')); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($voucher->allowed_courses && count($voucher->allowed_courses) > 0): ?>
                                <?php
                                    $courseCount = count($voucher->allowed_courses);
                                ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">
                                    <i class="fas fa-filter"></i> <?php echo e($courseCount); ?> Kursus
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                    <i class="fas fa-check-double"></i> Semua Kursus
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-800"><?php echo e($voucher->used_count); ?></span>
                            <?php if($voucher->max_usage): ?>
                            <span class="text-sm text-gray-500">/ <?php echo e($voucher->max_usage); ?></span>
                            <?php else: ?>
                            <span class="text-sm text-gray-500">/ ∞</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs"><?php echo e($voucher->start_date ? $voucher->start_date->format('d M Y') : '-'); ?></span>
                                <span class="text-xs text-gray-400">s/d</span>
                                <span class="text-xs"><?php echo e($voucher->end_date ? $voucher->end_date->format('d M Y') : '-'); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php if(!$voucher->is_active): ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                <i class="fas fa-times-circle"></i> Nonaktif
                            </span>
                            <?php elseif($voucher->is_active && !$voucher->end_date): ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                <i class="fas fa-infinity"></i> Aktif Permanen
                            </span>
                            <?php elseif($voucher->is_active && $voucher->end_date && $voucher->end_date->isFuture()): ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                <i class="fas fa-check-circle"></i> Aktif
                            </span>
                            <?php elseif($voucher->end_date && $voucher->end_date->isPast()): ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                <i class="fas fa-clock"></i> Expired
                            </span>
                            <?php else: ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                <i class="fas fa-question-circle"></i> Unknown
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('admin.vouchers.show', $voucher->id)); ?>" 
                                   class="text-gray-600 hover:text-gray-800" 
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.vouchers.edit', $voucher->id)); ?>" 
                                   class="text-blue-600 hover:text-blue-800"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('admin.vouchers.toggle', $voucher->id)); ?>" 
                                      method="POST" 
                                      class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" 
                                            class="text-amber-600 hover:text-amber-800"
                                            title="<?php echo e($voucher->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                </form>
                                <form action="<?php echo e(route('admin.vouchers.destroy', $voucher->id)); ?>" 
                                      method="POST" 
                                      class="inline"
                                      data-confirm="Yakin ingin menghapus voucher ini?">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-800"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4">
            <?php echo e($vouchers->links()); ?>

        </div>
        <?php else: ?>
        <div class="flex flex-col items-center justify-center py-16 px-6">
            <i class="fas fa-ticket-alt text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg font-semibold">Belum ada voucher</p>
            <p class="text-gray-400 text-sm mt-2 text-center">Buat voucher pertama untuk memberikan diskon pada kursus</p>
            <a href="<?php echo e(route('admin.vouchers.create')); ?>" class="mt-4 px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600">
                Tambah Voucher
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/admin/vouchers/index.blade.php ENDPATH**/ ?>