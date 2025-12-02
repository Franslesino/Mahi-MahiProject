@extends('layouts.app')

@section('title', $material->judul ?? $material->title)

@section('content')
<div class="min-h-screen bg-[#f4f2f0] py-8">
    <div class="max-w-5xl mx-auto px-4 space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('student.course.learn', $course) }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900 transition bg-white px-3 py-2 rounded-md shadow-sm">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke daftar materi</span>
            </a>
            @if($completed)
                <span class="text-sm text-green-700 font-semibold">Selesai</span>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow p-6">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1">{{ ucfirst($material->type) }}</p>
                    <h2 class="text-2xl font-semibold text-gray-900">{{ $material->judul ?? $material->title }}</h2>
                    @if($material->description)
                        <p class="text-sm text-gray-600 mt-1">{{ $material->description }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @php
                        $downloadUrl = $material->file_url_full ?? $material->url_konten;
                    @endphp
                    @if(in_array($material->type, ['video', 'pdf']) && $downloadUrl)
                        <div class="flex items-center gap-2">
                            <button type="button"
                                    class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-semibold"
                                    onclick="toggleFullscreen('#materialViewer')">
                                <i class="fas fa-expand mr-2"></i>Fullscreen
                            </button>
                            <a href="{{ $downloadUrl }}"
                               download
                               class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-semibold">
                                <i class="fas fa-download mr-2"></i>Download
                            </a>
                        </div>
                    @endif
                    @if(!$completed)
                        <form action="{{ route('courses.materials.complete', [$course, $material->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                                Tandai Selesai
                            </button>
                        </form>
                    @else
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">Sudah Selesai</span>
                    @endif
                </div>
            </div>

            <div id="materialViewer" class="rounded-xl border border-gray-200 bg-black overflow-hidden min-h-[400px] flex items-center justify-center relative">
                @if($material->type === 'video')
                    @php
                        $videoSrc = $material->video_url ?? $material->file_url_full ?? $material->url_konten;
                        $mime = 'video/mp4';
                        if ($videoSrc && \Illuminate\Support\Str::endsWith(strtolower($videoSrc), ['.mov'])) {
                            $mime = 'video/quicktime';
                        }
                    @endphp
                    @if($videoSrc)
                        <video class="w-full h-full" controls playsinline>
                            <source src="{{ $videoSrc }}" type="{{ $mime }}">
                            Browser tidak mendukung video.
                        </video>
                    @else
                        <p class="text-gray-400">Video belum tersedia</p>
                    @endif
                @elseif($material->type === 'pdf')
                    @php $pdfSrc = $material->file_url_full ?? $material->url_konten; @endphp
                    @if($pdfSrc)
                        <iframe id="pdfFrame" src="{{ $pdfSrc }}" class="w-full h-full" frameborder="0"></iframe>
                    @else
                        <p class="text-gray-400">PDF belum tersedia</p>
                    @endif
                @elseif($material->type === 'quiz')
                    <div class="flex-1 flex items-center justify-center">
                        <a href="{{ route('courses.materials.quiz', [$course, $material->id]) }}" class="px-6 py-3 bg-emerald-700 text-white rounded-lg hover:bg-emerald-800 transition shadow">
                            Mulai Quiz
                        </a>
                    </div>
                @else
                    <div class="p-6 text-gray-800 leading-relaxed w-full bg-white min-h-[300px]">
                        {!! nl2br(e($material->content ?? $material->description ?? 'Konten belum tersedia')) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function toggleFullscreen(selector) {
        const el = document.querySelector(selector);
        if (!el) return;

        if (!document.fullscreenElement) {
            if (el.requestFullscreen) {
                el.requestFullscreen();
            } else if (el.webkitRequestFullscreen) {
                el.webkitRequestFullscreen();
            } else if (el.msRequestFullscreen) {
                el.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }
    }
</script>
@endsection
