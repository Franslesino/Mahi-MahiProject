<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pelayanan TIK PNJ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'pnj-blue': '#003d82',
                        'pnj-teal': '#005F56',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-teal-50 to-blue-50 min-h-screen">
    
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="index.html" class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-pnj-blue rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-pnj-blue">Pelayanan TIK</span>
                </a>
                <a href="index.html" class="text-gray-600 hover:text-pnj-blue transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span class="font-medium">Kembali</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-md mx-auto px-4 py-12">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            
            <!-- Login Form -->
            <div class="p-8">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang!</h2>
                    <p class="text-gray-600">Masuk untuk melanjutkan pembelajaran Anda</p>
                </div>
                
                <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
    @csrf
    
    <!-- Error Messages -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Email atau NIM</label>
        <input 
            type="text" 
            name="email"
            value="{{ old('email') }}"
            placeholder="Masukkan email atau NIM"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pnj-teal focus:border-transparent outline-none transition">
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
        <input 
            type="password" 
            name="password"
            placeholder="Masukkan password"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pnj-teal focus:border-transparent outline-none transition">
    </div>

    <div class="flex items-center justify-between">
        <label class="flex items-center">
            <input type="checkbox" name="remember" class="w-4 h-4 text-pnj-teal border-gray-300 rounded focus:ring-pnj-teal">
            <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
        </label>
        <a href="#" class="text-sm text-pnj-teal hover:underline font-medium">Lupa password?</a>
    </div>

    <button 
        type="submit"
        class="w-full bg-pnj-teal text-white py-3 rounded-lg font-semibold hover:bg-teal-700 transition shadow-sm mt-6">
        Masuk
    </button>
</form>

                

                <div class="mt-8 text-center">
                    <p class="text-gray-600">
                        Belum punya akun? 
                        <a href="{{ route('register') }}" class="text-pnj-teal hover:underline font-medium">Masuk di sini</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="mt-8 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-pnj-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Data Anda Aman</h3>
                    <p class="text-sm text-gray-600">Kami menggunakan enkripsi tingkat tinggi untuk melindungi informasi pribadi Anda.</p>
                </div>
            </div>
        </div>
    </main>

</body>
</html>