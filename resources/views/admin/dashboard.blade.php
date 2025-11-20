{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('content')
<div x-data="{ currentPage: '{{ request()->get('page', 'dashboard') }}' }">
    <!-- Dashboard Page -->
    <div x-show="currentPage === 'dashboard'" x-transition class="p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h2>
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Kursus</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalCourses ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Pengguna</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalUsers ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-emerald-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Transaksi</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalEnrollments ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Pendapatan</p>
                        <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-wallet text-amber-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Recent Activity Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Pendapatan per Kategori</h3>
                <div class="flex items-center justify-center">
                    <canvas id="revenueChart" width="250" height="250"></canvas>
                </div>
                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                        <div>
                            <p class="text-xs text-gray-500">Web Development</p>
                            <p class="text-sm font-semibold text-gray-800">Rp. 64.2%</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                        <div>
                            <p class="text-xs text-gray-500">Mobile Development</p>
                            <p class="text-sm font-semibold text-gray-800">Rp. 15.3%</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-cyan-400"></div>
                        <div>
                            <p class="text-xs text-gray-500">Data Science</p>
                            <p class="text-sm font-semibold text-gray-800">Rp. 48.6%</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div>
                            <p class="text-xs text-gray-500">Design</p>
                            <p class="text-sm font-semibold text-gray-800">Rp. 8.6%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h3>
                @if(isset($recentEnrollments) && $recentEnrollments->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentEnrollments as $enrollment)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 font-semibold">{{ substr($enrollment->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $enrollment->user->name }}</p>
                                    <p class="text-xs text-gray-500">Mendaftar {{ $enrollment->course->title }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">{{ $enrollment->created_at->diffForHumans() }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-center py-8">Belum ada aktivitas</p>
                @endif
            </div>
        </div>

        <!-- Second Row Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
           

            <!-- Top Courses -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Kursus Terpopuler</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Laravel Mastery</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-gray-600 ml-4">245</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">React Native Basics</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: 70%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-gray-600 ml-4">198</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Python for Data Science</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-cyan-400 h-2 rounded-full" style="width: 65%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-gray-600 ml-4">176</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">UI/UX Design Fundamentals</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: 50%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-gray-600 ml-4">142</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kursus Page -->
    <div x-show="currentPage === 'kursus'" x-transition class="p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Kelola Kursus</h2>
        <div class="bg-white rounded-lg shadow-sm">
            <!-- Page Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center space-x-3">
                  <form method="GET" action="{{ route('admin.courses.index') }}" class="flex items-center">
                        <input type="text" name="search" placeholder="Cari kursus..." 
                               value="{{ request('search') }}"
                               class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <button type="submit" class="ml-2 px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <a href="{{ route('admin.courses.create') }}" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Kursus
                </a>
            </div>

            <!-- Courses Table -->
            @if(isset($courses) && $courses->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kursus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instruktur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($courses as $course)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($course->image)
                                    <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="w-12 h-12 rounded-lg object-cover">
                                    @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-book text-gray-400"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $course->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $course->videos }} video</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $course->category }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $course->instructor->name ?? 'Belum ditentukan' }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    @if($course->discount_price)
                                    <span class="text-gray-400 line-through">Rp {{ number_format($course->price, 0, ',', '.') }}</span>
                                    <span class="text-emerald-600 font-semibold ml-2">Rp {{ number_format($course->discount_price, 0, ',', '.') }}</span>
                                    @else
                                    <span class="text-gray-800 font-semibold">Rp {{ number_format($course->price, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($course->status === 'active')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Aktif</span>
                                @elseif($course->status === 'inactive')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Nonaktif</span>
                                @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Draft</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.courses.edit', $course->id) }}" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Yakin ingin menghapus kursus ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">
                {{ $courses->links() }}
            </div>
            @else
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16 px-6">
                <i class="fas fa-book-open text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg font-semibold">Belum ada data kursus</p>
                <p class="text-gray-400 text-sm mt-2 text-center">Klik tombol "Tambah Kursus" untuk menambahkan kursus baru</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Transactions Page -->
    <div x-show="currentPage === 'transactions'" x-transition class="p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Transaksi</h2>
        <div class="bg-white rounded-lg shadow-sm">
            <!-- Page Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center space-x-3">
                    <select class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Lunas</option>
                        <option value="completed">Selesai</option>
                    </select>
                    <input type="date" class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <button class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 flex items-center">
                    <i class="fas fa-download mr-2"></i>
                    Export
                </button>
            </div>

            <!-- Transactions Table -->
            @if(isset($enrollments) && $enrollments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengguna</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kursus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-600">#{{ $enrollment->id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $enrollment->user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $enrollment->course->title }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">Rp {{ number_format($enrollment->paid_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if($enrollment->status === 'paid')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Lunas</span>
                                @elseif($enrollment->status === 'completed')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Selesai</span>
                                @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $enrollment->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">
                {{ $enrollments->links() }}
            </div>
            @else
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16 px-6">
                <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg font-semibold">Belum ada data transaksi</p>
                <p class="text-gray-400 text-sm mt-2 text-center">Transaksi akan muncul di sini setelah ada pembelian kursus</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Pengguna Page -->
    <div x-show="currentPage === 'pengguna'" x-transition class="p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Kelola Pengguna</h2>
        <div class="bg-white rounded-lg shadow-sm">
            <!-- Page Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center space-x-3">
                   <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center">
                        <input type="text" name="search" placeholder="Cari pengguna..." 
                               value="{{ request('search') }}"
                               class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <select name="role" class="ml-2 px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Semua Role</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="instructor" {{ request('role') === 'instructor' ? 'selected' : '' }}>Instruktur</option>
                            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                        </select>
                        <button type="submit" class="ml-2 px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 flex items-center">
                    <i class="fas fa-user-plus mr-2"></i>
                    Tambah Pengguna
                </a>
            </div>

            <!-- Users Table -->
            @if(isset($users) && $users->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengguna</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bergabung</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold">{{ substr($user->name, 0, 2) }}</span>
                                    </div>
                                    <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @if($user->role === 'admin')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">Admin</span>
                                @elseif($user->role === 'instructor')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Instruktur</span>
                                @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">User</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">
                {{ $users->links() }}
            </div>
            @else
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16 px-6">
                <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg font-semibold">Belum ada data pengguna</p>
                <p class="text-gray-400 text-sm mt-2 text-center">Daftar pengguna akan ditampilkan di sini</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
    // Set currentPage from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page') || 'dashboard';
    
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardData', () => ({
            currentPage: page
        }));
    });

    // Initialize charts when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Donut Chart
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Web Development', 'Mobile Development', 'Data Science', 'Design'],
                    datasets: [{
                        data: [64.2, 15.3, 48.6, 8.6],
                        backgroundColor: [
                            '#F59E0B', // amber-500
                            '#3B82F6', // blue-500
                            '#06B6D4', // cyan-400
                            '#EF4444'  // red-500
                        ],
                        borderWidth: 0,
                        cutout: '70%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed + '%';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Enrollment Line Chart
        const enrollmentCtx = document.getElementById('enrollmentChart');
        if (enrollmentCtx) {
            new Chart(enrollmentCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Pendaftaran',
                        data: [45, 52, 48, 65, 72, 68, 85, 92, 88, 95, 108, 115],
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#10B981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [5, 5]
                            },
                            ticks: {
                                callback: function(value) {
                                    return value;
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush