@extends('layouts.instructor')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <a href="{{ route('instructor.courses.show', $course) }}" class="text-blue-600 hover:text-blue-700 mb-2 inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Detail Kursus</span>
        </a>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Tambah Materi Baru</h2>
        <p class="text-gray-600 mt-1">Untuk kursus: <strong>{{ $course->judul }}</strong></p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('instructor.materials.store', $course) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- judul -->
            <div class="mb-6">
                <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
                    Judul Materi <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="judul" 
                       name="judul" 
                       value="{{ old('judul') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('judul') border-red-500 @enderror"
                       placeholder="Contoh: Pengenalan Laravel Blade"
                       required>
                @error('judul')
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
                    <option value="video" {{ old('type') === 'video' ? 'selected' : '' }}>Video</option>
                    <option value="pdf" {{ old('type') === 'pdf' ? 'selected' : '' }}>PDF/Dokumen</option>
                    <option value="text" {{ old('type') === 'text' ? 'selected' : '' }}>Text/Article</option>
                    <option value="quiz" {{ old('type') === 'quiz' ? 'selected' : '' }}>Quiz</option>
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
                          placeholder="Jelaskan tentang materi ini...">{{ old('description') }}</textarea>
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
                       value="{{ old('duration') }}"
                       min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('duration') border-red-500 @enderror"
                       placeholder="Contoh: 15">
                @error('duration')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div class="mb-6">
                <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                    Upload File
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
                          placeholder="Masukkan konten artikel atau materi text di sini...">{{ old('content') }}</textarea>
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
                    <span>Simpan Materi</span>
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
        fileNameDisplay.textContent = `File dipilih: ${fileName} (${fileSize} MB)`;
        fileNameDisplay.classList.add('text-blue-600', 'font-medium');
    } else {
        fileNameDisplay.textContent = '';
    }
}
</script>
@endpush
@endsection