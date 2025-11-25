{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Dashboard Admin</h2>
        <p class="text-gray-600 mt-1">Selamat datang kembali, {{ Auth::user()->name }}!</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Kursus -->
        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Kursus</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalCourses }}</p>
                    <p class="text-xs text-green-600 mt-1 font-semibold">
                        <i class="fas fa-arrow-up"></i>
                        {{ $activeCourses }} Aktif
                    </p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Pengguna -->
        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Pengguna</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalUsers }}</p>
                    <p class="text-xs text-gray-600 mt-1 font-semibold">
                        {{ $totalStudents }} Students • {{ $totalInstructors }} Instructors
                    </p>
                </div>
                <div class="w-14 h-14 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-emerald-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Transaksi</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalTransactions }}</p>
                    <p class="text-xs text-yellow-600 mt-1 font-semibold">
                        <i class="fas fa-clock"></i>
                        {{ $pendingTransactions }} Pending
                    </p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Pendapatan</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-green-600 mt-1 font-semibold">
                        <i class="fas fa-check-circle"></i>
                        {{ $paidTransactions }} Paid
                    </p>
                </div>
                <div class="w-14 h-14 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wallet text-amber-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1: Revenue & Transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue by Category Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Pendapatan per Kategori</h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    Total: Rp {{ number_format($categoryData->sum('total'), 0, ',', '.') }}
                </span>
            </div>
            
            @if($categoryData->count() > 0)
            <div class="flex items-center justify-center mb-6">
                <canvas id="revenueChart" width="250" height="250"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-4">
                @php
                    $colors = ['#F59E0B', '#3B82F6', '#06B6D4', '#EF4444', '#10B981', '#8B5CF6'];
                @endphp
                @foreach($categoryData->take(6) as $index => $cat)
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $colors[$index % 6] }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 truncate">{{ $cat['category'] }}</p>
                        <p class="text-sm font-bold text-gray-800">{{ $cat['percentage'] }}%</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-12">
                <i class="fas fa-chart-pie text-6xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-semibold">Belum ada data pendapatan</p>
                <p class="text-gray-400 text-sm mt-1">Data akan muncul setelah ada transaksi paid</p>
            </div>
            @endif
        </div>

        <!-- Monthly Transactions Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Transaksi Bulanan {{ now()->year }}</h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    Total: {{ $monthlyData->sum() }} transaksi
                </span>
            </div>
            
            <div style="height: 250px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2: Gender & Profession -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Gender Distribution Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Distribusi Gender Pengguna</h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    Total: {{ $genderData->sum('count') }} pengguna
                </span>
            </div>
            
            @if($genderData->count() > 0)
            <div class="flex items-center justify-center mb-6">
                <canvas id="genderChart" width="250" height="250"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-4">
                @php
                    $genderColors = ['#3B82F6', '#EC4899', '#8B5CF6'];
                @endphp
                @foreach($genderData as $index => $gender)
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $genderColors[$index % 3] }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500">{{ $gender['gender'] }}</p>
                        <p class="text-sm font-bold text-gray-800">{{ $gender['count'] }} ({{ $gender['percentage'] }}%)</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-12">
                <i class="fas fa-users text-6xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-semibold">Belum ada data gender</p>
                <p class="text-gray-400 text-sm mt-1">Data akan muncul setelah pengguna mengisi profil</p>
            </div>
            @endif
        </div>

        <!-- Profession Distribution Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Profesi Pengguna</h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    Total: {{ $profesiData->sum('count') }} pengguna
                </span>
            </div>
            
            @if($profesiData->count() > 0)
            <div style="height: 300px;">
                <canvas id="profesiChart"></canvas>
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-12">
                <i class="fas fa-briefcase text-6xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-semibold">Belum ada data profesi</p>
                <p class="text-gray-400 text-sm mt-1">Data akan muncul setelah pengguna mengisi profil</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Bottom Row: Top Courses & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Courses -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Kursus Terpopuler</h3>
                <a href="{{ route('admin.courses.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            @if($topCourses->count() > 0)
            <div class="space-y-4">
                @foreach($topCourses as $index => $course)
                <div class="flex items-center justify-between group hover:bg-gray-50 p-3 rounded-lg transition">
                    <div class="flex items-center gap-3 flex-1">
                        <div class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-sm">{{ $index + 1 }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate group-hover:text-emerald-600 transition">
                                {{ $course['title'] }}
                            </p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-emerald-500 h-2 rounded-full transition-all" style="width: {{ $course['percentage'] }}%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-600 ml-4">{{ $course['count'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-12">
                <i class="fas fa-trophy text-6xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-semibold">Belum ada data kursus</p>
                <p class="text-gray-400 text-sm mt-1">Kursus terpopuler akan muncul di sini</p>
            </div>
            @endif
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Aktivitas Terbaru</h3>
                <a href="{{ route('admin.transactions.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            @if($recentEnrollments->count() > 0)
            <div class="space-y-1">
                @foreach($recentEnrollments as $enrollment)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 px-3 -mx-3 rounded-lg transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-sm">{{ substr($enrollment->user->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $enrollment->user->name }}</p>
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-book-open mr-1"></i>
                                {{ Str::limit($enrollment->kursus->judul ?? $enrollment->kursus->title, 30) }}
                            </p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 flex-shrink-0">
                        {{ $enrollment->created_at->diffForHumans() }}
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-12">
                <i class="fas fa-history text-6xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-semibold">Belum ada aktivitas</p>
                <p class="text-gray-400 text-sm mt-1">Aktivitas enrollment akan muncul di sini</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.courses.create') }}" class="bg-white border-2 border-emerald-500 hover:bg-emerald-50 rounded-lg p-4 flex items-center gap-3 transition">
            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-plus text-emerald-600 text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Tambah Kursus Baru</p>
                <p class="text-xs text-gray-500">Buat kursus baru untuk platform</p>
            </div>
        </a>

        <a href="{{ route('admin.users.create') }}" class="bg-white border-2 border-blue-500 hover:bg-blue-50 rounded-lg p-4 flex items-center gap-3 transition">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-user-plus text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Tambah Pengguna</p>
                <p class="text-xs text-gray-500">Daftarkan pengguna baru</p>
            </div>
        </a>

        <a href="{{ route('admin.transactions.index') }}" class="bg-white border-2 border-purple-500 hover:bg-purple-50 rounded-lg p-4 flex items-center gap-3 transition">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-receipt text-purple-600 text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Kelola Transaksi</p>
                <p class="text-xs text-gray-500">Lihat semua transaksi</p>
            </div>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // Revenue Donut Chart
    // ==========================================
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const categoryData = @json($categoryData);
        
        if (categoryData.length > 0) {
            new Chart(revenueCtx, {
                type: 'doughnut',
                data: {
                    labels: categoryData.map(item => item.category),
                    datasets: [{
                        data: categoryData.map(item => item.percentage),
                        backgroundColor: ['#F59E0B', '#3B82F6', '#06B6D4', '#EF4444', '#10B981', '#8B5CF6'],
                        borderWidth: 0,
                        cutout: '70%',
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    const item = categoryData[context.dataIndex];
                                    return [
                                        context.label + ': ' + context.parsed + '%',
                                        'Total: Rp ' + new Intl.NumberFormat('id-ID').format(item.total)
                                    ];
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // ==========================================
    // Monthly Transactions Line Chart
    // ==========================================
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        const monthlyData = @json($monthlyData->values());
        
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Transaksi',
                    data: monthlyData,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10B981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#059669',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return 'Transaksi: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false }
                    },
                    x: {
                        grid: { display: false, drawBorder: false }
                    }
                }
            }
        });
    }

    // ==========================================
    // Gender Distribution Pie Chart
    // ==========================================
    const genderCtx = document.getElementById('genderChart');
    if (genderCtx) {
        const genderData = @json($genderData);
        
        if (genderData.length > 0) {
            new Chart(genderCtx, {
                type: 'pie',
                data: {
                    labels: genderData.map(item => item.gender),
                    datasets: [{
                        data: genderData.map(item => item.count),
                        backgroundColor: ['#3B82F6', '#EC4899', '#8B5CF6'],
                        borderWidth: 0,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    const item = genderData[context.dataIndex];
                                    return [
                                        context.label + ': ' + item.count + ' pengguna',
                                        'Persentase: ' + item.percentage + '%'
                                    ];
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // ==========================================
    // Profession Bar Chart
    // ==========================================
    const profesiCtx = document.getElementById('profesiChart');
    if (profesiCtx) {
        const profesiData = @json($profesiData);
        
        if (profesiData.length > 0) {
            new Chart(profesiCtx, {
                type: 'bar',
                data: {
                    labels: profesiData.map(item => item.profesi),
                    datasets: [{
                        label: 'Jumlah Pengguna',
                        data: profesiData.map(item => item.count),
                        backgroundColor: '#3B82F6',
                        borderRadius: 6,
                        hoverBackgroundColor: '#2563EB'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    return 'Jumlah: ' + context.parsed.x + ' pengguna';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        },
                        y: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush