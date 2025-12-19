@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.courses.detail', $course) }}" 
               class="text-blue-600 hover:text-blue-800 transition">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <h1 class="text-3xl font-bold text-gray-800">Edit Materi</h1>
        <p class="text-gray-600 mt-2">{{ $course->judul }} - {{ $section->title }}</p>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <form action="{{ route('admin.courses.modules.materials.update', [$course, $section, $material]) }}" 
              method="POST" 
              enctype="multipart/form-data"
              class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Info -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Informasi Dasar</h3>
                
                <!-- Title -->
                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Materi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="judul" 
                           id="judul" 
                           value="{{ old('judul', $material->judul) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                    @error('judul')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description', $material->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Materi <span class="text-red-500">*</span>
                    </label>
                    <select name="type" 
                            id="type" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required
                            onchange="toggleContentFields()">
                        <option value="video" {{ old('type', $material->type) === 'video' ? 'selected' : '' }}>Video</option>
                        <option value="document" {{ old('type', $material->type) === 'document' ? 'selected' : '' }}>Dokumen</option>
                        <option value="pdf" {{ old('type', $material->type) === 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="text" {{ old('type', $material->type) === 'text' ? 'selected' : '' }}>Teks</option>
                        <option value="quiz" {{ old('type', $material->type) === 'quiz' ? 'selected' : '' }}>Quiz</option>
                        <option value="class_session" {{ old('type', $material->type) === 'class_session' ? 'selected' : '' }}>Sesi Kelas</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Content Fields -->
            <div class="space-y-4" id="content-fields">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Konten Materi</h3>
                
                <!-- Video URL (for video type) -->
                <div id="video-url-field" style="display: none;">
                    <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">
                        URL Video (YouTube atau link langsung)
                    </label>
                    <input type="url" 
                           name="video_url" 
                           id="video_url" 
                           value="{{ old('video_url', $material->video_url) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="https://youtube.com/watch?v=...">
                    <p class="text-sm text-gray-500 mt-1">Atau upload file video di bawah</p>
                    @error('video_url')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Upload (for video/document type) -->
                <div id="file-upload-field" style="display: none;">
                    <label for="file_path" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload File
                    </label>
                    
                    @if($material->file_path)
                        <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-file mr-2"></i>
                                File saat ini: 
                                <a href="{{ $material->file_path }}" target="_blank" class="underline hover:text-blue-600">
                                    Lihat file
                                </a>
                            </p>
                        </div>
                    @endif
                    
                    <input type="file" 
                           name="file_path" 
                           id="file_path" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           accept=".pdf,.doc,.docx,.ppt,.pptx,.mp4,.avi,.mov">
                    <p class="text-sm text-gray-500 mt-1">Format: PDF, DOC, DOCX, PPT, PPTX, MP4, AVI, MOV (max 50MB)</p>
                    @error('file_path')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quiz Note -->
                <div id="quiz-note" style="display: none;">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-yellow-600 text-xl mt-0.5"></i>
                            <div>
                                <h4 class="font-semibold text-yellow-900 mb-1">Catatan Quiz</h4>
                                <p class="text-sm text-yellow-800">
                                    Untuk mengelola soal quiz, gunakan tombol "Kelola Soal" di halaman detail kursus.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Class Session Fields -->
                <div id="class-session-fields" style="display: none;" class="space-y-4 bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <h4 class="font-semibold text-orange-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-calendar-alt"></i> Detail Sesi Kelas
                    </h4>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sesi *</label>
                            <input type="date" name="session_date" value="{{ old('session_date', $material->session_date ? $material->session_date->format('Y-m-d') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai *</label>
                            <input type="time" name="session_start_time" value="{{ old('session_start_time', $material->session_start_time ? $material->session_start_time->format('H:i') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai *</label>
                            <input type="time" name="session_end_time" value="{{ old('session_end_time', $material->session_end_time ? $material->session_end_time->format('H:i') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Sesi *</label>
                            <select name="session_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option value="online" {{ old('session_type', $material->session_type) === 'online' ? 'selected' : '' }}>Online (Zoom/Meet)</option>
                                <option value="offline" {{ old('session_type', $material->session_type) === 'offline' ? 'selected' : '' }}>Offline (Tatap Muka)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi / Ruangan</label>
                            <input type="text" name="session_location" value="{{ old('session_location', $material->session_location) }}" placeholder="Contoh: Ruang A atau Zoom" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link Pertemuan (Jika Online)</label>
                        <input type="url" name="session_meeting_link" value="{{ old('session_meeting_link', $material->session_meeting_link) }}" placeholder="https://zoom.us/..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Pengaturan</h3>
                
                <!-- Is Preview -->
                <div class="flex items-center gap-3">
                    <input type="checkbox" 
                           name="is_preview" 
                           id="is_preview" 
                           value="1"
                           {{ old('is_preview', $material->is_preview) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                    <label for="is_preview" class="text-sm font-medium text-gray-700">
                        <i class="fas fa-eye text-blue-600 mr-1"></i>
                        Izinkan preview (dapat dilihat tanpa mendaftar)
                    </label>
                </div>

                <!-- Is Locked -->
                <div class="flex items-center gap-3">
                    <input type="checkbox" 
                           name="status_terkunci" 
                           id="status_terkunci" 
                           value="1"
                           {{ old('status_terkunci', $material->status_terkunci) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                    <label for="status_terkunci" class="text-sm font-medium text-gray-700">
                        <i class="fas fa-lock text-yellow-600 mr-1"></i>
                        Kunci materi (memerlukan penyelesaian materi sebelumnya)
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t">
                <a href="{{ route('admin.courses.detail', $course) }}" 
                   class="inline-flex items-center px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm hover:shadow-md">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleContentFields() {
    const type = document.getElementById('type').value;
    const videoUrlField = document.getElementById('video-url-field');
    const fileUploadField = document.getElementById('file-upload-field');
    const quizNote = document.getElementById('quiz-note');
    const classSessionFields = document.getElementById('class-session-fields');
    
    // Hide all fields first
    videoUrlField.style.display = 'none';
    fileUploadField.style.display = 'none';
    quizNote.style.display = 'none';
    classSessionFields.style.display = 'none';
    
    // Show relevant fields based on type
    if (type === 'video') {
        videoUrlField.style.display = 'block';
        fileUploadField.style.display = 'block';
    } else if (type === 'document' || type === 'pdf') {
        fileUploadField.style.display = 'block';
    } else if (type === 'quiz') {
        quizNote.style.display = 'block';
    } else if (type === 'class_session') {
        classSessionFields.style.display = 'block';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleContentFields();
});
</script>
@endsection
