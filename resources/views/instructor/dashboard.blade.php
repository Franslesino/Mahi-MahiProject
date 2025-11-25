@extends('layouts.instructor')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Dashboard Instruktur</h2>
        <p class="text-gray-600 mt-2">Selamat datang kembali, {{ auth()->user()->name }}!</p>
    </div>

   
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Kursus -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Kursus</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['totalCourses'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Kursus Aktif -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Kursus Aktif</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['activeCourses'] }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Siswa -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Siswa</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['totalStudents'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Materi -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Materi</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['totalMaterials'] }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Card (Full Width) -->
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white mb-8">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-emerald-100 text-sm mb-1">Total Pendapatan</p>
                <h3 class="text-3xl font-bold">Rp {{ number_format($stats['totalRevenue'], 0, ',', '.') }}</h3>
                <p class="text-emerald-100 text-sm mt-2">Dari semua kursus Anda</p>
            </div>
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                <i class="fas fa-wallet text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Charts & Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Course Performance -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                    Performa Kursus
                </h3>
            </div>

            @if($courseStats->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-chart-bar text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-500">Belum ada data performa kursus</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($courseStats->take(5) as $course)
                    <a href="{{ route('instructor.courses.show', $course) }}"
                       class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition border border-transparent hover:border-blue-100">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-graduation-cap text-blue-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-800 mb-1 truncate">{{ $course->title ?? $course->judul }}</h4>
                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span><i class="fas fa-users mr-1"></i>{{ $course->students_count ?? 0 }} siswa</span>
                                    <span><i class="fas fa-file-alt mr-1"></i>{{ $course->materials_count }} materi</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-4">
                            <p class="text-lg font-bold text-emerald-600">Rp {{ number_format($course->total_revenue ?? 0, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500">Revenue</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">
                <i class="fas fa-bell text-blue-500 mr-2"></i>
                Aktivitas Terbaru
            </h3>
            
            @if($recentEnrollments->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-bell-slash text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500 text-sm">Belum ada aktivitas</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($recentEnrollments as $enrollment)
                    <div class="flex items-start gap-3 pb-4 border-b border-gray-100 last:border-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm font-bold">{{ substr($enrollment->user->name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
    <p class="text-sm font-medium text-gray-800">{{ $enrollment->user->name }}</p>
    {{-- ✅ PERBAIKAN: Cek dulu apakah kursus ada --}}
    <p class="text-xs text-gray-500 truncate">
        Mendaftar {{ $enrollment->kursus ? Str::limit($enrollment->kursus->judul, 25) : 'Kursus tidak ditemukan' }}
    </p>
    <p class="text-xs text-gray-400 mt-1">
        <i class="fas fa-clock mr-1"></i>
        {{ $enrollment->created_at->diffForHumans() }}
    </p>
</div>
                        @if($enrollment->status === 'paid' || $enrollment->status === 'completed')
                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full font-medium flex-shrink-0">Lunas</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
