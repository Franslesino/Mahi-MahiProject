{{-- resources/views/student/learn.blade.php --}}
@extends('layouts.app')

@section('title', 'Belajar - ' . ($course->judul ?? $course->title))

@section('content')
@php
    $completedIds = $completedIds ?? [];
    $progressValue = $progress ?? 0;
    $flatMaterials = collect($sections ?? [])->flatMap(fn($section) => $section->materials);
    $nextIncomplete = $flatMaterials->first(fn($material) => !in_array($material->id, $completedIds));
    $nextIncompleteUrl = $nextIncomplete
        ? ($nextIncomplete->type === 'quiz'
            ? route('courses.materials.quiz', [$course, $nextIncomplete->id])
            : route('courses.materials.view', [$course, $nextIncomplete->id]))
        : null;
@endphp

<div class="min-h-screen bg-[#f4f2f0]">
    <div class="max-w-6xl mx-auto px-4 py-6">
        <style>
            @keyframes shimmer {
                0% { background-position: -200px 0; }
                100% { background-position: 200px 0; }
            }
            .skeleton-line {
                position: relative;
                overflow: hidden;
                background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
                background-size: 400px 100%;
                animation: shimmer 1.4s ease-in-out infinite;
            }
            .section-chevron {
                transition: transform 0.2s ease;
            }
            [data-loaded="false"] .loaded-content { display: none; }
            [data-loaded="true"] .loading-skeleton { display: none; }
        </style>
        <noscript>
            <style>
                [data-loaded="false"] .loaded-content { display: block; }
                [data-loaded="false"] .loading-skeleton { display: none; }
            </style>
        </noscript>

        <div class="grid grid-cols-1 md:grid-cols-2 items-center mb-6 px-4 md:px-8 lg:px-10 gap-4">
            <div class="relative flex items-center gap-3">
                <a id="learn-back-btn" href="{{ route('my-courses') }}" class="fixed top-24 md:top-20 z-50 inline-flex items-center gap-2 text-gray-700 hover:text-gray-900 transition bg-white px-3 py-2 rounded-md shadow-sm" aria-label="Kembali ke kursus">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                <div class="text-sm text-gray-700 font-semibold">{{ $course->judul ?? $course->title }}</div>
            </div>
            <div class="flex flex-col items-start md:items-end gap-2">
                <div class="flex items-center gap-3 text-sm text-gray-600">
                    <span>Progress</span>
                    @if($nextIncompleteUrl)
                        <a href="{{ $nextIncompleteUrl }}" class="inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-full hover:bg-emerald-100 transition">
                            <i class="fas fa-play"></i> Lanjut materi berikutnya
                        </a>
                    @else
                        <span class="inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-full">
                            <i class="fas fa-check-circle"></i> Semua materi selesai
                        </span>
                    @endif
                </div>
                <div class="w-full md:w-72 h-2.5 bg-gray-200/80 rounded-full overflow-hidden">
                    @php $clampedProgress = min(max($progressValue, 0), 100); @endphp
                    <div class="h-full bg-gradient-to-r from-emerald-500 to-emerald-600" style="width: {{ $clampedProgress }}%"></div>
                </div>
                <div class="text-xs text-gray-600">Selesai {{ $clampedProgress }}%</div>
            </div>
        </div>

        <div id="learn-shell" data-loaded="false" class="grid grid-cols-1 gap-6">
            <!-- Outline -->
            <div class="loading-skeleton space-y-4">
                <div class="bg-white rounded-2xl shadow p-5 space-y-4">
                    <div class="skeleton-line h-4 w-1/3 rounded"></div>
                    <div class="space-y-2">
                        <div class="skeleton-line h-3 w-5/6 rounded"></div>
                        <div class="skeleton-line h-3 w-3/4 rounded"></div>
                        <div class="skeleton-line h-3 w-2/3 rounded"></div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow p-5 space-y-3">
                    <div class="skeleton-line h-4 w-1/4 rounded"></div>
                    <div class="skeleton-line h-3 w-3/5 rounded"></div>
                </div>
            </div>

            <div class="loaded-content space-y-5">
                <div class="bg-white rounded-2xl shadow p-5">
                    @if($sections->isEmpty())
                        <div class="text-center py-12 text-gray-500">
                            Materi belum tersedia. <a href="{{ route('my-courses') }}" class="text-emerald-700 font-semibold hover:underline">Kembali ke kursus</a>
                        </div>
                    @else
                        <div class="space-y-5">
                            @php $index=1; @endphp
                            @foreach($sections as $section)
                                @php
                                    $materialCount = $section->materials->count();
                                    // Count completed materials including class_session with hadir attendance
                                    $completedCount = 0;
                                    foreach($section->materials as $mat) {
                                        if (in_array($mat->id, $completedIds)) {
                                            $completedCount++;
                                        } elseif ($mat->type === 'class_session') {
                                            // Check if student has hadir attendance
                                            $att = $mat->attendances()->where('user_id', auth()->id())->first();
                                            if ($att && $att->status === 'hadir') {
                                                $completedCount++;
                                            }
                                        }
                                    }
                                    $sectionId = 'section-'.$loop->index;
                                @endphp
                                <div class="space-y-2" data-section-container="{{ $sectionId }}">
                                    <button type="button"
                                            class="flex w-full items-center justify-between rounded-xl px-4 py-3 bg-gray-50 hover:bg-gray-100 transition section-toggle"
                                            data-target="{{ $sectionId }}"
                                            aria-expanded="true">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 section-chevron">&gt;</span>
                                            <span class="text-sm font-semibold text-gray-800">{{ $section->title }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500">
                                            <span>{{ $materialCount }} Materi</span>
                                            <span class="px-2 py-1 rounded-full bg-white border border-gray-200 text-emerald-700 font-semibold">{{ $completedCount }}/{{ $materialCount }} selesai</span>
                                        </div>
                                    </button>
                                    <div id="{{ $sectionId }}" class="rounded-xl border border-gray-200 divide-y divide-gray-100 bg-white section-body">
                                        @forelse($section->materials as $material)
                                            @php
                                                $isCompleted = !empty($completedIds) && in_array($material->id, $completedIds);
                                                $isActive = ($currentMaterial && $currentMaterial->id === $material->id);
                                                $isClassSession = $material->type === 'class_session';
                                                
                                                // Get attendance status for class_session
                                                $attendanceStatus = null;
                                                if ($isClassSession) {
                                                    $attendance = $material->attendances()->where('user_id', auth()->id())->first();
                                                    $attendanceStatus = $attendance ? $attendance->status : null;
                                                }
                                                
                                                $rowClass = 'w-full text-left px-4 py-3 flex items-center gap-3 transition rounded-xl border ';
                                                if ($isClassSession) {
                                                    $rowClass .= 'bg-orange-50 border-orange-100';
                                                } elseif ($isCompleted) {
                                                    $rowClass .= 'bg-emerald-50 border-emerald-100 text-emerald-900';
                                                } elseif ($isActive) {
                                                    $rowClass .= 'bg-white border-emerald-200 shadow-[0_8px_30px_rgba(16,185,129,0.15)] ring-1 ring-emerald-100';
                                                } else {
                                                    $rowClass .= 'hover:bg-gray-50 border-transparent';
                                                }
                                                $targetUrl = $material->type === 'quiz'
                                                    ? route('courses.materials.quiz', [$course, $material->id])
                                                    : route('courses.materials.view', [$course, $material->id]);
                                            @endphp
                                            
                                            @if($isClassSession)
                                                {{-- Special display for class session --}}
                                                @php
                                                    $isSessionComplete = $attendanceStatus === 'hadir';
                                                    $sessionClass = $isSessionComplete 
                                                        ? 'w-full text-left px-4 py-3 flex items-center gap-3 transition rounded-xl border bg-emerald-50 border-emerald-200'
                                                        : 'w-full text-left px-4 py-3 flex items-center gap-3 transition rounded-xl border bg-orange-50 border-orange-100';
                                                @endphp
                                                <div class="{{ $sessionClass }}">
                                                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $isSessionComplete ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700' }} font-semibold">
                                                        <i class="fas {{ $isSessionComplete ? 'fa-check-circle' : 'fa-users' }}"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $material->judul ?? $material->title }}</p>
                                                        <div class="flex flex-wrap gap-2 mt-1 text-xs text-gray-600">
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full {{ ($material->session_type ?? 'offline') === 'online' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                                                <i class="fas {{ ($material->session_type ?? 'offline') === 'online' ? 'fa-video' : 'fa-building' }}"></i>
                                                                {{ ucfirst($material->session_type ?? 'offline') }}
                                                            </span>
                                                            @if($material->session_date)
                                                                <span class="inline-flex items-center gap-1">
                                                                    <i class="fas fa-calendar-alt text-gray-400"></i>
                                                                    {{ $material->session_date->format('d M Y') }}
                                                                </span>
                                                            @endif
                                                            @if($material->session_start_time)
                                                                <span class="inline-flex items-center gap-1">
                                                                    <i class="fas fa-clock text-gray-400"></i>
                                                                    {{ \Carbon\Carbon::parse($material->session_start_time)->format('H:i') }}
                                                                    @if($material->session_end_time)
                                                                        - {{ \Carbon\Carbon::parse($material->session_end_time)->format('H:i') }}
                                                                    @endif
                                                                </span>
                                                            @endif
                                                            @if($material->session_location)
                                                                <span class="inline-flex items-center gap-1">
                                                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                                                    {{ Str::limit($material->session_location, 30) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if($material->session_meeting_link && ($material->session_type ?? 'offline') === 'online')
                                                            <a href="{{ $material->session_meeting_link }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline mt-1">
                                                                <i class="fas fa-external-link-alt"></i> Link Meeting
                                                            </a>
                                                        @endif
                                                    </div>
                                                    <div class="flex flex-col items-end gap-1">
                                                        <span class="text-xs text-orange-600 font-medium">Sesi Tatap Muka</span>
                                                        @if($attendanceStatus)
                                                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-full
                                                                {{ $attendanceStatus === 'hadir' ? 'bg-green-100 text-green-700' : '' }}
                                                                {{ $attendanceStatus === 'tidak_hadir' ? 'bg-red-100 text-red-700' : '' }}
                                                                {{ in_array($attendanceStatus, ['izin', 'sakit']) ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                                {{ $attendanceStatus === 'terlambat' ? 'bg-blue-100 text-blue-700' : '' }}">
                                                                @if($attendanceStatus === 'hadir')
                                                                    <i class="fas fa-check-circle"></i> Hadir
                                                                @elseif($attendanceStatus === 'tidak_hadir')
                                                                    <i class="fas fa-times-circle"></i> Tidak Hadir
                                                                @elseif($attendanceStatus === 'izin')
                                                                    <i class="fas fa-envelope"></i> Izin
                                                                @elseif($attendanceStatus === 'sakit')
                                                                    <i class="fas fa-medkit"></i> Sakit
                                                                @elseif($attendanceStatus === 'terlambat')
                                                                    <i class="fas fa-clock"></i> Terlambat
                                                                @endif
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs text-gray-500 bg-gray-100 rounded-full">
                                                                <i class="fas fa-hourglass-half"></i> Belum Tercatat
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                {{-- Regular material display --}}
                                                <a href="{{ $targetUrl }}"
                                                        class="{{ trim($rowClass) }}"
                                                        aria-current="{{ $isActive ? 'step' : 'false' }}">
                                                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100 text-gray-700 font-semibold">
                                                        {{ sprintf('%02d', $index) }}
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $material->judul ?? $material->title }}</p>
                                                        <p class="text-xs text-gray-500 truncate">
                                                            {{ $material->duration ? $material->duration . ' menit' : ' ' }}
                                                        </p>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs text-gray-400 capitalize">{{ $material->type }}</span>
                                                        @if(!empty($completedIds) && in_array($material->id, $completedIds))
                                                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-full border border-emerald-200">
                                                                <i class="fas fa-check-circle"></i> Selesai
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-50 text-gray-500 text-xs font-semibold rounded-full border border-gray-200">
                                                                <i class="far fa-circle"></i> Belum Selesai
                                                            </span>
                                                        @endif
                                                    </div>
                                                </a>
                                            @endif
                                            @php $index++; @endphp
                                        @empty
                                            <div class="px-4 py-3 text-sm text-gray-500">Belum ada materi.</div>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if(!empty($finalExam))
                    @php
                        $finalStatus = $finalExamStatus ?? [];
                        $finalPassed = $finalStatus['passed'] ?? false;
                        $finalScore = $finalStatus['score'] ?? null;
                        $finalLocked = empty($materialsComplete);
                    @endphp
                    <div class="bg-white rounded-2xl shadow p-5 mt-4 border border-gray-100">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm text-gray-500 font-semibold uppercase">Final Quiz</p>
                                <h3 class="text-lg font-bold text-gray-900">{{ $finalExam->title ?? 'Final Quiz' }}</h3>
                                <p class="text-xs text-gray-500 mt-1">Harus diselesaikan untuk mendapatkan sertifikat kursus.</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Passing grade</p>
                                <p class="text-xl font-bold text-emerald-700">{{ $finalExam->passing_score ?? 70 }}%</p>
                                @if($finalScore !== null)
                                    <p class="text-xs text-gray-500 mt-1">Skor terakhir: <span class="font-semibold">{{ $finalScore }}%</span></p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            @if($finalLocked)
                                <button class="px-4 py-2 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed" disabled>
                                    Selesaikan semua materi untuk membuka final quiz
                                </button>
                            @elseif($finalPassed)
                                <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg font-semibold">
                                    <i class="fas fa-check-circle"></i> Final quiz lulus
                                </span>
                                <p class="text-sm text-gray-600">Sertifikat akan tersedia setelah proses selesai.</p>
                            @else
                                <a href="{{ route('courses.final-quiz.show', $course) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-700 text-white rounded-lg hover:bg-emerald-800 transition font-semibold shadow">
                                    <i class="fas fa-flag-checkered"></i>
                                    {{ $finalScore !== null ? 'Ulangi Final Quiz' : 'Mulai Final Quiz' }}
                                </a>
                                @if(($finalExamStatus['canRetake'] ?? false) === false && $finalScore !== null)
                                    <span class="text-xs text-red-600">Batas percobaan tercapai.</span>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<script>
// Geser tombol back saat sidebar terbuka (kelas sidebar-open dari components/sidebar)
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('learn-back-btn');
    if (!btn) return;

    const updatePos = () => {
        const open = document.body.classList.contains('sidebar-open');
        let left = '3rem';
        if (window.innerWidth >= 1024) { // desktop
            left = open ? '22rem' : '3rem';
        } else if (window.innerWidth >= 768) { // tablet
            left = open ? '14rem' : '3rem';
        }
        btn.style.left = left;
    };

    updatePos();
    window.addEventListener('resize', updatePos);
    const observer = new MutationObserver(updatePos);
    observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });

    // Loading skeleton toggle
    document.querySelectorAll('[data-loaded]').forEach(el => el.setAttribute('data-loaded', 'true'));

    // Collapsible sections
    document.querySelectorAll('.section-toggle').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const targetId = toggle.dataset.target;
            if (!targetId) return;
            const body = document.getElementById(targetId);
            if (!body) return;
            const expanded = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', String(!expanded));
            body.classList.toggle('hidden', expanded);
            const chevron = toggle.querySelector('.section-chevron');
            if (chevron) {
                chevron.style.transform = expanded ? 'rotate(-90deg)' : 'rotate(0deg)';
            }
        });
    });

    // Tandai selesai (AJAX)
    document.querySelectorAll('.mark-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const url = btn.dataset.url;
            if (!url) return;

            btn.disabled = true;
            btn.textContent = 'Memproses...';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).then(async (res) => {
                if (!res.ok) throw new Error(await res.text());
                return res.json();
            }).then(data => {
                notify({ type: 'success', message: data.message || 'Materi ditandai selesai' });
                setTimeout(() => window.location.reload(), 600);
            }).catch(err => {
                console.error(err);
                notify({ type: 'error', message: 'Gagal menandai selesai. Coba lagi.' });
                btn.disabled = false;
                btn.textContent = 'Tandai selesai';
            });
        });
    });
});
</script>
@endsection
