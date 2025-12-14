<?php
    use App\Models\MaterialCompletion;
    use App\Models\Assignment;
    use App\Models\Submission;
    use Illuminate\Support\Facades\Auth;
?>

<?php $__env->startSection('title', 'Kursus Saya'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $courseProgressMap = [];
    $certificateEligibleCount = 0;
    $ongoingCount = 0;
    $completedCount = 0;

    foreach ($enrollments as $enrollment) {
        $course = $enrollment->kursus;
        $materiIds = $course->materi ? $course->materi->pluck('id')->toArray() : [];
        $materiCount = count($materiIds);
        $completedMaterialCount = (!empty($materiIds))
            ? MaterialCompletion::where('user_id', Auth::id())
                ->whereIn('materi_id', $materiIds)
                ->count()
            : 0;
        $progress = $materiCount > 0 ? round(($completedMaterialCount / $materiCount) * 100) : 0;
        $materialsCompleted = $materiCount > 0 && $completedMaterialCount >= $materiCount;

        // Final quiz status (assignment type exam yang sudah publish)
        $finalExam = Assignment::where('kursus_id', $course->id)
            ->where('type', 'exam')
            ->where('is_published', true)
            ->first();
        $finalExamScore = null;
        $finalExamPassed = false;
        if ($finalExam) {
            $latestFinal = Submission::where('assignment_id', $finalExam->id)
                ->where('user_id', Auth::id())
                ->latest()
                ->first();
            $finalExamScore = $latestFinal->percentage ?? $latestFinal->score;
            $finalExamPassed = $latestFinal && $finalExamScore !== null && $finalExamScore >= ($finalExam->passing_score ?? 70);
        }

        $isCompleted = $materialsCompleted && (!$finalExam || $finalExamPassed);

        $courseProgressMap[$course->id] = [
            'materiCount' => $materiCount,
            'completedCount' => $completedMaterialCount,
            'progress' => $progress,
            'isCompleted' => $isCompleted,
            'finalExamRequired' => (bool) $finalExam,
            'finalExamPassed' => $finalExamPassed,
            'finalExamScore' => $finalExamScore,
            'materialsCompleted' => $materialsCompleted,
        ];

        if ($isCompleted) {
            $completedCount++;
            if ($enrollment->sertifikat) {
                $certificateEligibleCount++;
            }
        } else {
            $ongoingCount++;
        }
    }
?>
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Header with Back Button -->
        <div class="mb-6">
            <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center text-gray-700 hover:text-gray-900 mb-4">
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
                Berlangsung <span class="ml-1 px-2 py-0.5 bg-white/20 rounded-full text-xs"><?php echo e($ongoingCount); ?></span>
            </button>
            <button class="filter-tab px-6 py-2 bg-white text-gray-700 rounded-full font-semibold whitespace-nowrap border border-gray-300 transition hover:bg-gray-50" data-filter="completed">
                Selesai <span class="ml-1 px-2 py-0.5 bg-gray-200 rounded-full text-xs"><?php echo e($completedCount); ?></span>
            </button>
        </div>

        <!-- Stats Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl p-4 text-white shadow-sm flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-book text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold"><?php echo e($enrollments->count()); ?></div>
                    <div class="text-sm opacity-90">Total Kursus</div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-blue-500 to-indigo-500 rounded-2xl p-4 text-white shadow-sm flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-certificate text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold"><?php echo e($certificateEligibleCount); ?></div>
                    <div class="text-sm opacity-90">Sertifikat Tersedia</div>
                </div>
            </div>
        </div>

        <?php if($enrollments->isEmpty()): ?>
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                <div class="max-w-sm mx-auto">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-graduation-cap text-gray-400 text-4xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Kursus</h2>
                    <p class="text-gray-600 mb-6">Mulai perjalanan belajar Anda dengan memilih kursus yang sesuai</p>
                    <a href="<?php echo e(route('courses.index')); ?>" 
                       class="inline-flex items-center px-6 py-3 bg-teal-700 text-white rounded-xl hover:bg-teal-800 transition font-semibold">
                        <i class="fas fa-search mr-2"></i>
                        Jelajahi Kursus
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Courses List -->
            <div class="space-y-4" id="coursesList">
                <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $course = $enrollment->kursus;
                        $judul = $course->judul ?? $course->title ?? 'Untitled';
                        $kategori = $course->kategori ?? 'General';
                        $progressData = $courseProgressMap[$course->id] ?? ['materiCount' => 0, 'completedCount' => 0, 'progress' => 0, 'isCompleted' => false];
                        $progress = $progressData['progress'];
                        $isCompleted = $progressData['isCompleted'];
                        $certificate = $enrollment->sertifikat;
                        $finalExamRequired = $progressData['finalExamRequired'] ?? false;
                        $finalExamPassed = $progressData['finalExamPassed'] ?? false;
                        $finalExamScore = $progressData['finalExamScore'] ?? null;
                        $materialsCompleted = $progressData['materialsCompleted'] ?? false;

                        if ($finalExamRequired && !$finalExamPassed && $progress >= 100) {
                            $progress = 95;
                        }
                    ?>

                    <div class="course-item bg-white rounded-2xl shadow-sm hover:shadow-md transition p-4"
                         data-status="<?php echo e($isCompleted ? 'completed' : 'ongoing'); ?>"
                         data-course-name="<?php echo e(strtolower($judul)); ?>">
                        
                        <div class="flex gap-4">
                            <!-- Course Thumbnail -->
                            <div class="flex-shrink-0">
                                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl overflow-hidden bg-gradient-to-br from-gray-700 to-gray-900">
                                    <?php if($course->image_url): ?>
                                        <img src="<?php echo e($course->image_url); ?>" 
                                             alt="<?php echo e($judul); ?>" 
                                             class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-graduation-cap text-white text-3xl opacity-50"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Course Info -->
                            <div class="flex-1 min-w-0">
                                <!-- Category Label -->
                                <div class="mb-2">
                                    <span class="inline-block px-3 py-1 text-xs font-bold rounded-full
                                        <?php echo e(in_array($kategori, ['Graphic Design', 'Digital Marketing']) ? 'bg-orange-100 text-orange-600' : ''); ?>

                                        <?php echo e(in_array($kategori, ['Web Development', 'Frontend', 'Backend']) ? 'bg-blue-100 text-blue-600' : ''); ?>

                                        <?php echo e($kategori === 'Programming' ? 'bg-purple-100 text-purple-600' : ''); ?>

                                        <?php echo e($kategori === 'business' ? 'bg-green-100 text-green-600' : ''); ?>

                                        <?php echo e(!in_array($kategori, ['Graphic Design', 'Digital Marketing', 'Web Development', 'Frontend', 'Backend', 'Programming', 'business']) ? 'bg-gray-100 text-gray-600' : ''); ?>">
                                        <?php echo e(ucwords($kategori)); ?>

                                    </span>
                                </div>

                                <!-- Course Title -->
                                <h3 class="font-bold text-gray-900 text-base mb-2 line-clamp-2">
                                    <?php echo e($judul); ?>

                                </h3>

                                <?php if($isCompleted): ?>
                                    <!-- Completed Status -->
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold flex items-center gap-1">
                                                <i class="fas fa-check-circle"></i>
                                                Selesai
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                <?php echo e($progressData['completedCount']); ?>/<?php echo e($progressData['materiCount']); ?> Materi
                                            </span>
                                        </div>
                                        
                                        <?php if($certificate): ?>
                                            <!-- Certificate Available -->
                                            <div class="flex items-center gap-2">
                                                <button onclick="viewCertificate(<?php echo e($enrollment->id); ?>)" 
                                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition shadow-sm">
                                                    <i class="fas fa-eye mr-2"></i>
                                                    Lihat Sertifikat
                                                </button>
                                                <a href="<?php echo e(route('student.certificate.download', $enrollment)); ?>" 
                                                   class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-semibold hover:bg-teal-700 transition shadow-sm">
                                                    <i class="fas fa-download mr-2"></i>
                                                    Unduh
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <!-- Certificate Processing -->
                                            <div class="flex items-center gap-2 text-sm text-amber-600 bg-amber-50 px-3 py-2 rounded-lg">
                                                <i class="fas fa-clock animate-pulse"></i>
                                                <span class="font-medium">Sertifikat sedang diproses...</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <!-- Progress Bar -->
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-1 bg-gray-200 rounded-full h-2.5">
                                                <div class="bg-gradient-to-r from-teal-500 to-emerald-500 h-2.5 rounded-full transition-all duration-300" 
                                                     style="width: <?php echo e($progress); ?>%"></div>
                                            </div>
                                            <span class="text-sm font-bold text-gray-700 min-w-[45px]"><?php echo e($progress); ?>%</span>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            <?php echo e($progressData['completedCount']); ?>/<?php echo e($progressData['materiCount']); ?> Materi Selesai
                                        </p>
                                        <?php if($finalExamRequired && !$finalExamPassed && $materialsCompleted): ?>
                                            <p class="text-xs text-amber-600 font-semibold">
                                                Final quiz belum lulus. Selesaikan untuk mendapatkan sertifikat.
                                            </p>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('student.course.learn', $course)); ?>" 
                                           class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-semibold hover:bg-teal-700 transition shadow-sm">
                                            <i class="fas fa-play mr-2"></i>
                                            <?php echo e($finalExamRequired && $materialsCompleted ? 'Kerjakan Final Quiz' : 'Lanjutkan Belajar'); ?>

                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Status Badge -->
                            <div class="flex items-start">
                                <?php if($isCompleted): ?>
                                    <span class="px-3 py-1 bg-green-500 text-white rounded-full text-xs font-semibold shadow-md">
                                        <i class="fas fa-check-circle mr-1"></i>Completed
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-blue-500 text-white rounded-full text-xs font-semibold shadow-md">
                                        <i class="fas fa-play-circle mr-1"></i>Active
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Empty State for Filtered Results -->
            <div id="emptyState" class="hidden bg-white rounded-2xl shadow-sm p-12 text-center">
                <div class="max-w-sm mx-auto">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Tidak Ada Kursus</h3>
                    <p class="text-gray-600 text-sm">Tidak ada kursus yang sesuai dengan filter yang dipilih</p>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- Modal Kursus Selesai -->
<div id="courseCompleteOverlay" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm p-8 text-center relative overflow-hidden">
        <div class="absolute -top-10 -left-6 w-24 h-24 bg-emerald-100 rounded-full opacity-60"></div>
        <div class="absolute -bottom-8 -right-4 w-28 h-28 bg-blue-100 rounded-full opacity-60"></div>
        <div class="relative z-10 space-y-4">
            <div class="w-20 h-20 mx-auto bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-graduation-cap text-white text-3xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900">Kursus Selesai</h3>
                <p class="text-sm text-gray-600 mt-1">Silakan unduh sertifikat Anda.</p>
            </div>
            <div class="flex flex-col gap-3">
                <a id="downloadCertificateBtn" href="#" class="hidden px-4 py-3 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700 transition">
                    Unduh Sertifikat
                </a>
                <button id="closeCompleteModal" class="px-4 py-3 bg-gray-100 text-gray-800 rounded-xl font-semibold hover:bg-gray-200 transition">
                    Kembali
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Certificate Modal -->
<div id="certificateModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-2xl">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">
                <i class="fas fa-certificate text-blue-600 mr-2"></i>
                Sertifikat Penyelesaian
            </h3>
            <button onclick="closeCertificateModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
            <div id="certificatePreview" class="flex items-center justify-center">
                <div class="animate-pulse text-gray-400">
                    <i class="fas fa-spinner fa-spin text-4xl"></i>
                </div>
            </div>
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

    .filter-tab.active {
        background-color: #0f766e;
        color: white;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .course-item {
        animation: slideIn 0.3s ease-out;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterTabs = document.querySelectorAll('.filter-tab');
    const courseItems = document.querySelectorAll('.course-item');
    const emptyState = document.getElementById('emptyState');
    const searchInput = document.getElementById('searchCourse');

    const applyFilter = (filter) => {
        filterTabs.forEach(t => {
            t.classList.remove('active', 'bg-teal-700', 'text-white');
            t.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-300');
            if (t.dataset.filter === filter) {
                t.classList.add('active', 'bg-teal-700', 'text-white');
                t.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-300');
            }
        });

        let visibleCount = 0;
        const searchTerm = searchInput.value.toLowerCase();

        courseItems.forEach(item => {
            const status = item.dataset.status;
            const courseName = item.dataset.courseName;
            const matchesFilter = status === filter;
            const matchesSearch = courseName.includes(searchTerm);
            
            if (matchesFilter && matchesSearch) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Show/hide empty state
        if (visibleCount === 0 && courseItems.length > 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    };

    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const filter = this.dataset.filter;
            applyFilter(filter);
        });
    });

    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const activeFilter = document.querySelector('.filter-tab.active').dataset.filter;
            applyFilter(activeFilter);
        });
    }

    // Set default filter
    const hasOngoing = Array.from(courseItems).some(item => item.dataset.status === 'ongoing');
    applyFilter(hasOngoing ? 'ongoing' : 'completed');

    // Pop-up kursus selesai (muncul jika ada kursus completed)
    const completedItem = document.querySelector('.course-item[data-status="completed"]');
    const overlay = document.getElementById('courseCompleteOverlay');
    const closeBtn = document.getElementById('closeCompleteModal');
    const downloadBtn = document.getElementById('downloadCertificateBtn');

    // Jangan tampilkan otomatis di halaman "My Courses" agar tidak mengganggu.
    // Jika suatu saat ingin memicu manual, bisa pakai query ?show_complete=1.
    const params = new URLSearchParams(window.location.search);
    const showComplete = params.has('show_complete');

    if (showComplete && completedItem && overlay) {
        const certBtn = completedItem.querySelector('a[href*="http"]');
        if (certBtn && downloadBtn) {
            downloadBtn.classList.remove('hidden');
            downloadBtn.setAttribute('href', certBtn.getAttribute('href'));
            downloadBtn.setAttribute('target', '_blank');
        }
        overlay.classList.remove('hidden');
    }

    closeBtn?.addEventListener('click', () => overlay.classList.add('hidden'));
});

// Certificate modal functions
function viewCertificate(enrollmentId) {
    const modal = document.getElementById('certificateModal');
    const preview = document.getElementById('certificatePreview');
    
    modal.classList.remove('hidden');
    
    // Fetch certificate
    fetch(`/student/enrollment/${enrollmentId}/certificate-preview`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const url = data.url || '';
                const isHtml = url.toLowerCase().endsWith('.html');
                if (isHtml) {
                    preview.innerHTML = `
                        <iframe src="${url}" class="w-full h-[70vh] rounded-lg border-4 border-blue-100 shadow-lg" title="Certificate"></iframe>
                    `;
                } else {
                    preview.innerHTML = `
                        <img src="${url}" 
                             alt="Certificate" 
                             class="max-w-full h-auto rounded-lg shadow-lg border-4 border-blue-100">
                    `;
                }
            } else {
                preview.innerHTML = `
                    <div class="text-center text-red-600">
                        <i class="fas fa-exclamation-circle text-4xl mb-3"></i>
                        <p class="font-semibold">Gagal memuat sertifikat</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            preview.innerHTML = `
                <div class="text-center text-red-600">
                    <i class="fas fa-exclamation-circle text-4xl mb-3"></i>
                    <p class="font-semibold">Terjadi kesalahan</p>
                </div>
            `;
        });
}

function closeCertificateModal() {
    const modal = document.getElementById('certificateModal');
    modal.classList.add('hidden');
}

// Close modal on backdrop click
document.getElementById('certificateModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeCertificateModal();
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/my-courses.blade.php ENDPATH**/ ?>