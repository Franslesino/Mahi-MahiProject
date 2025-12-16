@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.courses.detail', $course) }}" 
               class="text-blue-600 hover:text-blue-800 transition">
                <i class="fas fa-arrow-left"></i> Kembali ke Kursus
            </a>
        </div>
        <h1 class="text-3xl font-bold text-gray-800">{{ $course->judul }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Material Preview Card (Main Content) -->
        <div class="lg:col-span-2">
            <!-- Material Preview Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-8 text-white">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        @if($material->type === 'video')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm">
                                <i class="fas fa-video mr-1.5"></i> Video
                            </span>
                        @elseif($material->type === 'document')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm">
                                <i class="fas fa-file-alt mr-1.5"></i> Dokumen
                            </span>
                        @elseif($material->type === 'quiz')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm">
                                <i class="fas fa-tasks mr-1.5"></i> Quiz
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm">
                                <i class="fas fa-book mr-1.5"></i> Teks
                            </span>
                        @endif
                        
                        @if($material->is_preview)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/30 backdrop-blur-sm">
                                <i class="fas fa-eye mr-1.5"></i> Preview Tersedia
                            </span>
                        @endif
                        
                        @if($material->status_terkunci)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-500/30 backdrop-blur-sm">
                                <i class="fas fa-lock mr-1.5"></i> Terkunci
                            </span>
                        @endif
                    </div>
                    <h2 class="text-2xl font-bold mb-2">{{ $material->judul }}</h2>
                    @if($material->description)
                        <p class="text-white/90 text-sm">{{ $material->description }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            @if($material->type === 'video')
                @if($material->video_url)
                    <div class="aspect-video bg-black rounded-lg overflow-hidden mb-4">
                        @if(str_contains($material->video_url, 'youtube.com') || str_contains($material->video_url, 'youtu.be'))
                            @php
                                $videoId = null;
                                if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $material->video_url, $matches)) {
                                    $videoId = $matches[1];
                                } elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $material->video_url, $matches)) {
                                    $videoId = $matches[1];
                                }
                            @endphp
                            @if($videoId)
                                <iframe 
                                    class="w-full h-full"
                                    src="https://www.youtube.com/embed/{{ $videoId }}" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            @else
                                <div class="flex items-center justify-center h-full">
                                    <p class="text-white">Format video YouTube tidak valid</p>
                                </div>
                            @endif
                        @else
                            <video controls class="w-full h-full">
                                <source src="{{ $material->video_url }}" type="video/mp4">
                                Browser Anda tidak mendukung video.
                            </video>
                        @endif
                    </div>
                @elseif($material->file_path)
                    <div class="aspect-video bg-black rounded-lg overflow-hidden mb-4">
                        <video controls class="w-full h-full">
                            <source src="{{ $material->file_path }}" type="video/mp4">
                            Browser Anda tidak mendukung video.
                        </video>
                    </div>
                @else
                    <div class="bg-gray-100 rounded-lg p-8 text-center">
                        <i class="fas fa-video text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600">Belum ada video yang diunggah</p>
                    </div>
                @endif
            
            @elseif($material->type === 'document')
                @if($material->file_path)
                    <div class="bg-gray-50 rounded-lg p-6 mb-4">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-pdf text-5xl text-red-500"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800 mb-1">{{ $material->judul }}</h3>
                                <p class="text-sm text-gray-600 mb-3">Dokumen pembelajaran</p>
                                <a href="{{ $material->file_path }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                    <i class="fas fa-download mr-2"></i>
                                    Download Dokumen
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- PDF Preview (if applicable) -->
                    @if(str_ends_with(strtolower($material->file_path), '.pdf'))
                        <div class="border rounded-lg overflow-hidden" style="height: 600px;">
                            <iframe src="{{ $material->file_path }}" class="w-full h-full" frameborder="0"></iframe>
                        </div>
                    @endif
                @else
                    <div class="bg-gray-100 rounded-lg p-8 text-center">
                        <i class="fas fa-file-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600">Belum ada dokumen yang diunggah</p>
                    </div>
                @endif
            
            @elseif($material->type === 'quiz')
                @if($material->assignment)
                    <div class="space-y-4">
                        <!-- Quiz Info -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-blue-600 text-xl mt-0.5"></i>
                                <div>
                                    <h4 class="font-semibold text-blue-900 mb-1">Informasi Quiz</h4>
                                    <ul class="text-sm text-blue-800 space-y-1">
                                        <li><i class="fas fa-clock mr-2"></i>Durasi: {{ $material->assignment->duration_minutes ?? 'Tidak dibatasi' }} menit</li>
                                        <li><i class="fas fa-check-circle mr-2"></i>Nilai Kelulusan: {{ $material->assignment->passing_score ?? 70 }}%</li>
                                        <li><i class="fas fa-question-circle mr-2"></i>Total Soal: {{ $material->assignment->questions->count() }} soal</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Quiz Questions Preview -->
                        @if($material->assignment->questions->count() > 0)
                            <div class="border rounded-lg p-6">
                                <h3 class="text-lg font-semibold mb-4 flex items-center">
                                    <i class="fas fa-list-ol mr-2 text-blue-600"></i>
                                    Daftar Soal
                                </h3>
                                <div class="space-y-6">
                                    @foreach($material->assignment->questions as $index => $question)
                                        <div class="border-l-4 border-blue-500 pl-4 py-2">
                                            <div class="flex items-start gap-3">
                                                <span class="flex-shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">
                                                    {{ $index + 1 }}
                                                </span>
                                                <div class="flex-1">
                                                    <p class="text-gray-800 mb-3">{{ $question->question_text }}</p>
                                                    @if($question->options->count() > 0)
                                                        <div class="space-y-2">
                                                            @foreach($question->options as $option)
                                                                <div class="flex items-center gap-2 text-sm {{ $option->is_correct ? 'text-green-700 font-semibold' : 'text-gray-600' }}">
                                                                    <i class="fas {{ $option->is_correct ? 'fa-check-circle text-green-500' : 'fa-circle text-gray-400' }}"></i>
                                                                    <span>{{ $option->option_text }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                                    <p class="text-yellow-800">Quiz ini belum memiliki soal</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-gray-100 rounded-lg p-8 text-center">
                        <i class="fas fa-tasks text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600">Quiz belum dikonfigurasi</p>
                    </div>
                @endif
            
            @else
                <!-- Text Content -->
                <div class="prose max-w-none">
                    @if($material->description)
                        <div class="text-gray-700 leading-relaxed">
                            {!! nl2br(e($material->description)) !!}
                        </div>
                    @else
                        <div class="bg-gray-100 rounded-lg p-8 text-center">
                            <i class="fas fa-file-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="text-gray-600">Belum ada konten</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Footer Actions -->
        <div class="bg-gray-50 px-6 py-4 border-t">
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.courses.materials.edit', [$course, $material]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
            </div>
        </div>
    </div>
        </div>

        <!-- Sidebar: Daftar Materi -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden sticky top-4">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-4 py-4">
                    <h3 class="font-bold text-white">Daftar Materi</h3>
                </div>
                
                <div class="p-4">
                    @php
                        $sections = $course->sections()->with('materials')->orderBy('order')->get();
                    @endphp

                    @foreach($sections as $courseSection)
                        <!-- Section Title -->
                        <div class="mb-3">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">{{ $courseSection->title }}</h4>
                            
                            <!-- Materials List -->
                            <div class="space-y-1">
                                @foreach($courseSection->materials as $mat)
                                    <a href="{{ route('admin.courses.materials.preview', [$course, $mat]) }}" 
                                       class="block px-3 py-2.5 rounded-lg transition {{ $mat->id === $material->id ? 'bg-teal-50 border-l-4 border-teal-500' : 'hover:bg-gray-50' }}">
                                        <div class="flex items-start gap-2">
                                            <!-- Icon -->
                                            <div class="flex-shrink-0 mt-0.5">
                                                @if($mat->type === 'video')
                                                    <i class="fas fa-play-circle text-blue-500"></i>
                                                @elseif($mat->type === 'quiz')
                                                    <i class="fas fa-question-circle text-yellow-500"></i>
                                                @elseif($mat->type === 'document' || $mat->type === 'pdf')
                                                    <i class="fas fa-file-pdf text-red-500"></i>
                                                @else
                                                    <i class="fas fa-book text-gray-500"></i>
                                                @endif
                                            </div>
                                            
                                            <!-- Title -->
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate {{ $mat->id === $material->id ? 'text-teal-700' : '' }}">
                                                    {{ $mat->judul }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ ucfirst($mat->type) }}
                                                </p>
                                            </div>

                                            <!-- Active Indicator -->
                                            @if($mat->id === $material->id)
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-check-circle text-teal-500"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
