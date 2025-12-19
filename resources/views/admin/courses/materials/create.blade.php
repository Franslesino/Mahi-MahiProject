@extends('layouts.admin')

@section('content')
<div class="p-8 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.courses.materials.index', $course) }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-3xl font-bold text-gray-800">Tambah Materi Baru</h2>
        </div>
        <p class="text-gray-600">{{ $course->judul }}</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm p-8">
        <form action="{{ route('admin.courses.materials.store', $course) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Judul -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Judul Materi <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="judul" 
                       value="{{ old('judul') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('judul') border-red-500 @enderror"
                       placeholder="Contoh: Pengenalan HTML"
                       required>
                @error('judul')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipe Materi -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tipe Materi <span class="text-red-500">*</span>
                </label>
                <select name="type" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror"
                        required>
                    <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video</option>
                    <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Text</option>
                    <option value="document" {{ old('type') == 'document' ? 'selected' : '' }}>Document</option>
                    <option value="pdf" {{ old('type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                    <option value="reading" {{ old('type') == 'reading' ? 'selected' : '' }}>Reading</option>
                </select>
                @error('type')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Isi/Konten Teks -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Isi Materi (Teks)
                </label>
                <textarea name="isi" 
                          rows="6"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('isi') border-red-500 @enderror"
                          placeholder="Isi konten materi jika berbentuk teks...">{{ old('isi') }}</textarea>
                @error('isi')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Konten URL (untuk video/link eksternal) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    URL Konten (YouTube, Vimeo, dll)
                </label>
                <input type="text" 
                       name="url_konten" 
                       value="{{ old('url_konten') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('url_konten') border-red-500 @enderror"
                       placeholder="https://youtube.com/watch?v=...">
                <p class="text-sm text-gray-500 mt-1">Atau upload file di bawah</p>
                @error('url_konten')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Upload File
                </label>
                <input type="file" 
                       name="file"
                       accept=".pdf,.doc,.docx,.ppt,.pptx,.mp4,.avi,.mov"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('file') border-red-500 @enderror">
                <p class="text-sm text-gray-500 mt-1">Maks 50MB. Format: PDF, DOC, DOCX, PPT, PPTX, MP4, AVI, MOV</p>
                @error('file')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Urutan -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Urutan
                </label>
                <input type="number" 
                       name="urutan" 
                       value="{{ old('urutan', $course->materi()->max('urutan') + 1) }}"
                       min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('urutan') border-red-500 @enderror">
                <p class="text-sm text-gray-500 mt-1">Urutan tampilan materi</p>
                @error('urutan')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Terkunci -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="status_terkunci"
                           value="1"
                           {{ old('status_terkunci') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Kunci Materi (Harus menyelesaikan materi sebelumnya)</span>
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.courses.materials.index', $course) }}" 
                   class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-save mr-2"></i>Simpan Materi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
