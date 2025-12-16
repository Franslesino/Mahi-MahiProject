<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Verifikasi Email - UpGreenius</title>

    <script>
        // Prevent back button to this page after login
        if (window.history && window.history.pushState) {
            window.history.pushState('forward', null, window.location.href);
            window.onpopstate = function () {
                window.history.pushState('forward', null, window.location.href);
            };
        }
    </script>
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

<body class="min-h-screen bg-[#f3f4f6]">

    <!-- Wrapper -->
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

        <!-- Left: Brand -->
        <div class="hidden lg:flex items-center justify-center bg-white">
            <div class="max-w-md w-full px-10">
                <div class="flex flex-col items-center text-center">
                    <img src="/logo.png" alt="UpGreenius" class="px-3 mb-1" />
                    <p class="mt-1 text-gray-600 text-lg leading-relaxed text-center">
                        <span class="font-semibold">Upgrade your brain.</span> Stay greenius
                    </p>
                </div>
            </div>
        </div>

        <!-- Right: Content -->
        <div class="flex items-center justify-center bg-[#f7f7f7] relative">

            <!-- Tombol Kembali -->
            <a href="{{ url('/') }}"
                class="fixed left-4 top-4 z-50 inline-flex items-center gap-2 text-gray-700 hover:text-ug transition bg-white/80 backdrop-blur px-3 py-2 rounded-md shadow">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span class="text-sm font-medium">Kembali</span>
            </a>

            <div class="w-full max-w-md px-6 py-10">
                <!-- Icon Email -->
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-ug/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-ug" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">Cek Email Anda</h1>
                    <p class="text-gray-600 mt-2 text-sm">
                        Kami telah mengirimkan link verifikasi ke email Gmail Anda.
                        Silakan cek inbox (atau folder spam) dan klik link untuk mengaktifkan akun.
                    </p>
                </div>

                @if (session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Resend Email Form -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Belum menerima email?</h3>
                    <form action="{{ route('verification.send') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Email yang didaftarkan</label>
                            <input type="email" name="email" required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-ug focus:border-ug outline-none text-sm"
                                placeholder="Masukkan email Gmail Anda" />
                        </div>
                        <button type="submit" class="w-full rounded-md py-2.5 text-white text-sm font-semibold
                     bg-gradient-to-r from-ug to-ugDark hover:opacity-95 transition">
                            Kirim Ulang Link Verifikasi
                        </button>
                    </form>
                </div>

                <!-- Tips -->
                <div class="mt-6 bg-blue-50 border border-blue-100 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">💡 Tips:</h4>
                    <ul class="text-xs text-blue-800 space-y-1">
                        <li>• Cek folder <strong>Spam</strong> atau <strong>Promosi</strong> di Gmail</li>
                        <li>• Email dikirim dari <strong>UpGreenius</strong></li>
                        <li>• Link verifikasi berlaku selama 60 menit</li>
                    </ul>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-[12px] text-gray-600">
                        Sudah verifikasi?
                        <a href="{{ route('login') }}" class="text-ug font-medium hover:underline">Login sekarang</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>