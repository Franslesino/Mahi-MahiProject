
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'EDUQUEST')); ?> - Admin Dashboard</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    
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
                <h2 class="text-2xl font-bold" style="color: var(--upgreen-primary);">UpGreenius</h2>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- User Profile -->
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xl font-bold"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 2))); ?></span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 truncate"><?php echo e(auth()->user()->name); ?></h3>
                    <p class="text-sm text-gray-500 truncate"><?php echo e(auth()->user()->email); ?></p>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto p-4 space-y-2">
            <!-- Dashboard -->
            <a href="<?php echo e(route('admin.dashboard')); ?>" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition <?php echo e(request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50'); ?>">
                <i class="fas fa-th-large w-5 text-center"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <?php
                $isKursusSectionActive = request()->routeIs('admin.courses*') || request()->routeIs('admin.vouchers*') || request()->routeIs('admin.question-banks*');
            ?>
            <div x-data="{ open: <?php echo e($isKursusSectionActive ? 'true' : 'false'); ?> }" class="space-y-1">
                <button type="button"
                        @click="open = !open"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-emerald-300 <?php echo e($isKursusSectionActive ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50'); ?>"
                        :class="open ? 'bg-emerald-50 text-emerald-700' : ''"
                        :aria-expanded="open"
                        aria-controls="menu-kursus">
                    <i class="fas fa-book w-5 text-center"></i>
                    <span class="font-medium flex-1 text-left">Kursus</span>
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition class="space-y-1 pl-10" id="menu-kursus">
                    <a href="<?php echo e(route('admin.courses.index')); ?>"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition <?php echo e(request()->routeIs('admin.courses*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50'); ?>">
                        <i class="fas fa-list-ul w-4 text-center"></i>
                        <span class="font-medium">Daftar Kursus</span>
                    </a>
                    <a href="<?php echo e(route('admin.vouchers.index')); ?>" 
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition <?php echo e(request()->routeIs('admin.vouchers*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50'); ?>">
                        <i class="fas fa-ticket-alt w-4 text-center"></i>
                        <span class="font-medium">Voucher</span>
                    </a>
                    <a href="<?php echo e(route('admin.question-banks.index')); ?>" 
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition <?php echo e(request()->routeIs('admin.question-banks*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50'); ?>">
                        <i class="fas fa-folder-open w-4 text-center"></i>
                        <span class="font-medium">Bank Soal</span>
                    </a>
                </div>
            </div>

            <a href="<?php echo e(route('admin.transactions.index')); ?>" 
   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition <?php echo e(request()->routeIs('admin.transactions.*') ? 'bg-white/10' : ''); ?>">
    <i class="fas fa-receipt text-xl"></i>
    <span>Transaksi</span>
    <?php if(isset($pendingTransactionsCount) && $pendingTransactionsCount > 0): ?>
    <span class="ml-auto px-2 py-1 text-xs font-bold bg-yellow-500 text-white rounded-full">
        <?php echo e($pendingTransactionsCount); ?>

    </span>
    <?php endif; ?>
</a>

            <!-- Pengguna -->
            <a href="<?php echo e(route('admin.users.index')); ?>" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition <?php echo e(request()->routeIs('admin.users*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50'); ?>">
                <i class="fas fa-users w-5 text-center"></i>
                <span class="font-medium">Pengguna</span>
            </a>

            <!-- Banner Promo -->
            <a href="<?php echo e(route('admin.promo-banners.index')); ?>" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition <?php echo e(request()->routeIs('admin.promo-banners*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50'); ?>">
                <i class="fas fa-images w-5 text-center"></i>
                <span class="font-medium">Banner Promo</span>
            </a>
        </nav>

        <!-- Logout Button -->
        <div class="flex-shrink-0 p-4 border-t border-gray-200 bg-white">
            <button 
                type="button"
                onclick="showLogoutModal()"
                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Logout</span>
            </button>
            <form id="logout-form" method="POST" action="<?php echo e(route('logout')); ?>" style="display: none;">
                <?php echo csrf_field(); ?>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main 
        class="transition-all duration-300 min-h-screen"
        :class="sidebarOpen ? 'lg:ml-80' : 'lg:ml-0'">
        <!-- Flash Messages -->
        <?php if(session('success')): ?>
        <div class="mx-8 mt-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="mx-8 mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-3">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
        <div class="mx-8 mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            <div class="flex items-center gap-3 mb-2">
                <i class="fas fa-exclamation-circle"></i>
                <span class="font-semibold">Terjadi kesalahan:</span>
            </div>
            <ul class="list-disc list-inside space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Quick Actions Bar -->
       

        <!-- Content -->
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    
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

    <script>
        

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Logout</h3>
                <p class="text-gray-600">Apakah Anda yakin ingin keluar dari akun?</p>
            </div>
            <div class="flex gap-3">
                <button onclick="hideLogoutModal()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                    Tidak
                </button>
                <button onclick="confirmLogout()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                    Iya, Logout
                </button>
            </div>
        </div>
    </div>

    <script>
        function showLogoutModal() {
            document.getElementById('logoutModal').classList.remove('hidden');
        }
        
        function hideLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }
        
        function confirmLogout() {
            document.getElementById('logout-form').submit();
        }
        
        // Close modal when clicking outside
        document.getElementById('logoutModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                hideLogoutModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideLogoutModal();
            }
        });
    </script>

    <?php echo $__env->make('components.delete-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/layouts/admin.blade.php ENDPATH**/ ?>