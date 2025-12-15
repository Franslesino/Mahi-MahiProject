<?php $__env->startSection('content'); ?>
<div class="p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kelola Pengguna</h2>
        <a href="<?php echo e(route('admin.users.create')); ?>" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 flex items-center gap-2">
            <i class="fas fa-user-plus"></i>
            <span>Tambah Pengguna</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center space-x-3">
                <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="flex items-center gap-2">
                    <input type="text" name="search" placeholder="Cari pengguna..." 
                           value="<?php echo e(request('search')); ?>"
                           class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <select name="role" class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua Role</option>
                        <option value="admin" <?php echo e(request('role') === 'admin' ? 'selected' : ''); ?>>Admin</option>
                        <option value="instructor" <?php echo e(request('role') === 'instructor' ? 'selected' : ''); ?>>Instruktur</option>
                        <option value="student" <?php echo e(request('role') === 'student' ? 'selected' : ''); ?>>Student</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <?php if($users->count() > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bergabung</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600">#<?php echo e($user->id); ?></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <?php if($user->avatar_url): ?>
                                    <img src="<?php echo e($user->avatar_url); ?>" alt="<?php echo e($user->name); ?>" class="w-10 h-10 rounded-full object-cover">
                                <?php else: ?>
                                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm"><?php echo e(strtoupper(substr($user->name, 0, 2))); ?></span>
                                    </div>
                                <?php endif; ?>
                                <p class="font-medium text-gray-800"><?php echo e($user->name); ?></p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($user->email); ?></td>
                        <td class="px-6 py-4">
                            <?php if($user->role === 'admin'): ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">Admin</span>
                            <?php elseif($user->role === 'instructor'): ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Instruktur</span>
                            <?php else: ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                <?php echo e($user->role === 'student' ? 'Student' : ucfirst($user->role)); ?>

                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($user->created_at->format('d M Y')); ?></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                
                                <a href="<?php echo e(route('admin.users.show', $user->id)); ?>" class="text-emerald-600 hover:text-emerald-800" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <?php if($user->id !== auth()->id()): ?>
                                <form action="<?php echo e(route('admin.users.destroy', $user->id)); ?>" method="POST" class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4">
            <?php echo e($users->links()); ?>

        </div>
        <?php else: ?>
        <div class="flex flex-col items-center justify-center py-16 px-6">
            <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg font-semibold">Belum ada data pengguna</p>
            <p class="text-gray-400 text-sm mt-2 text-center">Daftar pengguna akan ditampilkan di sini</p>
            <a href="<?php echo e(route('admin.users.create')); ?>" class="mt-4 px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600">
                Tambah Pengguna
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/admin/users/index.blade.php ENDPATH**/ ?>