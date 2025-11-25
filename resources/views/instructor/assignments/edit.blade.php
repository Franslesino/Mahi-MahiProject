@extends('layouts.instructor')

@section('content')
<div class="p-8 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Edit Assignment</h2>
        <p class="text-gray-600 mt-2">Perbarui pengaturan assignment</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('instructor.assignments.update', $assignment) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Course Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kursus <span class="text-red-500">*</span>
                    </label>
                    <select name="kursus_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            required>
                        @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $assignment->kursus_id == $course->id ? 'selected' : '' }}>
                            {{ $course->judul }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Material Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Materi (Opsional)
                    </label>
                    <select name="materi_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Tidak terkait materi tertentu --</option>
                        @foreach($materials as $material)
                        <option value="{{ $material->id }}" {{ $assignment->materi_id == $material->id ? 'selected' : '' }}>
                            {{ $material->judul }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Assignment <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           value="{{ old('title', $assignment->title) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                           required>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" 
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">{{ old('description', $assignment->description) }}</textarea>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe <span class="text-red-500">*</span>
                    </label>
                    <select name="type" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            required>
                        <option value="quiz" {{ $assignment->type == 'quiz' ? 'selected' : '' }}>Quiz</option>
                        <option value="assignment" {{ $assignment->type == 'assignment' ? 'selected' : '' }}>Assignment</option>
                        <option value="exam" {{ $assignment->type == 'exam' ? 'selected' : '' }}>Exam</option>
                    </select>
                </div>

                <!-- Duration and Passing Score -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Durasi (Menit)
                        </label>
                        <input type="number" 
                               name="duration_minutes" 
                               value="{{ old('duration_minutes', $assignment->duration_minutes) }}"
                               min="1"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nilai Minimal Lulus (%) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="passing_score" 
                               value="{{ old('passing_score', $assignment->passing_score) }}"
                               min="0"
                               max="100"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                               required>
                    </div>
                </div>

                <!-- Start and Due Date -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai
                        </label>
                        <input type="datetime-local" 
                               name="start_date" 
                               value="{{ old('start_date', $assignment->start_date?->format('Y-m-d\TH:i')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Deadline
                        </label>
                        <input type="datetime-local" 
                               name="due_date" 
                               value="{{ old('due_date', $assignment->due_date?->format('Y-m-d\TH:i')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <!-- Options -->
                <div class="space-y-3 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-800">Pengaturan Tambahan</h4>
                    
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" 
                               name="show_results_immediately" 
                               value="1"
                               {{ old('show_results_immediately', $assignment->show_results_immediately) ? 'checked' : '' }}
                               class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm text-gray-700">
                            Tampilkan hasil segera setelah submit
                        </span>
                    </label>

                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" 
                               name="allow_multiple_attempts" 
                               value="1"
                               id="allowMultiple"
                               {{ old('allow_multiple_attempts', $assignment->allow_multiple_attempts) ? 'checked' : '' }}
                               class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm text-gray-700">
                            Izinkan multiple attempts
                        </span>
                    </label>

                    <div id="maxAttemptsContainer" style="{{ $assignment->allow_multiple_attempts ? '' : 'display: none;' }}" class="ml-7">
                        <label class="block text-sm text-gray-700 mb-1">Maksimal Attempts</label>
                        <input type="number" 
                               name="max_attempts" 
                               value="{{ old('max_attempts', $assignment->max_attempts) }}"
                               min="1"
                               class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    </div>

                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" 
                               name="randomize_questions" 
                               value="1"
                               {{ old('randomize_questions', $assignment->randomize_questions) ? 'checked' : '' }}
                               class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm text-gray-700">
                            Acak urutan soal
                        </span>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-between pt-4 border-t">
                    <a href="{{ route('instructor.assignments.show', $assignment) }}" 
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const allowMultiple = document.getElementById('allowMultiple');
    const maxAttemptsContainer = document.getElementById('maxAttemptsContainer');

    allowMultiple.addEventListener('change', function() {
        maxAttemptsContainer.style.display = this.checked ? 'block' : 'none';
    });
});
</script>
@endsection
