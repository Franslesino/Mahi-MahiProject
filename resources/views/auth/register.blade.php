<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - UpGreenius</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#0B6E5E',
                        'primary-dark': '#095A4D',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-6xl">
        <div class="grid lg:grid-cols-2 gap-8 items-center">
            
            <!-- Left Side - Welcome Section (Hidden on Mobile) -->
            <div class="hidden lg:flex flex-col items-center justify-center bg-white rounded-3xl p-12 shadow-lg h-full">
                <div class="mb-8">
                    <!-- UpGreenius Logo SVG -->
                    <svg width="180" height="60" viewBox="0 0 180 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0)">
                            <path d="M20 15H25V35C25 38.866 21.866 42 18 42C14.134 42 11 38.866 11 35V15H16V35C16 36.105 16.895 37 18 37C19.105 37 20 36.105 20 35V15Z" fill="#0B6E5E"/>
                            <path d="M32 15H42C45.866 15 49 18.134 49 22V30C49 33.866 45.866 37 42 37H37V42H32V15ZM37 20V32H42C43.105 32 44 31.105 44 30V22C44 20.895 43.105 20 42 20H37Z" fill="#0B6E5E"/>
                            <circle cx="22" cy="28" r="3" fill="#4CAF50"/>
                        </g>
                        <text x="60" y="35" font-family="Arial, sans-serif" font-size="24" font-weight="bold" fill="#0B6E5E">UpGreenius</text>
                    </svg>
                </div>

                <h1 class="text-4xl font-bold text-gray-800 mb-4 text-center">
                    Selamat Datang Di<br>UpGreenius
                </h1>

                <p class="text-gray-600 text-center mb-8 max-w-md">
                    Buat Akun dan masuk untuk melanjutkan belajar dengan terbaik bersama kami
                </p>

                <div class="space-y-4 w-full max-w-sm">
                    <a href="{{ route('login') }}" 
                       class="block w-full py-4 bg-primary text-white text-center rounded-2xl font-semibold hover:bg-primary-dark transition shadow-lg">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" 
                       class="block w-full py-4 bg-gray-100 text-gray-700 text-center rounded-2xl font-semibold hover:bg-gray-200 transition">
                        Daftar
                    </a>
                </div>

                <div class="mt-12 text-center">
                    <p class="text-sm text-gray-500">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="text-primary hover:underline font-medium">Masuk di sini</a>
                    </p>
                </div>
            </div>

            <!-- Right Side - Register Form -->
            <div class="bg-white rounded-3xl shadow-xl p-8 lg:p-12">
                
                <!-- Mobile Logo -->
                <div class="lg:hidden flex justify-center mb-8">
                    <!-- UpGreenius Logo SVG Mobile -->
                    <svg width="150" height="50" viewBox="0 0 150 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0)">
                            <path d="M15 10H19V25C19 27.761 16.761 30 14 30C11.239 30 9 27.761 9 25V10H13V25C13 25.552 13.448 26 14 26C14.552 26 15 25.552 15 25V10Z" fill="#0B6E5E"/>
                            <path d="M24 10H31C33.761 10 36 12.239 36 15V21C36 23.761 33.761 26 31 26H28V30H24V10ZM28 14V22H31C31.552 22 32 21.552 32 21V15C32 14.448 31.552 14 31 14H28Z" fill="#0B6E5E"/>
                            <circle cx="16.5" cy="19" r="2" fill="#4CAF50"/>
                        </g>
                        <text x="45" y="25" font-family="Arial, sans-serif" font-size="18" font-weight="bold" fill="#0B6E5E">UpGreenius</text>
                    </svg>
                </div>

                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Mulai Sekarang</h2>
                    <p class="text-gray-600">Buat Akun dan mulai belajar dengan terbaik bersama kami</p>
                </div>

                <form action="{{ route('register.post') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                            <ul class="text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-exclamation-circle mt-0.5"></i>
                                        <span>{{ $error }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Name Fields Grid -->
                    <div class="grid md:grid-cols-2 gap-4">
                        <!-- First Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Awal</label>
                            <input 
                                type="text" 
                                name="first_name"
                                value="{{ old('first_name') }}"
                                placeholder="Bagus"
                                required
                                class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition bg-gray-50">
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Akhir</label>
                            <input 
                                type="text" 
                                name="last_name"
                                value="{{ old('last_name') }}"
                                placeholder="Arinta"
                                required
                                class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition bg-gray-50">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input 
                            type="email" 
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="bagusarinta@gmail.com"
                            required
                            class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition bg-gray-50">
                    </div>

                    <!-- Birth Date -->
                    

                    <!-- Phone Number -->
                    

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Buat Password</label>
                        <div class="relative">
                            <input 
                                type="password" 
                                name="password"
                                id="password"
                                placeholder="••••••••"
                                required
                                class="w-full px-4 py-3.5 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition bg-gray-50">
                            <button 
                                type="button"
                                onclick="togglePassword('password')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="password-icon"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Password harus minimal 8 karakter</p>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full bg-primary text-white py-4 rounded-xl font-bold text-lg hover:bg-primary-dark transition shadow-lg mt-8">
                        Daftar
                    </button>

                    <!-- Login Link (Mobile) -->
                    <div class="lg:hidden text-center mt-6">
                        <p class="text-sm text-gray-600">
                            Sudah punya akun? 
                            <a href="{{ route('login') }}" class="text-primary hover:underline font-medium">Masuk di sini</a>
                        </p>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>