@extends('layouts.instructor')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <a href="{{ route('instructor.courses.show', $course) }}" class="text-blue-600 hover:text-blue-700 mb-2 inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Detail Kursus</span>
        </a>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Edit Materi</h2>
        <p class="text-gray-600 mt-1">Untuk kursus: <strong>{{ $course->title }}</strong></p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('instructor.materials.update', [$course, $material]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Judul Materi <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       value="{{ old('title', $material->title) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                       placeholder="Contoh: Pengenalan Laravel Blade"
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type -->
            <div class="mb-6">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                    Tipe Materi <span class="text-red-500">*</span>
                </label>
                <select id="type" 
                        name="type" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror"
                        required>
                    <option value="">-- Pilih Tipe Materi --</option>
                    <option value="video" {{ old('type', $material->type) === 'video' ? 'selected' : '' }}>Video</option>
                    <option value="pdf" {{ old('type', $material->type) === 'pdf' ? 'selected' : '' }}>PDF/Dokumen</option>
                    <option value="text" {{ old('type', $material->type) === 'text' ? 'selected' : '' }}>Text/Article</option>
                    <option value="quiz" {{ old('type', $material->type) === 'quiz' ? 'selected' : '' }}>Quiz</option>
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                          placeholder="Jelaskan tentang materi ini...">{{ old('description', $material->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Duration -->
            <div class="mb-6">
                <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                    Durasi (menit)
                </label>
                <input type="number" 
                       id="duration" 
                       name="duration" 
                       value="{{ old('duration', $material->duration) }}"
                       min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('duration') border-red-500 @enderror"
                       placeholder="Contoh: 15">
                @error('duration')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current File -->
            @if($material->file_url)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Saat Ini</label>
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        @php
                            $extension = pathinfo($material->file_url, PATHINFO_EXTENSION);
                        @endphp
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            @if(in_array($extension, ['pdf']))
                                <i class="fas fa-file-pdf text-red-600"></i>
                            @elseif(in_array($extension, ['mp4', 'avi', 'mov']))
                                <i class="fas fa-video text-blue-600"></i>
                            @elseif(in_array($extension, ['jpg', 'jpeg', 'png']))
                                <i class="fas fa-image text-green-600"></i>
                            @else
                                <i class="fas fa-file text-gray-600"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ basename($material->file_url) }}</p>
                            <p class="text-xs text-gray-500">{{ strtoupper($extension) }} File</p>
                        </div>
                        <a href="{{ Storage::url($material->file_url) }}" 
                           target="_blank"
                           class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            Lihat File
                        </a>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-info-circle"></i> 
                        Upload file baru untuk mengganti file yang ada
                    </p>
                </div>
            @endif

            <!-- File Upload -->
            <div class="mb-6">
                <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                    Upload File {{ $material->file_url ? 'Baru (Opsional)' : '' }}
                </label>
                <div class="flex items-center justify-center w-full">
                    <label for="file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                            <p class="mb-2 text-sm text-gray-500">
                                <span class="font-semibold">Klik untuk upload</span> atau drag and drop
                            </p>
                            <p class="text-xs text-gray-500">PDF, MP4, AVI, MOV, JPG, PNG (Max. 100MB)</p>
                        </div>
                        <input id="file" 
                               name="file" 
                               type="file" 
                               class="hidden" 
                               accept=".pdf,.mp4,.avi,.mov,.jpg,.jpeg,.png"
                               onchange="displayFileName(this)">
                    </label>
                </div>
                <p id="file-name" class="mt-2 text-sm text-gray-600"></p>
                @error('file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content (for text type) -->
            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Konten Teks (opsional, untuk tipe Text)
                </label>
                <textarea id="content" 
                          name="content" 
                          rows="6"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('content') border-red-500 @enderror"
                          placeholder="Masukkan konten artikel atau materi text di sini...">{{ old('content', $material->content) }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('instructor.courses.show', $course) }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Update Materi</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function displayFileName(input) {
    const fileNameDisplay = document.getElementById('file-name');
    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
        fileNameDisplay.textContent = `File baru dipilih: ${fileName} (${fileSize} MB)`;
        fileNameDisplay.classList.add('text-blue-600', 'font-medium');
    } else {
        fileNameDisplay.textContent = '';
    }
}
</script>
@endpush
@endsection