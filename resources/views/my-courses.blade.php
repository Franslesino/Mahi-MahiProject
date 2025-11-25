{{-- resources/views/student/my-courses.blade.php --}}
@extends('layouts.app')

@php
    use App\Models\MaterialCompletion;
    use Illuminate\Support\Facades\Auth;
@endphp

@section('title', 'Kursus Saya')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Header with Back Button -->
        <div class="mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center text-gray-700 hover:text-gray-900 mb-4">
                <i class="fas fa-arrow-left mr-2"></i>
                <span class="font-semibold">Kursus Saya</span>
            </a>

            <!-- Search Bar -->
            <div class="relative">
                <input type="text" 
                       id="searchCourse"
                       placeholder="Cari Kursus" 
                       class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                <button class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-search text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex gap-3 mb-6 overflow-x-auto pb-2">
            <button class="filter-tab active px-6 py-2 bg-teal-700 text-white rounded-full font-semibold whitespace-nowrap transition hover:bg-teal-800" data-filter="ongoing">
                Berlangsung
            </button>
            <button class="filter-tab px-6 py-2 bg-white text-gray-700 rounded-full font-semibold whitespace-nowrap border border-gray-300 transition hover:bg-gray-50" data-filter="completed">
                Selesai
            </button>
        </div>

        <!-- Stats Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl p-4 text-white shadow-sm flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-book text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold">{{ $enrollments->count() }}</div>
                    <div class="text-sm opacity-90">Total Kursus</div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-blue-500 to-indigo-500 rounded-2xl p-4 text-white shadow-sm flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-certificate text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold">0</div>
                    <div class="text-sm opacity-90">Sertifikat</div>
                </div>
            </div>
        </div>

        @if($enrollments->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                <div class="max-w-sm mx-auto">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-graduation-cap text-gray-400 text-4xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Kursus</h2>
                    <p class="text-gray-600 mb-6">Mulai perjalanan belajar Anda dengan memilih kursus yang sesuai</p>
                    <a href="{{ route('courses.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-teal-700 text-white rounded-xl hover:bg-teal-800 transition font-semibold">
                        <i class="fas fa-search mr-2"></i>
                        Jelajahi Kursus
                    </a>
                </div>
            </div>
        @else
            <!-- Courses List -->
            <div class="space-y-4" id="coursesList">
                @foreach($enrollments as $enrollment)
                    @php
                        $course = $enrollment->kursus;
                        $judul = $course->judul ?? $course->title ?? 'Untitled';
                        $kategori = $course->kategori ?? 'General';
                        $materiCount = $course->materi_count ?? ($course->materi->count() ?? 0);

                        // Hitung progres berdasarkan materi yang sudah selesai
                        $materiIds = $course->materi ? $course->materi->pluck('id')->toArray() : [];
                        $completedCount = (!empty($materiIds))
                            ? MaterialCompletion::where('user_id', Auth::id())
                                ->whereIn('materi_id', $materiIds)
                                ->count()
                            : 0;
                        $progress = $materiCount > 0 ? round(($completedCount / $materiCount) * 100) : 0;
                        $isCompleted = $progress >= 100;
                    @endphp

                    <a href="{{ route('student.course.learn', $course) }}"
                       class="course-item block bg-white rounded-2xl shadow-sm hover:shadow-md transition p-4"
                       data-status="{{ $isCompleted ? 'completed' : 'ongoing' }}"
                       data-course-name="{{ strtolower($judul) }}">
                        
                        <div class="flex gap-4">
                            <!-- Course Thumbnail -->
                            <div class="flex-shrink-0">
                                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl overflow-hidden bg-gradient-to-br from-gray-700 to-gray-900">
                                    @if($course->image)
                                        <img src="{{ asset('storage/' . $course->image) }}" 
                                             alt="{{ $judul }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-graduation-cap text-white text-3xl opacity-50"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Course Info -->
                            <div class="flex-1 min-w-0">
                                <!-- Category Label -->
                                <div class="mb-2">
                                    <span class="inline-block px-3 py-1 text-xs font-bold rounded-full
                                        {{ $kategori === 'Graphic Design' ? 'bg-orange-100 text-orange-600' : '' }}
                                        {{ $kategori === 'Digital Marketing' ? 'bg-orange-100 text-orange-600' : '' }}
                                        {{ $kategori === 'Web Development' ? 'bg-blue-100 text-blue-600' : '' }}
                                        {{ $kategori === 'Programming' ? 'bg-purple-100 text-purple-600' : '' }}
                                        {{ $kategori === 'business' ? 'bg-green-100 text-green-600' : '' }}
                                        {{ !in_array($kategori, ['Graphic Design', 'Digital Marketing', 'Web Development', 'Programming', 'business']) ? 'bg-gray-100 text-gray-600' : '' }}">
                                        {{ ucwords($kategori) }}
                                    </span>
                                </div>

                                <!-- Course Title -->
                                <h3 class="font-bold text-gray-900 text-base mb-1 line-clamp-2">
                                    {{ $judul }}
                                </h3>

                                @if($isCompleted)
                                    <button class="inline-flex items-center text-sm font-semibold text-teal-700 hover:text-teal-800 transition">
                                        <i class="fas fa-download mr-1"></i>
                                        UNDUH SERTIFIKAT
                                    </button>
                                @else
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                            <div class="bg-teal-600 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-600">{{ $progress }}%</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Status Indicator -->
                            <div class="flex items-start">
                                <span class="px-3 py-1 bg-green-500 text-white rounded-full text-xs font-semibold shadow-md">Active</span>
                            </div>
                        </div>

                    </a>
                @endforeach
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 gap-4 mt-8"></div>
        @endif

    </div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .filter-tab.active {
        background-color: #0f766e;
        color: white;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterTabs = document.querySelectorAll('.filter-tab');
    const courseItems = document.querySelectorAll('.course-item');

    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Update active state
            filterTabs.forEach(t => {
                t.classList.remove('active', 'bg-teal-700', 'text-white');
                t.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-300');
            });
            
            this.classList.add('active', 'bg-teal-700', 'text-white');
            this.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-300');

            // Filter courses
                    const filter = this.dataset.filter;
                    courseItems.forEach(item => {
                        const status = item.dataset.status;
                        item.style.display = (status === filter) ? 'block' : 'none';
                    });
            });
        });

    // Set default filter to ongoing on load
    courseItems.forEach(item => {
        item.style.display = item.dataset.status === 'ongoing' ? 'block' : 'none';
    });

    // Search functionality
    const searchInput = document.getElementById('searchCourse');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            courseItems.forEach(item => {
                const courseName = item.dataset.courseName;
                const matches = courseName.includes(searchTerm);
                item.style.display = matches ? 'block' : 'none';
            });
        });
    }
});
</script>
@endsection
