<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pelayanan TIK PNJ')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
<body class="bg-gray-50">
    
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @auth
            @include('components.sidebar')
        @endauth

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col">
            
            <!-- Top Navbar -->
            <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        
                        <!-- Logo -->
                        <div class="flex items-center gap-3">
                            <a href="{{ route('home') }}" class="flex items-center gap-2">
                                <div class="w-10 h-10 bg-pnj-blue rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <span class="text-xl font-bold text-pnj-blue hidden sm:block">Pelayanan TIK</span>
                            </a>
                        </div>

                        <!-- Search Bar -->
                        <div class="hidden md:flex flex-1 mx-10 max-w-lg">
                            <div class="relative w-full">
                                <input 
                                    type="text" 
                                    placeholder="Cari kursus, pelatihan, atau topik..." 
                                    class="w-full rounded-full border border-gray-300 pl-12 pr-4 py-2 focus:ring-2 focus:ring-pnj-blue focus:outline-none shadow-sm placeholder-gray-400 text-sm">
                                <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Auth Buttons / User Info -->
                        <div class="flex items-center space-x-5">
                            @guest
                                <a href="{{ route('login') }}" 
                                   class="hidden sm:block px-4 py-2 text-sm font-medium border border-gray-300 rounded-full hover:bg-gray-50 transition">
                                    Masuk
                                </a>
                                <a href="{{ route('register') }}" 
                                   class="px-4 py-2 text-sm font-medium text-white bg-pnj-teal rounded-full hover:bg-teal-700 transition">
                                    Daftar
                                </a>
                            @endguest
                            
                            @auth
                                <!-- Notification Bell -->
                                <button class="relative p-2 text-gray-600 hover:text-gray-900 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                                </button>

                                <!-- User Avatar (Mobile) -->
                                <div class="lg:hidden flex items-center gap-2">
                                    <div class="w-8 h-8 bg-teal-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Mobile Search -->
            <div class="md:hidden bg-white border-b border-gray-200 py-2 px-4">
                <div class="relative">
                    <input 
                        type="text" 
                        placeholder="Cari kursus..." 
                        class="w-full rounded-full border border-gray-300 pl-10 pr-4 py-2 focus:ring-2 focus:ring-pnj-blue focus:outline-none shadow-sm placeholder-gray-400 text-sm">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 w-full">
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between">
                        <span>{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <main class="flex-1">
                @yield('content')
            </main>

            <!-- Footer -->
            @include('components.footer')
        </div>
    </div>

</body>
</html>