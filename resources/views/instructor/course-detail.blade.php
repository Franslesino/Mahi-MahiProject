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
        </div>
        <div class="flex gap-3">
            <button onclick="openSectionModal()" 
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium flex items-center gap-2">
                <i class="fas fa-folder-plus"></i>
                <span>Tambah Modul</span>
            </button>
            <button onclick="openMaterialModal()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Tambah Materi</span>
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-folder text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Modul</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sections->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Materi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalMaterials }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-video text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Video</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $course->videos }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-orange-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Siswa</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $course->enrollments_count ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

   

    <!-- Modul Kursus -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Modul Kursus</h3>
            <button onclick="toggleAllSections()" class="text-sm text-blue-600 hover:text-blue-700">
                <i class="fas fa-expand-alt mr-1"></i> Toggle Semua
            </button>
        </div>

        @if($sections->isEmpty())
            <div class="p-12 text-center">
                <i class="fas fa-folder-open text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Modul</h3>
                <p class="text-gray-500 mb-6">Mulai dengan menambahkan modul/section untuk kursus ini</p>
                <button onclick="openSectionModal()" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                    <i class="fas fa-folder-plus"></i>
                    <span>Tambah Modul Pertama</span>
                </button>
            </div>
        @else
            <div class="divide-y divide-gray-200">
                @foreach($sections as $section)
                    <div class="section-container" data-section-id="{{ $section->id }}">
                        <!-- Section Header -->
                        <div class="p-4 hover:bg-gray-50 cursor-pointer flex items-center justify-between" 
                             onclick="toggleSection({{ $section->id }})">
                            <div class="flex items-center gap-3 flex-1">
                                <button class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 transition">
                                    <i class="fas fa-chevron-right section-chevron transition-transform" id="chevron-{{ $section->id }}"></i>
                                </button>
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $section->title }}</h4>
                                    @if($section->description)
                                        <p class="text-sm text-gray-600">{{ $section->description }}</p>
                                    @endif
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ $section->materials->count() }} materi
                                </span>
                            </div>
                            <div class="flex items-center gap-2 ml-4" onclick="event.stopPropagation()">
                                <button onclick="editSection({{ $section->id }})" 
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteSection({{ $section->id }})" 
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Section Content (Materials) -->
                        <div class="section-content hidden" id="section-{{ $section->id }}">
                            <div class="bg-gray-50 p-4">
                                <!-- Section Pembelajaran Header -->
                                <div class="flex items-center justify-between mb-3">
                                    <h5 class="text-sm font-medium text-gray-700">Section Pembelajaran</h5>
                                    <div class="flex gap-2">
                                        <button onclick="openMaterialModal({{ $section->id }}, 'video')" 
                                                class="px-3 py-1.5 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition flex items-center gap-1">
                                            <i class="fas fa-video"></i> Upload Video
                                        </button>
                                        <button onclick="openMaterialModal({{ $section->id }}, 'pdf')" 
                                                class="px-3 py-1.5 bg-red-600 text-white rounded text-xs hover:bg-red-700 transition flex items-center gap-1">
                                            <i class="fas fa-file-pdf"></i> Upload PDF
                                        </button>
                                        <button onclick="openMaterialModal({{ $section->id }}, 'text')" 
                                                class="px-3 py-1.5 bg-green-600 text-white rounded text-xs hover:bg-green-700 transition flex items-center gap-1">
                                            <i class="fas fa-align-left"></i> Buat Teks
                                        </button>
                                        <button onclick="openQuizModal({{ $section->id }})" 
                                                class="px-3 py-1.5 bg-yellow-600 text-white rounded text-xs hover:bg-yellow-700 transition flex items-center gap-1">
                                            <i class="fas fa-question-circle"></i> Buat Quiz
                                        </button>
                                    </div>
                                </div>

                                <!-- Materials List -->
                                @if($section->materials->isEmpty())
                                    <div class="bg-white rounded-lg border border-gray-200 p-6 text-center">
                                        <i class="fas fa-inbox text-gray-300 text-3xl mb-2"></i>
                                        <p class="text-gray-500 text-sm">Belum ada materi di section ini</p>
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        @foreach($section->materials as $material)
                                            <div class="bg-white rounded-lg border border-gray-200 p-3 hover:border-blue-300 transition group">
                                                <div class="flex items-center gap-3">
                                                    <!-- Icon based on type -->
                                                    <div class="w-8 h-8 rounded flex items-center justify-center flex-shrink-0
                                                        {{ $material->type === 'video' ? 'bg-blue-100' : '' }}
                                                        {{ $material->type === 'pdf' ? 'bg-red-100' : '' }}
                                                        {{ $material->type === 'quiz' ? 'bg-yellow-100' : '' }}
                                                        {{ $material->type === 'text' ? 'bg-green-100' : '' }}">
                                                        @if($material->type === 'video')
                                                            <i class="fas fa-play text-blue-600"></i>
                                                        @elseif($material->type === 'pdf')
                                                            <i class="fas fa-file-pdf text-red-600"></i>
                                                        @elseif($material->type === 'quiz')
                                                            <i class="fas fa-question-circle text-yellow-600"></i>
                                                        @else
                                                            <i class="fas fa-align-left text-green-600"></i>
                                                        @endif
                                                    </div>

                                                    <!-- Content - Clickable -->
                                                    <a href="{{ route('instructor.materials.preview', [$course, $material]) }}" 
                                                       class="flex-1 min-w-0">
                                                        <h6 class="font-medium text-gray-900 truncate hover:text-blue-600 transition">{{ $material->judul }}</h6>
                                                        <p class="text-xs text-gray-500">
                                                            {{ ucfirst($material->type) }}
                                                            @if($material->duration)
                                                                • {{ $material->duration }} menit
                                                            @endif
                                                        </p>
                                                    </a>

                                                    <!-- Actions -->
                                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                        @if($material->type === 'quiz' && $material->assignment)
                                                        <a href="{{ route('instructor.assignments.edit-questions', $material->assignment) }}"
                                                           class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded transition"
                                                           title="Kelola Soal">
                                                            <i class="fas fa-list"></i>
                                                        </a>
                                                        @endif
                                                        <a href="{{ route('instructor.materials.preview', [$course, $material]) }}" 
                                                           class="p-1.5 text-green-600 hover:bg-green-50 rounded transition"
                                                           title="Preview">
                                                            <i class="fas fa-eye text-sm"></i>
                                                        </a>
                                                        <a href="{{ route('instructor.materials.edit', [$course, $material]) }}" 
                                                           class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition"
                                                           title="Edit">
                                                            <i class="fas fa-edit text-sm"></i>
                                                        </a>
                                                        <form action="{{ route('instructor.materials.destroy', [$course, $material]) }}" 
                                                              method="POST" 
                                                              onsubmit="return confirm('Yakin ingin menghapus?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="p-1.5 text-red-600 hover:bg-red-50 rounded transition"
                                                                    title="Hapus">
                                                                <i class="fas fa-trash text-sm"></i>
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
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah/Edit Section -->
<div id="sectionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4" id="sectionModalTitle">Tambah Modul</h3>
        <form id="sectionForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="sectionMethod" value="POST">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Modul *</label>
                <input type="text" name="title" id="sectionTitle" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" id="sectionDescription" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                <input type="number" name="order" id="sectionOrder" min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeSectionModal()" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Materi -->
