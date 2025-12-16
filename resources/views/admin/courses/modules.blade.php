@extends('layouts.admin')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.courses.panel', $course) }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-3xl font-bold text-gray-800">Kelola Modul</h2>
        </div>
        <p class="text-gray-600">{{ $course->judul }}</p>
    </div>

    <!-- Action Button -->
    <div class="mb-6 flex gap-3">
        <a href="{{ route('instructor.courses.final-quiz.edit', $course->id) }}" 
           class="px-5 py-2.5 bg-purple-500 text-white rounded-lg hover:bg-purple-400 transition">
            <i class="fas fa-graduation-cap mr-2"></i>
            Final Quiz
        </a>
        <button onclick="openModuleModal()" 
                class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
            <i class="fas fa-plus mr-2"></i>
            Tambah Modul
        </button>
    </div>

    <!-- Modules List -->
    <div class="space-y-4">
        @forelse($sections as $section)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $section->judul }}</h3>
                    @if($section->deskripsi)
                    <p class="text-sm text-gray-600 mt-1">{{ $section->deskripsi }}</p>
                    @endif
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-file-alt mr-1"></i>
                        {{ $section->materials->count() }} materi
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.courses.materials.index', [$course]) }}" 
                       class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-medium">
                        <i class="fas fa-book-open mr-1"></i>
                        Kelola Materi
                    </a>
                    <button onclick="editModule({{ $section->id }}, '{{ addslashes($section->judul) }}', '{{ addslashes($section->deskripsi ?? '') }}')" 
                            class="text-emerald-600 hover:text-emerald-800 p-2">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('admin.courses.modules.destroy', [$course, $section]) }}" 
                          method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus modul ini?')" 
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 p-2">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>

            @if($section->materials->isNotEmpty())
            <div class="border-t border-gray-200 bg-gray-50 p-4">
                <div class="space-y-2">
                    @foreach($section->materials as $material)
                    <div class="flex items-center justify-between bg-white p-3 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                @if($material->type === 'video')
                                    <i class="fas fa-video text-blue-600"></i>
                                @elseif($material->type === 'reading')
                                    <i class="fas fa-book text-blue-600"></i>
                                @else
                                    <i class="fas fa-clipboard-question text-blue-600"></i>
                                @endif
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $material->judul }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst($material->type) }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="fas fa-folder-open text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Modul</h3>
            <p class="text-gray-500 mb-6">Mulai dengan menambahkan modul untuk kursus ini</p>
            <button onclick="openModuleModal()" 
                    class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                <i class="fas fa-plus mr-2"></i>
                Tambah Modul Pertama
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- Add Module Modal -->
<div id="moduleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Tambah Modul</h3>
        </div>
        <form action="{{ route('admin.courses.modules.store', $course) }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Modul *</label>
                    <input type="text" name="judul" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                </div>
            </div>
            <div class="mt-6 flex gap-3 justify-end">
                <button type="button" onclick="closeModuleModal()" 
                        class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Module Modal -->
<div id="editModuleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Edit Modul</h3>
        </div>
        <form id="editModuleForm" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Modul *</label>
                    <input type="text" id="edit_judul" name="judul" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="edit_deskripsi" name="deskripsi" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                </div>
            </div>
            <div class="mt-6 flex gap-3 justify-end">
                <button type="button" onclick="closeEditModuleModal()" 
                        class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModuleModal() {
    document.getElementById('moduleModal').classList.remove('hidden');
}

function closeModuleModal() {
    document.getElementById('moduleModal').classList.add('hidden');
}

function editModule(id, judul, deskripsi) {
    document.getElementById('edit_judul').value = judul;
    document.getElementById('edit_deskripsi').value = deskripsi || '';
    // Build URL manually untuk update module
    const baseUrl = '{{ url("/admin/courses/" . $course->id . "/modules") }}';
    document.getElementById('editModuleForm').action = baseUrl + '/' + id;
    document.getElementById('editModuleModal').classList.remove('hidden');
}

function closeEditModuleModal() {
    document.getElementById('editModuleModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('moduleModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModuleModal();
});

document.getElementById('editModuleModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeEditModuleModal();
});
</script>
@endsection
