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

            <div class="rounded-xl border border-gray-200 bg-black overflow-hidden min-h-[400px] flex items-center justify-center">
                @if($material->type === 'video')
                    @php
                        $videoSrc = $material->video_url ?? $material->file_url_full ?? $material->url_konten;
                        $mime = 'video/mp4';
                        if ($videoSrc && \Illuminate\Support\Str::endsWith(strtolower($videoSrc), ['.mov'])) {
                            $mime = 'video/quicktime';
                        }
                    @endphp
                    @if($videoSrc)
                        <video class="w-full h-full" controls>
                            <source src="{{ $videoSrc }}" type="{{ $mime }}">
                            Browser tidak mendukung video.
                        </video>
                    @else
                        <p class="text-gray-400">Video belum tersedia</p>
                    @endif
                @elseif($material->type === 'pdf')
                    @php $pdfSrc = $material->file_url_full ?? $material->url_konten; @endphp
                    @if($pdfSrc)
                        <iframe src="{{ $pdfSrc }}" class="w-full h-full" frameborder="0"></iframe>
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
@endsection