<div id="materialModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Tambah Materi</h3>
        <form action="{{ route('instructor.materials.store', $course) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="section_id" id="materialSectionId">
            <input type="hidden" name="type" id="materialType">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Materi *</label>
                <input type="text" name="judul" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="mb-4" id="fileUploadSection">
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
                <input type="file" name="file" accept=".pdf,.mp4,.avi,.mov"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>

            <div class="mb-4" id="contentSection" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Konten</label>
                <textarea name="content" rows="6"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Durasi (menit)</label>
                <input type="number" name="duration" min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeMaterialModal()" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan Materi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Buat Quiz -->
<div id="quizModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-question-circle text-yellow-600"></i> Buat Quiz
        </h3>
        <form action="{{ route('instructor.courses.quizzes.store', $course) }}" method="POST">
            @csrf
            <input type="hidden" name="section_id" id="quizSectionId">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Quiz *</label>
                <input type="text" name="title" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Soal (opsional)</label>
                <select name="question_bank_id" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Tanpa bank soal (buat kosong dulu)</option>
                    @foreach($questionBanks as $bank)
                        <option value="{{ $bank->id }}">{{ $bank->title }} ({{ $bank->questions_count }} soal)</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Jika dipilih, semua soal di bank ini akan ditambahkan ke quiz.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Passing Score</label>
                    <input type="number" name="passing_score" value="60" min="0" max="100" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (menit)</label>
                    <input type="number" name="duration_minutes" min="1" class="w-full px-4 py-2 border rounded-lg" placeholder="Opsional">
                </div>
            </div>

            <label class="inline-flex items-center gap-2 mb-6">
                <input type="checkbox" name="randomize_questions" class="h-4 w-4 text-yellow-600">
                <span class="text-sm text-gray-700">Acak urutan soal</span>
            </label>

            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeQuizModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    Simpan Quiz
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleSection(sectionId) {
    const content = document.getElementById(`section-${sectionId}`);
    const chevron = document.getElementById(`chevron-${sectionId}`);
    
    content.classList.toggle('hidden');
    chevron.classList.toggle('rotate-90');
}

