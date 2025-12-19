@extends('layouts.instructor')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <a href="{{ route('instructor.courses.materials.preview', [$course, $material]) }}" class="text-blue-600 hover:text-blue-700 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Materi</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Edit Materi</h2>
        <p class="text-gray-600 mb-6">Kursus: {{ $course->judul }}</p>

        <form action="{{ route('instructor.courses.materials.update', [$course, $material]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Materi *</label>
                    <input type="text" name="judul" value="{{ old('judul', $material->judul) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('judul') border-red-500 @enderror">
                    @error('judul')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $material->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Materi *</label>
                    {{-- Tipe materi tidak bisa diubah setelah dibuat --}}
                    <input type="hidden" name="type" id="materialTypeSelect" value="{{ $material->type }}">
                    @php
                        $typeLabels = [
                            'video' => 'Video',
                            'pdf' => 'PDF',
                            'document' => 'Dokumen',
                            'text' => 'Teks',
                            'quiz' => 'Quiz',
                            'reading' => 'Reading',
                            'class_session' => 'Sesi Kelas',
                        ];
                        $currentTypeLabel = $typeLabels[$material->type] ?? ucfirst($material->type);
                    @endphp
                    <div class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed">
                        {{ $currentTypeLabel }}
                        <span class="text-xs text-gray-400 ml-2">(tidak dapat diubah)</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Tipe materi tidak dapat diubah setelah materi dibuat.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="draft" {{ old('status', $material->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $material->status) === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Section</label>
                    <select name="section_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tanpa Section</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ old('section_id', $material->section_id) == $section->id ? 'selected' : '' }}>
                                {{ $section->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2" id="fileUploadBlock">
                    <label class="block text-sm font-medium text-gray-700 mb-2">File (Video/PDF)</label>
                    <input type="file" name="file" accept="video/mp4,application/pdf"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('file') border-red-500 @enderror">
                    <p class="text-sm text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengganti file.</p>
                    @if($material->file_url || $material->url_konten)
                        <p class="text-sm text-gray-600 mt-1">File saat ini: {{ basename($material->file_url ?? $material->url_konten) }}</p>
                    @endif
                    @error('file')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Durasi (menit)</label>
                    <input type="number" name="duration" value="{{ old('duration', $material->duration) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                    <input type="number" name="urutan" value="{{ old('urutan', $material->urutan) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_preview" value="1" {{ old('is_preview', $material->is_preview) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Bisa diakses sebagai preview</span>
                    </label>
                </div>

                <div class="md:col-span-2" id="contentBlock">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konten Text (jika tipe Text)</label>
                    <textarea name="content" rows="6"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('content', $material->content) }}</textarea>
                </div>

                {{-- Class Session Fields --}}
                <div class="md:col-span-2 space-y-4" id="classSessionBlock">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Sesi *</label>
                            <input type="date" name="session_date" value="{{ old('session_date', optional($material->session_date)->format('Y-m-d')) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Sesi *</label>
                            <select name="session_type"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 shadow-sm">
                                <option value="offline" {{ old('session_type', $material->session_type) === 'offline' ? 'selected' : '' }}>Offline (Tatap Muka)</option>
                                <option value="online" {{ old('session_type', $material->session_type) === 'online' ? 'selected' : '' }}>Online (Virtual)</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Mulai *</label>
                            <input type="time" name="session_start_time" value="{{ old('session_start_time', $material->session_start_time) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Selesai *</label>
                            <input type="time" name="session_end_time" value="{{ old('session_end_time', $material->session_end_time) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 shadow-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi / Tempat</label>
                        <input type="text" name="session_location" value="{{ old('session_location', $material->session_location) }}" placeholder="Contoh: Ruang Lab 301, Gedung A"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Link Meeting (untuk sesi online)</label>
                        <input type="url" name="session_meeting_link" value="{{ old('session_meeting_link', $material->session_meeting_link) }}" placeholder="https://zoom.us/j/..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 shadow-sm">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 mt-8">
                <button type="submit"
                        class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('instructor.courses.materials.preview', [$course, $material]) }}"
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const typeSelect = document.getElementById('materialTypeSelect');
        const fileBlock = document.getElementById('fileUploadBlock');
        const contentBlock = document.getElementById('contentBlock');
        const sessionBlock = document.getElementById('classSessionBlock');

        const toggleFields = () => {
            const type = typeSelect.value;
            if (type === 'text') {
                contentBlock.style.display = '';
            } else {
                contentBlock.style.display = 'none';
            }

            if (type === 'class_session') {
                sessionBlock.style.display = '';
                fileBlock.style.display = 'none';
            } else {
                sessionBlock.style.display = 'none';
                fileBlock.style.display = '';
            }
        };

        toggleFields();
        // Type tidak bisa diubah, jadi tidak perlu event listener
    });
</script>
@endpush
