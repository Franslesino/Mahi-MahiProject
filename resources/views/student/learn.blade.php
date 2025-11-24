{{-- resources/views/student/learn.blade.php --}}
@extends('layouts.app')

@section('title', 'Belajar - ' . ($course->judul ?? $course->title))

@section('content')
<div class="bg-gray-900 min-h-screen">
    
    <!-- Top Navigation Bar -->
    <div class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('my-courses') }}" 
                       class="text-gray-400 hover:text-white transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        <span class="hidden sm:inline">Kembali</span>
                    </a>
                    <div class="hidden sm:block h-6 w-px bg-gray-700"></div>
                    <h1 class="text-base sm:text-lg font-semibold text-white truncate max-w-md">
                        {{ $course->judul ?? $course->title }}
                    </h1>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs sm:text-sm text-gray-400">
                        Progress: <span class="text-white font-semibold">0%</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-4 min-h-screen">
            
            <!-- Sidebar: Materials List -->
            <div class="lg:col-span-1 bg-gray-800 border-r border-gray-700 overflow-y-auto" style="max-height: calc(100vh - 73px);">
                <div class="p-4">
                    <h2 class="text-base sm:text-lg font-bold text-white mb-4 flex items-center">
                        <i class="fas fa-list-ul mr-2"></i>
                        Konten Kursus
                    </h2>
                    
                    @if($materials->isEmpty())
                        <div class="text-center py-12">
                            <i class="fas fa-inbox text-gray-600 text-4xl mb-3"></i>
                            <p class="text-gray-500 text-sm">Materi belum tersedia</p>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach($materials as $index => $material)
                                <div class="bg-gray-700 rounded-lg hover:bg-gray-600 transition cursor-pointer group" 
                                     onclick="loadMaterial({{ $material->id }})">
                                    <div class="p-3">
                                        <div class="flex items-start gap-3">
                                            <!-- Icon -->
                                            <div class="w-8 h-8 rounded flex items-center justify-center flex-shrink-0 {{ 
                                                $material->type === 'video' ? 'bg-red-500' : 
                                                ($material->type === 'pdf' ? 'bg-blue-500' : 
                                                ($material->type === 'quiz' ? 'bg-purple-500' : 'bg-gray-500'))
                                            }}">
                                                <i class="fas fa-{{ 
                                                    $material->type === 'video' ? 'play' : 
                                                    ($material->type === 'pdf' ? 'file-pdf' : 
                                                    ($material->type === 'quiz' ? 'question' : 'align-left'))
                                                }} text-white text-sm"></i>
                                            </div>

                                            <!-- Content -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-xs text-gray-400 font-semibold">{{ $index + 1 }}</span>
                                                    <h4 class="font-medium text-white text-sm group-hover:text-teal-300 transition line-clamp-2">
                                                        {{ $material->title }}
                                                    </h4>
                                                </div>
                                                <div class="flex items-center gap-3 text-xs text-gray-400">
                                                    @if($material->duration)
                                                        <span>
                                                            <i class="fas fa-clock mr-1"></i>{{ $material->duration }} menit
                                                        </span>
                                                    @endif
                                                    <span class="capitalize">{{ $material->type }}</span>
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-circle text-gray-600 text-xs"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Main Content: Video/Material Player -->
            <div class="lg:col-span-3 bg-gray-900">
                <div class="h-full flex flex-col">
                    
                    <!-- Video/Content Area -->
                    <div class="flex-1 flex items-center justify-center bg-black">
                        @if($currentMaterial)
                            <div id="material-content" class="w-full h-full">
                                @if($currentMaterial->type === 'video')
                                    <!-- Video Player -->
                                    <div class="w-full h-full flex items-center justify-center">
                                        @if($currentMaterial->video_url)
                                            <video class="w-full h-auto max-h-full" controls>
                                                <source src="{{ $currentMaterial->video_url }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        @else
                                            <div class="text-center text-gray-400">
                                                <i class="fas fa-video text-6xl mb-4"></i>
                                                <p>Video belum tersedia</p>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($currentMaterial->type === 'pdf')
                                    <!-- PDF Viewer -->
                                    <div class="w-full h-full flex items-center justify-center">
                                        @if($currentMaterial->file_path)
                                            <iframe src="{{ asset('storage/' . $currentMaterial->file_path) }}" 
                                                    class="w-full h-full" 
                                                    frameborder="0"></iframe>
                                        @else
                                            <div class="text-center text-gray-400">
                                                <i class="fas fa-file-pdf text-6xl mb-4"></i>
                                                <p>PDF belum tersedia</p>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <!-- Text/Other Content -->
                                    <div class="w-full h-full flex items-center justify-center p-8">
                                        <div class="max-w-4xl text-white">
                                            <h2 class="text-2xl font-bold mb-4">{{ $currentMaterial->title }}</h2>
                                            @if($currentMaterial->description)
                                                <p class="text-gray-300">{{ $currentMaterial->description }}</p>
                                            @else
                                                <p class="text-gray-400">Konten belum tersedia</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <!-- No Material Selected -->
                            <div class="text-center text-gray-400">
                                <i class="fas fa-book-open text-6xl mb-4"></i>
                                <h3 class="text-xl font-semibold mb-2">Selamat Datang!</h3>
                                <p>Pilih materi dari sidebar untuk mulai belajar</p>
                            </div>
                        @endif
                    </div>

                    <!-- Material Info Bar -->
                    @if($currentMaterial)
                        <div class="bg-gray-800 border-t border-gray-700 p-4">
                            <div class="max-w-6xl mx-auto">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold text-white mb-1 truncate">
                                            {{ $currentMaterial->title }}
                                        </h3>
                                        @if($currentMaterial->description)
                                            <p class="text-sm text-gray-400 truncate">
                                                {{ $currentMaterial->description }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                                            <i class="fas fa-chevron-left mr-2"></i>
                                            <span class="hidden sm:inline">Sebelumnya</span>
                                        </button>
                                        <button class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">
                                            <span class="hidden sm:inline">Selanjutnya</span>
                                            <i class="fas fa-chevron-right ml-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

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
</style>

<script>
function loadMaterial(materialId) {
    // TODO: Implement AJAX to load material content dynamically
    console.log('Loading material:', materialId);
    
    // For now, just reload the page with the material ID
    // You can enhance this with AJAX later
    window.location.href = `{{ route('student.course.learn', $course) }}?material=${materialId}`;
}
</script>
@endsection