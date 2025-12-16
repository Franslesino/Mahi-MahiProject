{{-- resources/views/layouts/instructor.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EDUQUEST') }} - Instructor</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
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
        class="fixed top-0 left-0 z-40 h-screen w-80 bg-white shadow-xl flex flex-col lg:translate-x-0">
        
        <!-- Header Sidebar -->
        <div class="flex-shrink-0 p-6 border-b border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-blue-900">Menu Instruktur</h2>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- User Profile with Email -->
            <?php
                $sidebarUser = auth()->user();
                $sidebarInitial = strtoupper(substr($sidebarUser->first_name ?? $sidebarUser->name, 0, 1));
            ?>
            <div class="flex items-center gap-3">
<<<<<<<< HEAD:resources/views/layouts/instructor.blade.php
                <img
                    src="{{ auth()->user()->avatar_url
                            ? auth()->user()->avatar_url
                            : 'data:image/svg+xml;utf8,'.rawurlencode(
                                '<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'56\' height=\'56\'>
                                    <rect width=\'100%\' height=\'100%\' rx=\'28\' fill=\'#3b82f6\'/>
                                    <text x=\'50%\' y=\'56%\' font-size=\'24\' text-anchor=\'middle\' fill=\'white\' font-family=\'Arial, Helvetica, sans-serif\'>' .
                                    e(auth()->user()->initials) .
                                    '</text>
                                </svg>'
                            )
                        }}"
                    alt="Avatar"
                    class="w-14 h-14 rounded-full object-cover ring-2 ring-blue-500/20 flex-shrink-0"
                >
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</h3>
                    <p class="text-sm text-gray-500 truncate">{{ auth()->user()->email }}</p>
========
                <div class="w-14 h-14 rounded-full overflow-hidden ring-2 ring-blue-500/20 bg-gray-100 flex-shrink-0">
                    <?php if($sidebarUser?->avatar_url): ?>
                        <img src="<?php echo e($sidebarUser->avatar_url); ?>" alt="Avatar" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                            <span class="text-white text-xl font-bold"><?php echo e($sidebarInitial); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 truncate">
                        <?php echo e(trim(($sidebarUser->first_name ?? '') . ' ' . ($sidebarUser->last_name ?? '')) ?: $sidebarUser->name); ?>

                    </h3>
                    <p class="text-sm text-gray-500 truncate"><?php echo e($sidebarUser->email); ?></p>
>>>>>>>> origin/sabil0:storage/framework/views/9531a82020418ae90631f966ab595617.php
                    <span class="inline-block mt-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">
                        Instruktur
                    </span>
                    
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto p-4 space-y-2">
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
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('instructor.courses*') && !request()->routeIs('instructor.courses.materials*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="font-medium">Kursus Saya</span>
            </a>

            <!-- Bank Soal -->
            <a href="{{ route('instructor.question-banks.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('instructor.question-banks*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-folder-open w-5 text-center"></i>
                <span class="font-medium">Bank Soal</span>
            </a>

<<<<<<<< HEAD:resources/views/layouts/instructor.blade.php
========
            <!-- Edit Profile -->
            <a href="<?php echo e(route('instructor.profile')); ?>" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition <?php echo e(request()->routeIs('instructor.profile*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50'); ?>">
                <i class="fas fa-user-cog w-5 text-center"></i>
                <span class="font-medium">Edit Profile</span>
            </a>

>>>>>>>> origin/sabil0:storage/framework/views/9531a82020418ae90631f966ab595617.php

            <!-- Divider -->
            <div class="py-2">
                <div class="border-t border-gray-200"></div>
            </div>

            <!-- Edit Profil -->
            <a href="{{ route('instructor.profile.edit') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('instructor.profile*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="font-medium">Edit Profil</span>
            </a>

            <!-- Bantuan -->
            <a href="{{ route('instructor.help') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('instructor.help') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">Bantuan</span>
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
            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                @csrf
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
                    <!-- Notifications -->
                    <div class="relative" x-data="{ openNotif: false }" @keydown.escape.window="openNotif=false">
                        <button @click="openNotif = !openNotif" class="p-2 hover:bg-gray-100 rounded-lg transition relative">
                            <i class="fas fa-bell text-gray-600"></i>
                            <span id="notif-badge" class="hidden absolute -top-1 -right-1 min-w-[18px] h-4 px-1 bg-red-500 text-white text-[11px] rounded-full flex items-center justify-center"></span>
                        </button>
                        <div x-show="openNotif" x-transition 
                             @click.away="openNotif=false; markAllAsReadOnClose()"
                             x-on:keydown.escape.window="openNotif=false; markAllAsReadOnClose()"
                             class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-30">
                            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-bell text-blue-600"></i>
                                    <span class="font-semibold text-gray-800 text-sm">Notifikasi</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('instructor.notifications.mark-all-read') }}" method="POST" id="notif-mark-all-form" class="hidden">
                                        @csrf
                                        <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-semibold">Tandai semua</button>
                                    </form>
                                    <button onclick="deleteAllNotifications()" id="notif-delete-all-btn" class="text-xs text-red-600 hover:text-red-800 font-semibold hidden">
                                        Hapus semua
                                    </button>
                                </div>
                            </div>
                            <div class="max-h-96 overflow-y-auto" id="notif-list">
                                <div class="px-4 py-6 text-center text-sm text-gray-500">Memuat notifikasi...</div>
                            </div>
                        </div>
                    </div>

                    <!-- User Avatar Mobile -->
                    <div class="lg:hidden flex items-center gap-2">
                        <img
                            src="{{ auth()->user()->avatar_url
                                    ? auth()->user()->avatar_url
                                    : 'data:image/svg+xml;utf8,'.rawurlencode(
                                        '<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'32\' height=\'32\'>
                                            <rect width=\'100%\' height=\'100%\' rx=\'16\' fill=\'#3b82f6\'/>
                                            <text x=\'50%\' y=\'56%\' font-size=\'14\' text-anchor=\'middle\' fill=\'white\' font-family=\'Arial, Helvetica, sans-serif\'>' .
                                            e(auth()->user()->initials) .
                                            '</text>
                                        </svg>'
                                    )
                                }}"
                            alt="Avatar"
                            class="w-8 h-8 rounded-full object-cover ring-1 ring-blue-500/20"
                        >
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
    <script>
        (function() {
            const badge = document.getElementById('notif-badge');
            const list = document.getElementById('notif-list');
            const markAllForm = document.getElementById('notif-mark-all-form');
            const deleteAllBtn = document.getElementById('notif-delete-all-btn');
            const endpoint = "{{ route('instructor.notifications.poll') }}";

            const escapeHtml = (str) => {
                if (!str) return '';
                return str.replace(/[&<>"']/g, m => ({
                    '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
                }[m]));
            };

            const render = (data) => {
                if (!badge || !list) return;
                if (data.unread > 0) {
                    badge.textContent = data.unread;
                    badge.classList.remove('hidden');
                    markAllForm?.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                    markAllForm?.classList.add('hidden');
                }

                if (!data.items || data.items.length === 0) {
                    list.innerHTML = '<div class="px-4 py-6 text-center text-sm text-gray-500">Belum ada notifikasi.</div>';
                    deleteAllBtn?.classList.add('hidden');
                    return;
                }
                
                deleteAllBtn?.classList.remove('hidden');

                list.innerHTML = data.items.map(item => {
                    const typeClass = item.type === 'success'
                        ? 'bg-emerald-100 text-emerald-600'
                        : item.type === 'warning'
                            ? 'bg-amber-100 text-amber-600'
                            : 'bg-blue-100 text-blue-600';
                    const unreadClass = item.unread ? 'bg-blue-50' : '';
                    return `
                        <div class="px-4 py-3 border-b border-gray-100 ${unreadClass}">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center ${typeClass}">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900">${escapeHtml(item.title)}</p>
                                    <p class="text-xs text-gray-600 leading-relaxed">${escapeHtml(item.message)}</p>
                                    <div class="flex items-center gap-3 mt-1">
                                        <p class="text-[11px] text-gray-400">${escapeHtml(item.time)}</p>
                                        <button onclick="deleteNotification(${item.id})" class="text-[11px] text-red-600 hover:text-red-800 font-semibold">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            };

            const poll = async () => {
                try {
                    const res = await fetch(endpoint, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!res.ok) return;
                    const data = await res.json();
                    render(data);
                } catch (e) {
                    // silent fail
                }
            };

            poll();
            setInterval(poll, 15000);
        })();

        function deleteNotification(notificationId) {
            if (!confirm('Hapus notifikasi ini?')) return;
            
            fetch(`/instructor/notifications/${notificationId}`, {
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
                    // Re-poll notifikasi untuk update UI
                    const pollFunc = async () => {
                        const endpoint = "{{ route('instructor.notifications.poll') }}";
                        const badge = document.getElementById('notif-badge');
                        const list = document.getElementById('notif-list');
                        const markAllForm = document.getElementById('notif-mark-all-form');
                        const deleteAllBtn = document.getElementById('notif-delete-all-btn');
                        
                        try {
                            const res = await fetch(endpoint, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            if (!res.ok) return;
                            const data = await res.json();
                            
                            // Update badge
                            if (data.unread > 0) {
                                badge.textContent = data.unread;
                                badge.classList.remove('hidden');
                                markAllForm?.classList.remove('hidden');
                            } else {
                                badge.classList.add('hidden');
                                markAllForm?.classList.add('hidden');
                            }
                            
                            // Update list
                            if (!data.items || data.items.length === 0) {
                                list.innerHTML = '<div class="px-4 py-6 text-center text-sm text-gray-500">Belum ada notifikasi.</div>';
                                deleteAllBtn?.classList.add('hidden');
                            } else {
                                deleteAllBtn?.classList.remove('hidden');
                                list.innerHTML = data.items.map(item => {
                                    const typeClass = item.type === 'success'
                                        ? 'bg-emerald-100 text-emerald-600'
                                        : item.type === 'warning'
                                            ? 'bg-amber-100 text-amber-600'
                                            : 'bg-blue-100 text-blue-600';
                                    const unreadClass = item.unread ? 'bg-blue-50' : '';
                                    const escapeHtml = (str) => {
                                        if (!str) return '';
                                        return str.replace(/[&<>"']/g, m => ({
                                            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
                                        }[m]));
                                    };
                                    return `
                                        <div class="px-4 py-3 border-b border-gray-100 ${unreadClass}">
                                            <div class="flex items-start gap-3">
                                                <div class="w-9 h-9 rounded-full flex items-center justify-center ${typeClass}">
                                                    <i class="fas fa-bell"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-900">${escapeHtml(item.title)}</p>
                                                    <p class="text-xs text-gray-600 leading-relaxed">${escapeHtml(item.message)}</p>
                                                    <div class="flex items-center gap-3 mt-1">
                                                        <p class="text-[11px] text-gray-400">${escapeHtml(item.time)}</p>
                                                        <button onclick="deleteNotification(${item.id})" class="text-[11px] text-red-600 hover:text-red-800 font-semibold">
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }).join('');
                            }
                        } catch (e) {
                            console.error('Error:', e);
                        }
                    };
                    pollFunc();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menghapus notifikasi');
            });
        }

        function deleteAllNotifications() {
            if (!confirm('Hapus semua notifikasi?')) return;
            
            fetch('/instructor/notifications', {
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
                    // Clear UI langsung
                    const badge = document.getElementById('notif-badge');
                    const list = document.getElementById('notif-list');
                    const markAllForm = document.getElementById('notif-mark-all-form');
                    const deleteAllBtn = document.getElementById('notif-delete-all-btn');
                    
                    badge?.classList.add('hidden');
                    markAllForm?.classList.add('hidden');
                    deleteAllBtn?.classList.add('hidden');
                    list.innerHTML = '<div class="px-4 py-6 text-center text-sm text-gray-500">Belum ada notifikasi.</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menghapus notifikasi');
            });
        }

        function markAllAsReadOnClose() {
            const badge = document.getElementById('notif-badge');
            
            // Cek apakah ada notifikasi unread
            if (badge && !badge.classList.contains('hidden')) {
                // Kirim request untuk mark all as read
                fetch('/instructor/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Hide badge setelah berhasil
                    badge.classList.add('hidden');
                    const markAllForm = document.getElementById('notif-mark-all-form');
                    markAllForm?.classList.add('hidden');
                })
                .catch(error => {
                    console.error('Error marking all as read:', error);
                });
            }
        }
    </script>

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

    @include('components.delete-modal')
    @stack('scripts')
</body>
</html>
