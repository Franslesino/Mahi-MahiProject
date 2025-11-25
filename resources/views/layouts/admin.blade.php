{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EDUQUEST') }} - Admin Dashboard</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50"
      x-data="adminSidebar()"
      x-init="init()">
    
    <!-- Toggle Button (Desktop) -->
    <button 
        @click="toggle()" 
        class="hidden lg:block fixed z-50 p-2 bg-white rounded-r-lg shadow-lg transition-all duration-300 hover:bg-gray-50"
        :class="sidebarOpen ? 'left-[320px] top-20' : 'left-0 top-20'">
        <svg 
            class="w-5 h-5 text-gray-600 transition-transform duration-300" 
            :class="sidebarOpen ? '' : 'rotate-180'"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>

    <!-- Mobile Toggle Button -->
    <button 
        @click="toggle()" 
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
        class="fixed top-0 left-0 z-40 h-screen w-80 bg-white shadow-xl flex flex-col lg:translate-x-0">
        
        <!-- Header Sidebar -->
        <div class="flex-shrink-0 p-6 border-b border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-blue-900">EDUQUEST</h2>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- User Profile -->
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xl font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</h3>
                    <p class="text-sm text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto p-4 space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-th-large w-5 text-center"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- Kursus -->
            <a href="{{ route('admin.courses.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.courses*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-book w-5 text-center"></i>
                <span class="font-medium">Kursus</span>
            </a>

            <a href="{{ route('admin.transactions.index') }}" 
   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('admin.transactions.*') ? 'bg-white/10' : '' }}">
    <i class="fas fa-receipt text-xl"></i>
    <span>Transaksi</span>
    @if(isset($pendingTransactionsCount) && $pendingTransactionsCount > 0)
    <span class="ml-auto px-2 py-1 text-xs font-bold bg-yellow-500 text-white rounded-full">
        {{ $pendingTransactionsCount }}
    </span>
    @endif
</a>

            <!-- Pengguna -->
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.users*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-users w-5 text-center"></i>
                <span class="font-medium">Pengguna</span>
            </a>

            <!-- Voucher -->
            <a href="{{ route('admin.vouchers.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.vouchers*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-ticket-alt w-5 text-center"></i>
                <span class="font-medium">Voucher</span>
            </a>

            <!-- Bank Soal -->
            <a href="{{ route('admin.question-banks.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.question-banks*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-question-circle w-5 text-center"></i>
                <span class="font-medium">Bank Soal</span>
            </a>

            <!-- Assignment & Quiz -->
            <a href="{{ route('admin.assignments.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.assignments*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-clipboard-list w-5 text-center"></i>
                <span class="font-medium">Assignment & Quiz</span>
            </a>
        </nav>

        <!-- Logout Button -->
        <div class="flex-shrink-0 p-4 border-t border-gray-200 bg-white">
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
        

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="mx-8 mt-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mx-8 mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-3">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="mx-8 mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            <div class="flex items-center gap-3 mb-2">
                <i class="fas fa-exclamation-circle"></i>
                <span class="font-semibold">Terjadi kesalahan:</span>
            </div>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Content -->
        @yield('content')
    </main>

    {{-- Alpine component: simpan state sidebar di localStorage --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminSidebar', () => ({
                sidebarOpen: true,

                init() {
                    const saved = localStorage.getItem('adminSidebar');
                    this.sidebarOpen = saved === null ? true : saved === 'true';
                },

                toggle() {
                    this.sidebarOpen = !this.sidebarOpen;
                    localStorage.setItem('adminSidebar', this.sidebarOpen ? 'true' : 'false');
                }
            }));
        });
    </script>

    @stack('scripts')
</body>
</html>
