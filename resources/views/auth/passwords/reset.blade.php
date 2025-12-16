<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - UpGreenius</title>
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
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-lg bg-white shadow-lg rounded-2xl p-8 relative">
            <a href="{{ route('login') }}" class="absolute left-4 top-4 text-sm text-gray-600 hover:text-ug flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>

            <div class="text-center mb-8">
                <h1 class="text-2xl font-semibold text-ug mb-2">Reset Password</h1>
                <p class="text-gray-600 text-sm">Masukkan email dan password baru Anda.</p>
            </div>

            @if (session('status'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $email ?? '') }}" required autofocus
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-ug @error('email') border-red-500 @enderror">
                    @error('email')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-ug @error('password') border-red-500 @enderror">
                    @error('password')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-ug">
                </div>

                <button type="submit"
                        class="w-full bg-ug hover:bg-ugDark text-white font-semibold py-3 rounded-lg transition">
                    Simpan Password Baru
                </button>
            </form>
        </div>
    </div>
</body>
</html>
