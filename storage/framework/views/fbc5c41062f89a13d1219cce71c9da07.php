<?php $__env->startSection('content'); ?>
<div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Dashboard Admin</h2>
        <p class="text-gray-600 mt-1">Selamat datang kembali, <?php echo e(Auth::user()->name); ?>!</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Kursus -->
        <a href="<?php echo e(route('admin.courses.index')); ?>" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-blue-300 block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Kursus</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($totalCourses); ?></p>
                    <p class="text-xs text-green-600 mt-1 font-semibold">
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 text-emerald-700 rounded-full"><i class="fas fa-arrow-up"></i> <?php echo e($activeCourses); ?> aktif</span>
                    </p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-blue-600 text-2xl"></i>
                </div>
            </div>
        </a>

        <!-- Total Pengguna -->
        <a href="<?php echo e(route('admin.users.index')); ?>" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-emerald-300 block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Pengguna</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($totalUsers); ?></p>
                    <p class="text-xs text-gray-600 mt-1 font-semibold">
                        <?php echo e($totalStudents); ?> Students | <?php echo e($totalInstructors); ?> Instructors
                    </p>
                </div>
                <div class="w-14 h-14 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-emerald-600 text-2xl"></i>
                </div>
            </div>
        </a>

        <!-- Total Transaksi -->
        <a href="<?php echo e(route('admin.transactions.index')); ?>" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-purple-300 block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Transaksi</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($totalTransactions); ?></p>
                    <p class="text-xs text-yellow-600 mt-1 font-semibold">
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-50 text-yellow-700 rounded-full"><i class="fas fa-clock"></i> <?php echo e($pendingTransactions); ?> pending</span>
                    </p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-purple-600 text-2xl"></i>
                </div>
            </div>
        </a>

        <!-- Total Pendapatan -->
        <a href="<?php echo e(route('admin.transactions.index')); ?>" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-amber-300 block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Pendapatan</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        Rp <?php echo e(number_format($totalRevenue, 0, ',', '.')); ?>

                    </p>
                    <p class="text-xs text-green-600 mt-1 font-semibold">
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 text-emerald-700 rounded-full"><i class="fas fa-check-circle"></i> <?php echo e($paidTransactions); ?> paid</span>
                    </p>
                </div>
                <div class="w-14 h-14 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wallet text-amber-600 text-2xl"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Charts Row 1: Revenue & Transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue by Category Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6" data-chart-card="revenue">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Pendapatan per Kategori</h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    Total: Rp <?php echo e(number_format($categoryData->sum('total'), 0, ',', '.')); ?>

                </span>
            </div>
            
            <div class="loading-skeleton space-y-3 mb-4 hidden">
                <div class="h-6 w-40 bg-gray-200 rounded animate-pulse"></div>
                <div class="h-48 bg-gray-200 rounded-xl animate-pulse"></div>
            </div>
            <?php if($categoryData->count() > 0): ?>
            <div class="flex items-center justify-center mb-6 chart-body">
                <canvas id="revenueChart" width="250" height="250"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <?php
                    $colors = ['#F59E0B', '#3B82F6', '#06B6D4', '#EF4444', '#10B981', '#8B5CF6'];
                ?>
                <?php $__currentLoopData = $categoryData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: <?php echo e($colors[$index % 6]); ?>"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 truncate"><?php echo e($cat['category']); ?></p>
                        <p class="text-sm font-bold text-gray-800"><?php echo e($cat['percentage']); ?>%</p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <div class="flex flex-col items-center justify-center py-12">
                <i class="fas fa-chart-pie text-6xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-semibold">Belum ada data pendapatan</p>
                <p class="text-gray-400 text-sm mt-1">Data akan muncul setelah ada transaksi paid</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Monthly Transactions Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6" data-chart-card="monthly">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Transaksi Bulanan <?php echo e(now()->year); ?></h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    Total: <?php echo e($monthlyData->sum()); ?> transaksi
                </span>
            </div>
            
            <div class="loading-skeleton space-y-3 mb-4 hidden">
                <div class="h-6 w-40 bg-gray-200 rounded animate-pulse"></div>
                <div class="h-48 bg-gray-200 rounded-xl animate-pulse"></div>
            </div>

            <?php if($monthlyData->sum() > 0): ?>
            <div class="flex items-center gap-2 mb-4 chart-toggle">
                <div class="flex rounded-lg border border-gray-200 overflow-hidden text-xs">
                    <button id="chartTypeLine" class="px-3 py-1.5 bg-gray-100 text-gray-800 font-semibold" aria-pressed="true" aria-label="Tampilkan grafik garis">Line</button>
                    <button id="chartTypeBar" class="px-3 py-1.5 text-gray-600 hover:bg-gray-100" aria-pressed="false" aria-label="Tampilkan grafik batang">Bar</button>
                </div>
            </div>

            <div style="height: 250px;" class="chart-body">
                <canvas id="monthlyChart"></canvas>
            </div>
            <?php else: ?>
            <div class="flex flex-col items-center justify-center py-12 text-center chart-empty">
                <i class="fas fa-chart-line text-5xl text-gray-300 mb-3"></i>
                <p class="text-gray-600 font-semibold">Belum ada transaksi bulan ini</p>
                <p class="text-gray-400 text-sm">Grafik akan tampil otomatis setelah ada transaksi paid.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Charts Row 2: Gender & Profession -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Gender Distribution Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6" data-chart-card="gender">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Distribusi Gender Pengguna</h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    Total: <?php echo e($genderData->sum('count')); ?> pengguna
                </span>
            </div>
            
            <div class="loading-skeleton space-y-3 mb-4 hidden">
                <div class="h-6 w-40 bg-gray-200 rounded animate-pulse"></div>
                <div class="h-48 bg-gray-200 rounded-xl animate-pulse"></div>
            </div>
            <?php if($genderData->count() > 0): ?>
            <div class="flex items-center justify-center mb-6 chart-body">
                <canvas id="genderChart" width="250" height="250"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <?php
                    $genderColors = ['#3B82F6', '#EC4899', '#8B5CF6'];
                ?>
                <?php $__currentLoopData = $genderData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $gender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: <?php echo e($genderColors[$index % 3]); ?>"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500"><?php echo e($gender['gender']); ?></p>
                        <p class="text-sm font-bold text-gray-800"><?php echo e($gender['count']); ?> (<?php echo e($gender['percentage']); ?>%)</p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <div class="flex flex-col items-center justify-center py-12">
                <i class="fas fa-users text-6xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-semibold">Belum ada data gender</p>
                <p class="text-gray-400 text-sm mt-1">Data akan muncul setelah pengguna mengisi profil</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Profession Distribution Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6" data-chart-card="profesi">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Profesi Pengguna</h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    Total: <?php echo e($profesiData->sum('count')); ?> pengguna
                </span>
            </div>
            
            <div class="loading-skeleton space-y-3 mb-4 hidden">
                <div class="h-6 w-40 bg-gray-200 rounded animate-pulse"></div>
                <div class="h-48 bg-gray-200 rounded-xl animate-pulse"></div>
            </div>
            <?php if($profesiData->count() > 0): ?>
            <div style="height: 300px;" class="chart-body">
                <canvas id="profesiChart"></canvas>
            </div>
            <?php else: ?>
            <div class="flex flex-col items-center justify-center py-12">
                <i class="fas a-briefcase text-6l text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-semibold">Belum ada data profesi</p>
                <p class="text-gray-400 text-sm mt-1">Data akan muncul setelah pengguna mengisi profil</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bottom Row: Top Courses & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Courses -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Kursus Terpopuler</h3>
                <a href="<?php echo e(route('admin.courses.index')); ?>" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <?php if($topCourses->count() > 0): ?>
            <div class="space-y-4">
                <?php $__currentLoopData = $topCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between group hover:bg-gray-50 p-3 rounded-lg transition">
                    <div class="flex items-center gap-3 flex-1">
                        <div class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-sm"><?php echo e($index + 1); ?></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate group-hover:text-emerald-600 transition">
                                <?php echo e($course['title']); ?>

                            </p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-emerald-500 h-2 rounded-full transition-all" style="width: <?php echo e($course['percentage']); ?>%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-600 ml-4"><?php echo e($course['count']); ?></span>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <div class="flex flex-col items-center justify-center py-12">
                <i class="fas fa-trophy text-6xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-semibold">Belum ada data kursus</p>
                <p class="text-gray-400 text-sm mt-1">Kursus terpopuler akan muncul di sini</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Aktivitas Terbaru</h3>
                <a href="<?php echo e(route('admin.transactions.index')); ?>" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <?php if($recentEnrollments->count() > 0): ?>
            <div class="space-y-1">
                <?php $__currentLoopData = $recentEnrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 px-3 -mx-3 rounded-lg transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-sm"><?php echo e(substr($enrollment->user->name, 0, 1)); ?></span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800"><?php echo e($enrollment->user->name); ?></p>
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-book-open mr-1"></i>
                                <?php echo e(Str::limit($enrollment->kursus->judul ?? $enrollment->kursus->title, 30)); ?>

                            </p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 flex-shrink-0">
                        <?php echo e($enrollment->created_at->diffForHumans()); ?>

                    </span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <div class="flex flex-col items-center justify-center py-12">
                <i class="fas fa-history text-6xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-semibold">Belum ada aktivitas</p>
                <p class="text-gray-400 text-sm mt-1">Aktivitas enrollment akan muncul di sini</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="<?php echo e(route('admin.courses.create')); ?>" class="bg-white border-2 border-emerald-500 hover:bg-emerald-50 rounded-lg p-4 flex items-center gap-3 transition">
            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-plus text-emerald-600 text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Tambah Kursus Baru</p>
                <p class="text-xs text-gray-500">Buat kursus baru untuk platform</p>
            </div>
        </a>

        <a href="<?php echo e(route('admin.users.create')); ?>" class="bg-white border-2 border-blue-500 hover:bg-blue-50 rounded-lg p-4 flex items-center gap-3 transition">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-user-plus text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Tambah Pengguna</p>
                <p class="text-xs text-gray-500">Daftarkan pengguna baru</p>
            </div>
        </a>

        <a href="<?php echo e(route('admin.transactions.index')); ?>" class="bg-white border-2 border-purple-500 hover:bg-purple-50 rounded-lg p-4 flex items-center gap-3 transition">
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<!-- Chart.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // Revenue Donut Chart
    // ==========================================
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const categoryData = <?php echo json_encode($categoryData, 15, 512) ?>;
        
        if (categoryData.length > 0) {
            const revenueCard = document.querySelector('[data-chart-card=\"revenue\"]');
            revenueCard?.querySelector('.loading-skeleton')?.classList.add('hidden');
            revenueCard?.querySelector('.chart-body')?.classList.remove('hidden');

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
    // Monthly Transactions Line/Bar Chart
    // ==========================================
    const monthlyCtx = document.getElementById('monthlyChart');
        if (monthlyCtx) {
        const monthlyData = <?php echo json_encode($monthlyData->values(), 15, 512) ?>;
        const monthlySum = monthlyData.reduce((a,b)=>a+b,0);
        const monthlyCard = document.querySelector('[data-chart-card=\"monthly\"]');
        if (monthlySum === 0) {
            monthlyCard?.querySelector('.loading-skeleton')?.classList.add('hidden');
        } else {
            monthlyCard?.querySelector('.loading-skeleton')?.classList.add('hidden');
            monthlyCard?.querySelector('.chart-body')?.classList.remove('hidden');
            monthlyCard?.querySelector('.chart-toggle')?.classList.remove('hidden');

            const monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            let monthlyChartInstance = null;

            const buildMonthlyChart = (type = 'line') => {
                if (monthlyChartInstance) monthlyChartInstance.destroy();
                const isLine = type === 'line';
                monthlyChartInstance = new Chart(monthlyCtx, {
                    type,
                    data: {
                        labels: monthlyLabels,
                        datasets: [{
                            label: 'Transaksi',
                            data: monthlyData,
                            borderColor: '#10B981',
                            backgroundColor: isLine ? 'rgba(16, 185, 129, 0.1)' : '#10B981',
                            tension: isLine ? 0.4 : 0,
                            fill: isLine,
                            borderWidth: 2,
                            borderRadius: isLine ? 0 : 6,
                            pointBackgroundColor: '#10B981',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: isLine ? 5 : 3,
                            pointHoverRadius: isLine ? 7 : 5,
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
            };

            const setChartToggleState = (type) => {
                const btnLine = document.getElementById('chartTypeLine');
                const btnBar = document.getElementById('chartTypeBar');
                if (btnLine && btnBar) {
                    if (type === 'line') {
                        btnLine.classList.add('bg-gray-100', 'text-gray-800', 'font-semibold');
                        btnLine.setAttribute('aria-pressed', 'true');
                        btnBar.classList.remove('bg-gray-100', 'text-gray-800', 'font-semibold');
                        btnBar.classList.add('text-gray-600');
                        btnBar.setAttribute('aria-pressed', 'false');
                    } else {
                        btnBar.classList.add('bg-gray-100', 'text-gray-800', 'font-semibold');
                        btnBar.setAttribute('aria-pressed', 'true');
                        btnLine.classList.remove('bg-gray-100', 'text-gray-800', 'font-semibold');
                        btnLine.classList.add('text-gray-600');
                        btnLine.setAttribute('aria-pressed', 'false');
                    }
                }
            };

            buildMonthlyChart('line');
            setChartToggleState('line');
            document.getElementById('chartTypeLine')?.addEventListener('click', () => {
                setChartToggleState('line');
                buildMonthlyChart('line');
            });
            document.getElementById('chartTypeBar')?.addEventListener('click', () => {
                setChartToggleState('bar');
                buildMonthlyChart('bar');
            });
        }
    }

    // ==========================================
    // Gender Distribution Pie Chart
    // ==========================================
    const genderCtx = document.getElementById('genderChart');
    if (genderCtx) {
        const genderData = <?php echo json_encode($genderData, 15, 512) ?>;
        
        if (genderData.length > 0) {
            const genderCard = document.querySelector('[data-chart-card=\"gender\"]');
            genderCard?.querySelector('.loading-skeleton')?.classList.add('hidden');
            genderCard?.querySelector('.chart-body')?.classList.remove('hidden');

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
        const profesiData = <?php echo json_encode($profesiData, 15, 512) ?>;
        
        if (profesiData.length > 0) {
            const profesiCard = document.querySelector('[data-chart-card=\"profesi\"]');
            profesiCard?.querySelector('.loading-skeleton')?.classList.add('hidden');
            profesiCard?.querySelector('.chart-body')?.classList.remove('hidden');

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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>