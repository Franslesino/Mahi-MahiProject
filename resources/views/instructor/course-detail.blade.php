@extends('layouts.instructor')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex-1">
            <a href="{{ route('instructor.courses') }}" class="text-blue-600 hover:text-blue-700 mb-2 inline-flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Daftar Kursus</span>
            </a>
            <h2 class="text-2xl font-bold text-gray-800 mt-2">{{ $course->title }}</h2>
            <p class="text-gray-600 mt-1">{{ $course->description }}</p>
            <div class="flex items-center gap-3 mt-3">
                <span class="text-sm px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-medium">
                    {{ $course->category }}
                </span>
                <span class="text-sm px-3 py-1 rounded-full bg-purple-50 text-purple-700 font-medium">
                    {{ $course->mode }}
                </span>
                <span class="text-sm px-3 py-1 rounded-full font-medium {{ $course->status === 'active' ? 'bg-green-50 text-green-700' : ($course->status === 'draft' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-50 text-gray-700') }}">
                    {{ ucfirst($course->status) }}
                </span>
            </div>
        </div>
        <a href="{{ route('instructor.materials.create', $course) }}" 
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center justify-center gap-2 whitespace-nowrap">
            <i class="fas fa-plus"></i>
            <span>Tambah Materi</span>
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Materi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $materials->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-video text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Video</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $course->videos }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tag text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Harga</p>
                    @if($course->discount_price)
                        <p class="text-xl font-bold text-green-600">Rp {{ number_format($course->discount_price, 0, ',', '.') }}</p>
                    @else
                        <p class="text-xl font-bold text-gray-900">Rp {{ number_format($course->price, 0, ',', '.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Materials List -->
    @if($materials->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <i class="fas fa-folder-open text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Materi</h3>
            <p class="text-gray-500 mb-6">Mulai menambahkan materi untuk kursus ini</p>
            <a href="{{ route('instructor.materials.create', $course) }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                <i class="fas fa-plus"></i>
                <span>Tambah Materi Pertama</span>
            </a>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Daftar Materi</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($materials as $material)
                    <div class="p-6 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        @if($material->file_url)
                                            @php
                                                $extension = pathinfo($material->file_url, PATHINFO_EXTENSION);
                                            @endphp
                                            @if(in_array($extension, ['pdf']))
                                                <i class="fas fa-file-pdf text-red-600"></i>
                                            @elseif(in_array($extension, ['mp4', 'avi', 'mov']))
                                                <i class="fas fa-video text-blue-600"></i>
                                            @elseif(in_array($extension, ['jpg', 'jpeg', 'png']))
                                                <i class="fas fa-image text-green-600"></i>
                                            @else
                                                <i class="fas fa-file text-gray-600"></i>
                                            @endif
                                        @else
                                            @if($material->type === 'video')
                                                <i class="fas fa-video text-blue-600"></i>
                                            @elseif($material->type === 'pdf')
                                                <i class="fas fa-file-pdf text-red-600"></i>
                                            @elseif($material->type === 'quiz')
                                                <i class="fas fa-question-circle text-purple-600"></i>
                                            @else
                                                <i class="fas fa-align-left text-blue-600"></i>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $material->title }}</h4>
                                            <span class="text-xs px-2 py-1 rounded-full bg-{{ $material->status === 'published' ? 'green' : 'yellow' }}-100 text-{{ $material->status === 'published' ? 'green' : 'yellow' }}-700">
                                                {{ ucfirst($material->type) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500">
                                            {{ $material->created_at->diffForHumans() }}
                                            @if($material->duration)
                                                • {{ $material->duration }} menit
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                @if($material->description)
                                    <p class="text-gray-600 ml-[52px] mb-3">{{ $material->description }}</p>
                                @endif
                                @if($material->file_url)
                                    <div class="ml-[52px]">
                                        <a href="{{ Storage::url($material->file_url) }}" 
                                           target="_blank"
                                           class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 text-sm">
                                            <i class="fas fa-download"></i>
                                            <span>Download File</span>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('instructor.materials.edit', [$course, $material]) }}" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('instructor.materials.destroy', [$course, $material]) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection