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
            <h2 class="text-2xl font-bold text-gray-800 mt-2">{{ $course->judul }}</h2>
            <p class="text-gray-600 mt-1">{{ $course->deskripsi }}</p>
            <div class="flex items-center gap-3 mt-3">
                <span class="text-sm px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-medium">
                    {{ $course->kategori }}
                </span>
                <span class="text-sm px-3 py-1 rounded-full bg-purple-50 text-purple-700 font-medium">
                    {{ $course->mode }}
                </span>
                <span class="text-sm px-3 py-1 rounded-full font-medium {{ $course->status === 'active' ? 'bg-green-50 text-green-700' : ($course->status === 'draft' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-50 text-gray-700') }}">
                    {{ ucfirst($course->status) }}
                </span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('instructor.materials.create', $course) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center justify-center gap-2 whitespace-nowrap">
                <i class="fas fa-plus"></i>
                <span>Tambah Materi</span>
            </a>
            <a href="{{ route('instructor.assignments.create', ['course_id' => $course->id]) }}" 
               class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium flex items-center justify-center gap-2 whitespace-nowrap">
                <i class="fas fa-clipboard-list"></i>
                <span>Tambah Quiz/Assignment</span>
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
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
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Quiz & Assignment</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $assignments->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Peserta</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['totalStudents'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tag text-amber-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Harga</p>
                    @if($course->discount_price > 0)
                        <p class="text-xl font-bold text-green-600">Rp {{ number_format($course->discount_price, 0, ',', '.') }}</p>
                    @elseif($course->harga > 0)
                        <p class="text-xl font-bold text-gray-900">Rp {{ number_format($course->harga, 0, ',', '.') }}</p>
                    @else
                        <p class="text-xl font-bold text-blue-600">GRATIS</p>
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

    <!-- Assignments/Quiz Section -->
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-clipboard-list text-purple-600 mr-2"></i>
                    Quiz & Assignment ({{ $assignments->count() }})
                </h3>
                <a href="{{ route('instructor.assignments.create', ['course_id' => $course->id]) }}" 
                   class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-medium">
                    <i class="fas fa-plus mr-1"></i>
                    Tambah Quiz/Assignment
                </a>
            </div>

            @if($assignments->isEmpty())
                <div class="p-12 text-center">
                    <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Quiz/Assignment</h3>
                    <p class="text-gray-500 mb-6">Tambahkan quiz atau assignment untuk menguji pemahaman siswa</p>
                    <a href="{{ route('instructor.assignments.create', ['course_id' => $course->id]) }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                        <i class="fas fa-plus"></i>
                        <span>Buat Quiz/Assignment</span>
                    </a>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach($assignments as $assignment)
                        <div class="p-6 hover:bg-gray-50 transition">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 {{ $assignment->type === 'quiz' ? 'bg-blue-100' : ($assignment->type === 'exam' ? 'bg-red-100' : 'bg-purple-100') }}">
                                    @if($assignment->type === 'quiz')
                                        <i class="fas fa-question-circle {{ $assignment->type === 'quiz' ? 'text-blue-600' : ($assignment->type === 'exam' ? 'text-red-600' : 'text-purple-600') }} text-xl"></i>
                                    @elseif($assignment->type === 'exam')
                                        <i class="fas fa-file-alt {{ $assignment->type === 'quiz' ? 'text-blue-600' : ($assignment->type === 'exam' ? 'text-red-600' : 'text-purple-600') }} text-xl"></i>
                                    @else
                                        <i class="fas fa-clipboard-list {{ $assignment->type === 'quiz' ? 'text-blue-600' : ($assignment->type === 'exam' ? 'text-red-600' : 'text-purple-600') }} text-xl"></i>
                                    @endif
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $assignment->title }}</h4>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $assignment->type === 'quiz' ? 'bg-blue-100 text-blue-700' : ($assignment->type === 'exam' ? 'bg-red-100 text-red-700' : 'bg-purple-100 text-purple-700') }}">
                                            {{ ucfirst($assignment->type) }}
                                        </span>
                                        @if($assignment->is_published)
                                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                                Published
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">
                                                Draft
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if($assignment->description)
                                        <p class="text-gray-600 text-sm mb-3">{{ Str::limit($assignment->description, 150) }}</p>
                                    @endif
                                    
                                    <div class="flex items-center gap-4 text-sm text-gray-600">
                                        <span>
                                            <i class="fas fa-question-circle mr-1"></i>
                                            {{ $assignment->questions_count }} Soal
                                        </span>
                                        @if($assignment->duration_minutes)
                                            <span>
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $assignment->duration_minutes }} menit
                                            </span>
                                        @endif
                                        @if($assignment->passing_score)
                                            <span>
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Pass: {{ $assignment->passing_score }}%
                                            </span>
                                        @endif
                                        @if($assignment->due_date)
                                            <span>
                                                <i class="fas fa-calendar mr-1"></i>
                                                Deadline: {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <a href="{{ route('instructor.assignments.show', $assignment) }}" 
                                       class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('instructor.assignments.edit', $assignment) }}" 
                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('instructor.assignments.edit-questions', $assignment) }}" 
                                       class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition"
                                       title="Kelola Soal">
                                        <i class="fas fa-list"></i>
                                    </a>
                                    @if($assignment->is_published)
                                        <form action="{{ route('instructor.assignments.unpublish', $assignment) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Unpublish assignment ini?')">
                                            @csrf
                                            <button type="submit" 
                                                    class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition"
                                                    title="Unpublish">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('instructor.assignments.publish', $assignment) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Publish assignment ini?')">
                                            @csrf
                                            <button type="submit" 
                                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition"
                                                    title="Publish">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('instructor.assignments.destroy', $assignment) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus assignment ini?')">
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
            @endif
        </div>
    </div>
</div>
@endsection