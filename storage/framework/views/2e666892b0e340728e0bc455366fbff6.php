<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Pelayanan TIK PNJ'); ?></title>
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
    <style>
        /* Custom scrollbar for notifications */
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Line clamp utility */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Smooth transitions */
        @media (prefers-reduced-motion: no-preference) {
            * {
                scroll-behavior: smooth;
            }
        }
    </style>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
</head>
<body class="bg-gray-50">
    
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php if(auth()->guard()->check()): ?>
            <?php echo $__env->make('components.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col">
            
            <!-- Top Navbar -->
            <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        
                        <!-- Logo -->
                        <div class="flex items-center gap-3">
                               <a href="<?php echo e(route('home')); ?>" class="flex items-center">
                            <img src="/logo.png" alt="Logo" class="object-contain" style="height: 200px;">
                        </a>
                            </a>
                        </div>

                        <!-- Search Bar (Desktop) -->
                        <div class="hidden md:flex flex-1 mx-10 max-w-lg">
                            <form action="<?php echo e(route('home')); ?>" method="GET" class="hidden md:flex flex-1 mx-6 max-w-md">
    <div class="relative w-full">
        <input
            type="text"
            name="search"
            value="<?php echo e(request('search')); ?>"
            placeholder="Cari kursus, pelatihan, atau topik..."
            class="w-full rounded-full border border-gray-300 pl-12 pr-4 py-2 focus:ring-2 focus:ring-pnj-blue focus:outline-none shadow-sm placeholder-gray-400 text-sm">
        <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </div>
</form>

                        </div>

                        <!-- Auth Buttons / User Info -->
                        <div class="flex items-center space-x-5">
                            <?php if(auth()->guard()->guest()): ?>
                                <a href="<?php echo e(route('login')); ?>" 
                                   class="hidden sm:block px-4 py-2 text-sm font-medium border border-gray-300 rounded-full hover:bg-gray-50 transition">
                                    Masuk
                                </a>
                                <a href="<?php echo e(route('register')); ?>" 
                                   class="px-4 py-2 text-sm font-medium text-white bg-pnj-teal rounded-full hover:bg-teal-700 transition">
                                    Daftar
                                </a>
                            <?php endif; ?>
                            
                            <?php if(auth()->guard()->check()): ?>
                                <!-- Notification Bell with Dropdown (Desktop & Mobile) -->
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 transition rounded-lg hover:bg-gray-100">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        <?php if(Auth::user()->unreadNotifications()->count() > 0): ?>
                                            <span class="absolute top-0 right-0 min-w-[18px] h-[18px] sm:min-w-[20px] sm:h-5 bg-red-500 rounded-full text-white text-[10px] sm:text-xs flex items-center justify-center font-semibold px-1">
                                                <?php echo e(Auth::user()->unreadNotifications()->count() > 9 ? '9+' : Auth::user()->unreadNotifications()->count()); ?>

                                            </span>
                                        <?php endif; ?>
                                    </button>

                                    <!-- Dropdown (Responsive) -->
                                    <div x-show="open" 
                                         @click.away="open = false; markAllAsReadOnClose()"
                                         x-on:keydown.escape.window="open = false; markAllAsReadOnClose()"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="fixed sm:absolute right-0 sm:right-0 mt-2 w-screen sm:w-96 max-w-md bg-white rounded-none sm:rounded-xl shadow-2xl border-t sm:border border-gray-200 overflow-hidden z-50"
                                         style="display: none;">
                                        
                                        <!-- Header -->
                                        <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-teal-50 border-b border-gray-200">
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                                    </svg>
                                                    <h3 class="font-semibold text-gray-900 text-base">Notifikasi</h3>
                                                    <?php if(Auth::user()->unreadNotifications()->count() > 0): ?>
                                                        <span class="bg-red-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                                            <?php echo e(Auth::user()->unreadNotifications()->count()); ?>

                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <?php if(Auth::user()->unreadNotifications()->count() > 0): ?>
                                                        <form action="<?php echo e(route('notifications.mark-all-read')); ?>" method="POST" class="inline">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="text-xs font-medium text-blue-600 hover:text-blue-800 hover:underline transition">
                                                                Tandai semua
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <?php if(Auth::user()->notifications()->count() > 0): ?>
                                                        <button onclick="deleteAllNotifications()" 
                                                                class="text-xs font-medium text-red-600 hover:text-red-800 hover:underline transition">
                                                            Hapus semua
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Notifications List -->
                                        <div class="max-h-[70vh] sm:max-h-[500px] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                                            <?php $__empty_1 = true; $__currentLoopData = Auth::user()->notifications()->take(15)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <div class="group relative <?php echo e($notification->isUnread() ? 'bg-blue-50 border-l-4 border-l-blue-500' : 'bg-white'); ?> hover:bg-gray-50 transition-colors">
                                                    <div class="p-4 flex items-start gap-3">
                                                        <!-- Icon -->
                                                        <div class="flex-shrink-0 mt-0.5">
                                                            <?php if($notification->type === 'success'): ?>
                                                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center ring-2 ring-green-200">
                                                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                </div>
                                                            <?php elseif($notification->type === 'info'): ?>
                                                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center ring-2 ring-blue-200">
                                                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                </div>
                                                            <?php elseif($notification->type === 'warning'): ?>
                                                                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center ring-2 ring-yellow-200">
                                                                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center ring-2 ring-gray-200">
                                                                    <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        
                                                        <!-- Content -->
                                                        <div class="flex-1 min-w-0">
                                                            <div class="flex items-start justify-between gap-2">
                                                                <p class="text-sm font-semibold text-gray-900 leading-tight"><?php echo e($notification->title); ?></p>
                                                                <?php if($notification->isUnread()): ?>
                                                                    <span class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full mt-1"></span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <p class="text-xs text-gray-600 mt-1.5 line-clamp-2 leading-relaxed"><?php echo e($notification->message); ?></p>
                                                            <div class="flex items-center gap-3 mt-2">
                                                                <p class="text-xs text-gray-400 font-medium">
                                                                    <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    <?php echo e($notification->created_at->diffForHumans()); ?>

                                                                </p>
                                                                <?php if($notification->isUnread()): ?>
                                                                    <form action="<?php echo e(route('notifications.mark-read', $notification)); ?>" method="POST" class="inline">
                                                                        <?php echo csrf_field(); ?>
                                                                        <button type="submit" class="text-xs font-medium text-blue-600 hover:text-blue-800 hover:underline transition">
                                                                            Tandai dibaca
                                                                        </button>
                                                                    </form>
                                                                <?php endif; ?>
                                                                <button onclick="deleteNotification(<?php echo e($notification->id); ?>)" 
                                                                        class="text-xs font-medium text-red-600 hover:text-red-800 hover:underline transition">
                                                                    Hapus
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="border-b border-gray-100"></div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <div class="p-12 text-center">
                                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                                        </svg>
                                                    </div>
                                                    <p class="text-sm font-medium text-gray-900">Tidak ada notifikasi</p>
                                                    <p class="text-xs text-gray-500 mt-1">Anda akan menerima notifikasi di sini</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Footer (Mobile Close Button) -->
                                        <div class="sm:hidden border-t border-gray-200 p-3 bg-gray-50">
                                            <button @click="open = false" class="w-full py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition">
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- User Avatar (Mobile) -->
                                <div class="lg:hidden flex items-center gap-2">
                                    <div class="w-8 h-8 bg-teal-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-semibold"><?php echo e(substr(Auth::user()->name, 0, 1)); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Mobile Search -->
            <div class="md:hidden bg-white border-b border-gray-200 py-2 px-4">
                <form action="<?php echo e(route('home')); ?>" method="GET" class="relative">
    <input 
        type="text"
        name="search"
        value="<?php echo e(request('search')); ?>"
        placeholder="Cari kursus..." 
        class="w-full rounded-full border border-gray-300 pl-10 pr-4 py-2 focus:ring-2 focus:ring-pnj-blue focus:outline-none shadow-sm placeholder-gray-400 text-sm">
    <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
    </svg>
</form>

            </div>

            <!-- Flash Messages -->
            <?php if(session('success')): ?>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 w-full">
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between">
                        <span><?php echo e(session('success')); ?></span>
                        <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <main class="flex-1">
                <?php echo $__env->yieldContent('content'); ?>
            </main>

            <!-- Footer -->
            <?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    <script>
        function deleteNotification(notificationId) {
            if (!confirm('Hapus notifikasi ini?')) return;
            
            fetch(`/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload halaman untuk update badge dan list
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menghapus notifikasi');
            });
        }

        function deleteAllNotifications() {
            if (!confirm('Hapus semua notifikasi?')) return;
            
            fetch('/notifications', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload halaman untuk update badge dan list
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menghapus notifikasi');
            });
        }

        function markAllAsReadOnClose() {
            <?php if(auth()->guard()->check()): ?>
            const unreadCount = <?php echo e(Auth::user()->unreadNotifications()->count()); ?>;
            
            // Cek apakah ada notifikasi unread
            if (unreadCount > 0) {
                // Kirim request untuk mark all as read
                fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    // Reload halaman untuk update badge
                    setTimeout(() => {
                        window.location.reload();
                    }, 100);
                })
                .catch(error => {
                    console.error('Error marking all as read:', error);
                });
            }
            <?php endif; ?>
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/layouts/app.blade.php ENDPATH**/ ?>