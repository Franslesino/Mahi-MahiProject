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
    <a href="{{ url('/') }}"
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
                    <!-- ganti path logo sesuai aset kamu -->
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

                <!-- NOTIFIKASI (logic sama seperti sebelumnya) -->
                @if ($errors->any())
                    <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                        <ul class="text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-start gap-2">
                                    <span>•</span>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="mb-5 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                        <div class="flex items-start gap-2">
                            <span>✓</span>
                            <span class="text-sm">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <!-- FORM LOGIN (logic tidak diubah) -->
                <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Email</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
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
                                placeholder="••••••••"
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

                    <!-- Remember & Forgot Password (tetap ada seperti sebelumnya) -->
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
                        class="w-full bg-gradient-to-r from-ug to-ugDark text-white py-4 rounded-xl font-bold text-lg hover:opacity-95 transition shadow-lg mt-4">
                        Masuk
                    </button>

                    <!-- Link Register (mobile & desktop) -->
                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-600">
                            Belum punya akun?
                            <a href="{{ route('register') }}" class="text-ug hover:underline font-medium">Daftar di sini</a>
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
        // toggle show/hide password (logic sederhana, tidak ganggu autentikasi)
        document.getElementById('togglePass').addEventListener('click', function () {
            const input = document.getElementById('password');
            if (input.type === 'password') {
                input.type = 'text';
                this.textContent = 'Sembunyikan';
            } else {
                input.type = 'password';
                this.textContent = 'Lihat';
            }
        });
    </script>
</body>
</html>
