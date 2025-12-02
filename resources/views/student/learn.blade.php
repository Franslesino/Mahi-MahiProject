{{-- resources/views/student/learn.blade.php --}}
@extends('layouts.app')

@section('title', 'Belajar - ' . ($course->judul ?? $course->title))

@section('content')
<div class="min-h-screen bg-[#f4f2f0]">
    <div class="max-w-6xl mx-auto px-4 py-6">
        <div class="grid grid-cols-2 items-center mb-6 px-4 md:px-8 lg:px-10">
            <div class="relative">
                <a id="learn-back-btn" href="{{ route('my-courses') }}" class="fixed top-24 md:top-20 z-50 inline-flex items-center gap-2 text-gray-700 hover:text-gray-900 transition bg-white px-3 py-2 rounded-md shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
            <div class="text-sm text-gray-600 text-right">
                Progress: <span class="font-semibold text-gray-800">{{ $progress ?? 0 }}%</span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- Outline -->
            <div>
                <div class="bg-white rounded-2xl shadow p-5">
                    @if($sections->isEmpty())
                        <div class="text-center py-12 text-gray-500">
                            Materi belum tersedia.
                        </div>
                    @else
                        <div class="space-y-5">
                            @php $index=1; @endphp
                            @foreach($sections as $section)
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-sm text-gray-600 font-semibold">
                                        <span>{{ $section->title }}</span>
                                        <span>{{ $section->materials->count() }} Materi</span>
                                    </div>
                                    <div class="rounded-xl border border-gray-200 divide-y divide-gray-100 bg-white">
                                        @forelse($section->materials as $material)
                                            @php
                                                $isCompleted = !empty($completedIds) && in_array($material->id, $completedIds);
                                                $isActive = ($currentMaterial && $currentMaterial->id === $material->id);
                                                $rowClass = 'w-full text-left px-4 py-3 flex items-center gap-3 hover:bg-gray-50 transition ';
                                                if ($isCompleted) {
                                                    $rowClass .= 'bg-gray-100 ';
                                                } elseif ($isActive) {
                                                    $rowClass .= 'bg-gray-50 ';
                                                }
                                                $targetUrl = $material->type === 'quiz'
                                                    ? route('courses.materials.quiz', [$course, $material->id])
                                                    : route('courses.materials.view', [$course, $material->id]);
                                            @endphp
                                            <a href="{{ $targetUrl }}"
                                                    class="{{ trim($rowClass) }}">
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
                                                        <i class="fas fa-check-circle text-emerald-500"></i>
                                                    @endif
                                                </div>
                                            </a>
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
});
</script>
@endsection