function toggleAllSections() {
    document.querySelectorAll('.section-content').forEach(content => {
        content.classList.toggle('hidden');
    });
    document.querySelectorAll('.section-chevron').forEach(chevron => {
        chevron.classList.toggle('rotate-90');
    });
}

function openSectionModal() {
    document.getElementById('sectionModal').classList.remove('hidden');
    document.getElementById('sectionModalTitle').textContent = 'Tambah Modul';
    document.getElementById('sectionForm').action = "{{ route('instructor.courses.sections.store', $course) }}";
    document.getElementById('sectionMethod').value = 'POST';
    document.getElementById('sectionTitle').value = '';
    document.getElementById('sectionDescription').value = '';
    document.getElementById('sectionOrder').value = '';
}

function closeSectionModal() {
    document.getElementById('sectionModal').classList.add('hidden');
}

function openMaterialModal(sectionId = null, type = 'video') {
    document.getElementById('materialModal').classList.remove('hidden');
    if (sectionId) {
        document.getElementById('materialSectionId').value = sectionId;
    }
    document.getElementById('materialType').value = type;
    
    // Show/hide sections based on type
    if (type === 'text') {
        document.getElementById('fileUploadSection').style.display = 'none';
        document.getElementById('contentSection').style.display = 'block';
    } else {
        document.getElementById('fileUploadSection').style.display = 'block';
        document.getElementById('contentSection').style.display = 'none';
    }
}

function closeMaterialModal() {
    document.getElementById('materialModal').classList.add('hidden');
}

function openQuizModal(sectionId = null) {
    document.getElementById('quizModal').classList.remove('hidden');
    document.getElementById('quizSectionId').value = sectionId || '';
}

function closeQuizModal() {
    document.getElementById('quizModal').classList.add('hidden');
}

function deleteSection(sectionId) {
    if (confirm('Yakin ingin menghapus modul ini? Semua materi di dalamnya akan ikut terhapus.')) {
        // Submit delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('instructor.sections.destroy', '__SECTION_ID__') }}".replace('__SECTION_ID__', sectionId);
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
