@extends('layouts.admin')

@section('content')
<div class="px-8 pt-6 pb-8 space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.courses.index') }}" 
               class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Kursus</h1>
                <p class="text-gray-600 mt-1">Informasi lengkap kursus</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.materials.index', $course) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-book-open mr-2"></i>
                Kelola Materi
            </a>
            <a href="{{ route('admin.courses.edit', $course) }}"
               class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                <i class="fas fa-edit mr-2"></i>
                Edit Kursus
            </a>
        </div>
    </div>

    {{-- STATISTICS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Peserta</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_enrollments'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Materi</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_materials'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Assignment</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_assignments'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Peserta Aktif</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['active_students'] }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- LEFT COLUMN - COURSE INFO --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- COURSE DETAIL CARD --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                @if($course->image && Storage::disk('public')->exists($course->image))
                    <img src="{{ Storage::url($course->image) }}" 
                         class="w-full h-64 object-cover"
                         alt="{{ $course->judul }}">
                @else
                    <div class="w-full h-64 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-book text-white text-6xl opacity-50"></i>
                    </div>
                @endif

                <div class="p-6 space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                @if($course->badge)
                                    <span class="px-2.5 py-0.5 bg-{{ $course->badge_color }}-100 text-{{ $course->badge_color }}-800 text-xs font-medium rounded-full">
                                        {{ $course->badge }}
                                    </span>
                                @endif
                                <span class="px-2.5 py-0.5 bg-gray-100 text-gray-700 text-xs rounded-full">
                                    {{ $course->kategori }}
                                </span>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $course->judul }}</h2>
                        </div>
                        
                        @if($course->status === 'active')
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">Active</span>
                        @elseif($course->status === 'draft')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm rounded-full">Draft</span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full">Inactive</span>
                        @endif
                    </div>

                    <p class="text-gray-700 leading-relaxed">{{ $course->deskripsi }}</p>

                    {{-- PRICE --}}
                    <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
                        <div>
                            <p class="text-sm text-gray-600">Harga Normal</p>
                            <p class="text-2xl font-bold text-gray-900">
                                Rp {{ number_format($course->harga, 0, ',', '.') }}
                            </p>
                        </div>
                        @if($course->discount_price > 0)
                            <div>
                                <p class="text-sm text-gray-600">Harga Diskon</p>
                                <p class="text-2xl font-bold text-green-600">
                                    Rp {{ number_format($course->discount_price, 0, ',', '.') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- LEARNING OUTCOMES --}}
                    @if($course->learning)
                        <div class="pt-4 border-t border-gray-200">
                            <h3 class="font-semibold text-gray-900 mb-2">Yang Akan Dipelajari:</h3>
                            <p class="text-gray-700">{{ $course->learning }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- MATERIALS LIST --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-book text-blue-600 mr-2"></i>
                        Materi Kursus ({{ $course->materi->count() }})
                    </h3>
                    <a href="{{ route('admin.courses.materials.index', $course) }}"
                       class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Kelola Materi →
                    </a>
                </div>

                <div class="p-6">
                    @forelse($course->materi as $index => $materi)
                        <div class="flex items-start gap-4 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div class="w-8 h-8 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center flex-shrink-0 font-semibold text-sm">
                                {{ $materi->urutan }}
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $materi->judul }}</h4>
                                @if($materi->isi)
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($materi->isi, 100) }}</p>
                                @endif
                                @if($materi->url_konten)
                                    <div class="flex items-center gap-2 mt-2">
                                        @if(Str::contains($materi->url_konten, ['youtube.com', 'youtu.be']))
                                            <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded">
                                                <i class="fab fa-youtube mr-1"></i>Video
                                            </span>
                                        @elseif(Str::endsWith($materi->url_konten, '.pdf'))
                                            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">
                                                <i class="fas fa-file-pdf mr-1"></i>PDF
                                            </span>
                                        @else
                                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded">
                                                <i class="fas fa-link mr-1"></i>Link
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            @if($materi->status_terkunci)
                                <i class="fas fa-lock text-gray-400"></i>
                            @else
                                <i class="fas fa-lock-open text-green-500"></i>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-book-open text-4xl mb-3"></i>
                            <p>Belum ada materi</p>
                            <a href="{{ route('admin.courses.materials.create', $course) }}"
                               class="text-blue-600 hover:text-blue-700 text-sm mt-2 inline-block">
                                Tambah Materi Pertama →
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ASSIGNMENTS LIST --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-clipboard-list text-purple-600 mr-2"></i>
                        Assignment & Quiz ({{ $assignments->count() }})
                    </h3>
                    <a href="{{ route('admin.assignments.create') }}?course_id={{ $course->id }}"
                       class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                        Tambah Assignment →
                    </a>
                </div>

                <div class="p-6">
                    @forelse($assignments as $assignment)
                        <div class="flex items-start gap-4 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                @if($assignment->type === 'quiz')
                                    <i class="fas fa-question-circle text-purple-600 text-xl"></i>
                                @elseif($assignment->type === 'exam')
                                    <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                                @else
                                    <i class="fas fa-clipboard-list text-purple-600 text-xl"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-semibold text-gray-900">{{ $assignment->title }}</h4>
                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded-full">
                                        {{ ucfirst($assignment->type) }}
                                    </span>
                                    @if($assignment->is_published)
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">
                                            Published
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded-full">
                                            Draft
                                        </span>
                                    @endif
                                </div>
                                @if($assignment->description)
                                    <p class="text-sm text-gray-600 mb-2">{{ Str::limit($assignment->description, 100) }}</p>
                                @endif
                                <div class="flex items-center gap-4 text-sm text-gray-600">
                                    <span>
                                        <i class="fas fa-question-circle mr-1"></i>
                                        {{ $assignment->questions_count }} Soal
                                    </span>
                                    <span>
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $assignment->duration }} menit
                                    </span>
                                    @if($assignment->passing_score)
                                        <span>
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Pass: {{ $assignment->passing_score }}%
                                        </span>
                                    @endif
                                    @if($assignment->deadline)
                                        <span>
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.assignments.show', $assignment) }}"
                                   class="text-gray-600 hover:text-gray-800"
                                   title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.assignments.edit', $assignment) }}"
                                   class="text-blue-600 hover:text-blue-800"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-clipboard-list text-4xl mb-3"></i>
                            <p>Belum ada assignment</p>
                            <a href="{{ route('admin.assignments.create') }}?course_id={{ $course->id }}"
                               class="text-purple-600 hover:text-purple-700 text-sm mt-2 inline-block">
                                Tambah Assignment Pertama →
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN - INSTRUCTOR & STUDENTS --}}
        <div class="space-y-6">
            
            {{-- INSTRUCTOR INFO --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-chalkboard-teacher text-emerald-600 mr-2"></i>
                    Instruktur
                </h3>
                
                @if($course->instructor)
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold">{{ strtoupper(substr($course->instructor->name, 0, 2)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900">{{ $course->instructor->name }}</p>
                            <p class="text-sm text-gray-600 truncate">{{ $course->instructor->email }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Belum ada instruktur</p>
                @endif
            </div>

            {{-- COURSE INFO --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Informasi Kursus
                </h3>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Mode</span>
                        <span class="font-medium text-gray-900">{{ $course->mode }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Status</span>
                        <span class="font-medium text-gray-900">
                            @if($course->status === 'active')
                                <span class="text-green-600">Active</span>
                            @elseif($course->status === 'draft')
                                <span class="text-yellow-600">Draft</span>
                            @else
                                <span class="text-gray-600">Inactive</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Rating</span>
                        <span class="font-medium text-gray-900">
                            <i class="fas fa-star text-yellow-400"></i>
                            {{ number_format($course->rating, 1) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Dibuat</span>
                        <span class="font-medium text-gray-900">{{ $course->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-600">Diupdate</span>
                        <span class="font-medium text-gray-900">{{ $course->updated_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- ENROLLED STUDENTS --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-users text-blue-600 mr-2"></i>
                    Peserta Terdaftar ({{ $course->enrollments->count() }})
                </h3>
                
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @forelse($course->enrollments->take(10) as $enrollment)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="font-semibold text-sm">{{ strtoupper(substr($enrollment->user->name, 0, 2)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 text-sm truncate">{{ $enrollment->user->name }}</p>
                                <p class="text-xs text-gray-600">{{ $enrollment->created_at->diffForHumans() }}</p>
                            </div>
                            @if($enrollment->status === 'active')
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-500">
                            <i class="fas fa-user-slash text-3xl mb-2"></i>
                            <p class="text-sm">Belum ada peserta</p>
                        </div>
                    @endforelse

                    @if($course->enrollments->count() > 10)
                        <p class="text-center text-sm text-blue-600 pt-2">
                            +{{ $course->enrollments->count() - 10 }} peserta lainnya
                        </p>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
