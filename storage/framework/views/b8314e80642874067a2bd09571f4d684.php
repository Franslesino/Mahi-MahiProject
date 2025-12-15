<?php $__env->startSection('title', 'Edit Profile - Pelayanan TIK PNJ'); ?>

<?php $__env->startSection('content'); ?>
<?php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Carbon;

    /** @var \App\Models\User $user */
    $user = isset($user) ? $user : Auth::user();

    // pastikan input <input type="date"> selalu format Y-m-d
    $rawDob   = old('dob', $user->dob);
    $dobValue = $rawDob ? Carbon::parse($rawDob)->format('Y-m-d') : '';
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Edit Profile</h1>

    
    <?php if($errors->any()): ?>
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="text-sm"><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <?php if(session('success')): ?>
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="POST" action="<?php echo e(route('profile.update')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="space-y-6">
                
                <div class="flex items-center gap-6">
                    <div class="relative">
                        <img
                            id="avatarPreview"
                            src="<?php echo e($user->avatar_url
                                    ? $user->avatar_url
                                    : 'data:image/svg+xml;utf8,'.rawurlencode(
                                        '<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'96\' height=\'96\'>
                                            <rect width=\'100%\' height=\'100%\' rx=\'48\' fill=\'#14b8a6\'/>
                                            <text x=\'50%\' y=\'56%\' font-size=\'40\' text-anchor=\'middle\' fill=\'white\' font-family=\'Arial, Helvetica, sans-serif\'>'
                                            . e(strtoupper(substr($user->first_name ?: $user->name, 0, 1)))
                                            . '</text>
                                        </svg>'
                                    )); ?>"
                            alt="Avatar"
                            class="w-24 h-24 rounded-full object-cover ring-2 ring-teal-500/20 bg-gray-100"
                        >
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-700">Ubah Foto</label>
                        <input
                            type="file"
                            name="avatar"
                            id="avatarInput"
                            accept="image/*"
                            class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-teal-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-teal-700"
                        >
                        <p class="text-xs text-gray-500">Format: JPG/PNG/WEBP • Maks 2MB</p>
                    </div>
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Depan</label>
                    <input
                        type="text"
                        name="first_name"
                        value="<?php echo e(old('first_name', $user->first_name)); ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none"
                        required
                    >
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Belakang</label>
                    <input
                        type="text"
                        name="last_name"
                        value="<?php echo e(old('last_name', $user->last_name)); ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none"
                        required
                    >
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email (tidak bisa diubah)</label>
                    <input
                        type="email"
                        value="<?php echo e($user->email); ?>"
                        class="w-full px-4 py-3 border border-gray-200 bg-gray-100 text-gray-500 rounded-lg outline-none cursor-not-allowed"
                        disabled
                    >
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                    <input
                        type="tel"
                        name="phone"
                        value="<?php echo e(old('phone', $user->phone)); ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none"
                        placeholder="08xxxxxxxxxx"
                    >
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                    <input
                        type="date"
                        name="dob"
                        value="<?php echo e($dobValue); ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none"
                    >
                </div>

                
                <?php
                    $profesiOptions = ['Mahasiswa','Pelajar','Karyawan','Dosen','Freelancer','Wiraswasta','Tidak bekerja','Lainnya…'];
                    $profesiValue = old('profesi', $user->profesi);
                ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Profesi</label>
                    <select
                        name="profesi"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none bg-white"
                    >
                        <option value="" <?php echo e($profesiValue === null || $profesiValue === '' ? 'selected' : ''); ?>>Pilih</option>
                        <?php $__currentLoopData = $profesiOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($option); ?>" <?php echo e($profesiValue === $option ? 'selected' : ''); ?>><?php echo e($option); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <?php $g = old('gender', $user->gender); ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select
                        name="gender"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none"
                    >
                        <option value="" <?php echo e($g === null || $g === '' ? 'selected' : ''); ?>>Pilih gender</option>
                        <option value="Pria"   <?php echo e($g === 'Pria' ? 'selected' : ''); ?>>Pria</option>
                        <option value="Wanita" <?php echo e($g === 'Wanita' ? 'selected' : ''); ?>>Wanita</option>
                        <option value="Other"  <?php echo e($g === 'Other' ? 'selected' : ''); ?>>Lainnya</option>
                    </select>
                </div>

                
                <?php if(Schema::hasColumn('users', 'nim')): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NIM</label>
                        <input
                            type="text"
                            name="nim"
                            value="<?php echo e(old('nim', $user->nim)); ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none"
                        >
                    </div>
                <?php endif; ?>

                
                <div class="flex gap-3 pt-4">
                    <button type="submit"
                            class="px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition font-medium">
                        Simpan Perubahan
                    </button>
                    <a href="<?php echo e(url()->previous()); ?>"
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('avatarInput');
    const preview = document.getElementById('avatarPreview');

    if (!input || !preview) return;

    input.addEventListener('change', () => {
        const file = input.files && input.files[0];
        if (!file) return;

        // Validasi size 2MB (opsional, server side juga sudah ada)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran gambar melebihi 2MB.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result; // tampilkan preview
        };
        reader.readAsDataURL(file);
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/profile.blade.php ENDPATH**/ ?>