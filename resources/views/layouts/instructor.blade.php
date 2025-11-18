{{-- resources/views/layouts/instructor.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EDUQUEST') }} - Instructor</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50" x-data="{ sidebarOpen: true }">
    
    <!-- Toggle Button (Desktop) -->
    <button 
        @click="sidebarOpen = !sidebarOpen" 
        class="hidden lg:block fixed z-50 p-2 bg-white rounded-r-lg shadow-lg transition-all duration-300 hover:bg-gray-50"
        :class="sidebarOpen ? 'left-[320px] top-20' : 'left-0 top-20'">
        <svg 
            class="w-5 h-5 text-gray-600 transition-transform duration-300" 
            :class="sidebarOpen ? '' : 'rotate-180'"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>

    <!-- Mobile Toggle Button -->
    <button 
        @click="sidebarOpen = !sidebarOpen" 
        class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-white rounded-lg shadow-lg">
        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <!-- Overlay for Mobile -->
    <div 
        x-show="sidebarOpen" 
        @click="sidebarOpen = false" 
        x-transition.opacity
        class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden">
    </div>

    <!-- Sidebar -->
    <aside 
        x-show="sidebarOpen"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed top-0 left-0 z-40 h-screen w-80 bg-white shadow-xl overflow-y-auto lg:translate-x-0">
        
        <!-- Header Sidebar -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-blue-900">Menu Instruktur</h2>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- User Profile with Email -->
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xl font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</h3>
                    <p class="text-sm text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    <span class="inline-block mt-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">
                        Instruktur
                    </span>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="p-4 space-y-2 pb-24">
            <!-- Dashboard -->
            <a href="{{ route('instructor.dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('instructor.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- Kursus Saya -->
            <a href="{{ route('instructor.courses') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('instructor.courses*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="font-medium">Kursus Saya</span>
            </a>

            <!-- Edit Profile -->
            {{-- <a href="{{ route('profile') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('profile') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="font-medium">Edit Profile</span>
            </a> --}}

            <!-- Divider -->
            <div class="py-2">
                <div class="border-t border-gray-200"></div>
            </div>

            <!-- Bantuan -->
            <a href="#" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition text-gray-700 hover:bg-gray-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">Bantuan</span>
            </a>
        </nav>

        <!-- Logout Button -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 bg-white">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button 
                    type="submit" 
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main 
        class="transition-all duration-300 min-h-screen" 
        :class="sidebarOpen ? 'lg:ml-80' : 'lg:ml-0'">
        
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-20">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-800">Instructor Panel</h1>
                <div class="flex items-center gap-4">
                    <!-- Notifications (Optional) -->
                    <button class="p-2 hover:bg-gray-100 rounded-lg transition relative">
                        <i class="fas fa-bell text-gray-600"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    
                    <!-- User Avatar Mobile -->
                    <div class="lg:hidden flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-semibold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="mx-8 mt-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div class="mx-8 mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif

        @if(session('info'))
        <div class="mx-8 mt-4 p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-info-circle"></i>
                <span>{{ session('info') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-blue-700 hover:text-blue-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif

        @if($errors->any())
        <div class="mx-8 mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="font-semibold">Terjadi kesalahan:</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <ul class="list-disc list-inside space-y-1 ml-8">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Content -->
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>