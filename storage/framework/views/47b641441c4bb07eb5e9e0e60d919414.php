<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login - UpGreenius</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ug: '#005F56',
                        ugDark: '#014a45',
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-[#f3f4f6] relative">

    <!-- Tombol Kembali (pojok kiri atas) -->
    <a href="<?php echo e(url('/')); ?>"
       class="fixed left-4 top-4 z-50 inline-flex items-center gap-2 text-gray-700 hover:text-ug transition bg-white/80 backdrop-blur px-3 py-2 rounded-md shadow">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        <span class="text-sm font-medium">Kembali</span>
    </a>

    <!-- Wrapper -->
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

        <!-- Kiri: Brand -->
        <div class="hidden lg:flex items-center justify-center bg-white">
            <div class="w-full max-w-md px-6">
                <div class="flex flex-col items-center">
                    <img src="/logo.png" alt="UpGreenius" class="px-3 max-w-full" />
                    <p class="mt-2 text-gray-600 text-lg leading-relaxed text-center">
                        <span class="font-semibold">Upgrade your brain.</span> Stay greenius
                    </p>
                </div>
            </div>
        </div>

        <!-- Kanan: Form Login -->
        <div class="flex items-center justify-center bg-[#f7f7f7]">
            <div class="w-full max-w-md px-6 py-10">

                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">Mulai Sekarang</h1>
                    <p class="text-[13px] text-ug mt-1">
                        Buat akun dan masuk untuk melanjutkan belajar dengan terbaik bersama kami
                    </p>
                </div>

                <!-- NOTIFIKASI -->
                <?php if($errors->any()): ?>
                    <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                        <ul class="text-sm space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="flex items-start gap-2">
                                    <span>�?�</span>
                                    <span><?php echo e($error); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if(session('success')): ?>
                    <div class="mb-5 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm"><?php echo e(session('success')); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- GOOGLE LOGIN BUTTON -->
                <a href="<?php echo e(route('auth.google')); ?>"
                   class="w-full flex items-center justify-center gap-3 px-4 py-3.5 border-2 border-gray-300 rounded-xl hover:bg-gray-50 transition mb-5 bg-white shadow-sm">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="text-sm font-semibold text-gray-700">Masuk dengan Google</span>
                </a>

                <!-- DIVIDER -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-[#f7f7f7] text-gray-500">atau masuk dengan email</span>
                    </div>
                </div>

                <!-- FORM LOGIN -->
                <form action="<?php echo e(route('login.post')); ?>" method="POST" class="space-y-5">
                    <?php echo csrf_field(); ?>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Email</label>
                        <input
                            type="email"
                            name="email"
                            value="<?php echo e(old('email')); ?>"
                            placeholder="bagusarinta@gmail.com"
                            required
                            class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-ug focus:border-transparent outline-none transition bg-gray-50 text-sm">
                    </div>

                    <!-- Password + toggle -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                placeholder="********"
                                required
                                class="w-full px-4 py-3.5 pr-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-ug focus:border-transparent outline-none transition bg-gray-50 text-sm">
                            <button
                                type="button"
                                id="togglePass"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs">
                                Lihat
                            </button>
                        </div>
                    </div>

                    <!-- Remember & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                name="remember"
                                class="w-4 h-4 text-ug border-gray-300 rounded focus:ring-ug">
                            <span class="ml-2 text-sm text-gray-600">Ingat Saya</span>
                        </label>
                        <a href="#" class="text-sm text-ug hover:underline font-medium">
                            Lupa Password?
                        </a>
                    </div>

                    <!-- Tombol Submit -->
                    <button
                        type="submit"
                        id="loginSubmit"
                        class="w-full bg-gradient-to-r from-ug to-ugDark text-white py-4 rounded-xl font-bold text-lg hover:opacity-95 transition shadow-lg mt-4 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <span id="loginSubmitText">Masuk</span>
                        <svg id="loginSpinner" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </button>

                    <!-- Link Register -->
                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-600">
                            Belum punya akun?
                            <a href="<?php echo e(route('register')); ?>" class="text-ug hover:underline font-medium">Daftar di sini</a>
                        </p>
                    </div>
                </form>

                <!-- Benefit kecil -->
                <div class="mt-8 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-semibold text-gray-900 mb-4">Selamat datang kembali di UpGreenius</h3>
                    <ul class="space-y-3 text-sm text-gray-700">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-ug mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Akses materi yang sudah kamu mulai
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-ug mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Lanjutkan progres belajar tanpa hambatan
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <script>
        // toggle show/hide password
        const togglePass = document.getElementById('togglePass');
        const passInput = document.getElementById('password');
        togglePass?.addEventListener('click', function () {
            if (!passInput) return;
            const isPassword = passInput.type === 'password';
            passInput.type = isPassword ? 'text' : 'password';
            this.textContent = isPassword ? 'Sembunyikan' : 'Lihat';
        });

        // submit loading state
        const loginForm = document.querySelector('form[action="<?php echo e(route('login.post')); ?>"]');
        const loginSubmit = document.getElementById('loginSubmit');
        const loginSpinner = document.getElementById('loginSpinner');
        const loginSubmitText = document.getElementById('loginSubmitText');
        if (loginForm && loginSubmit && loginSpinner && loginSubmitText) {
            loginForm.addEventListener('submit', () => {
                loginSubmit.disabled = true;
                loginSpinner.classList.remove('hidden');
                loginSubmitText.textContent = 'Memproses...';
            });
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/auth/login.blade.php ENDPATH**/ ?>