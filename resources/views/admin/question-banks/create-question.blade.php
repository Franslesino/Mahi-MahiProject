@extends('layouts.admin')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.question-banks.show', $questionBank) }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-3xl font-bold text-gray-800">Buat Soal Baru</h2>
            </div>
            <p class="text-gray-600">{{ $questionBank->title }}</p>
        </div>
        
        <!-- Import Button -->
        <button type="button" 
                onclick="document.getElementById('importModal').classList.remove('hidden')"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
            <i class="fas fa-file-import mr-2"></i>Import dari Template
        </button>
    </div>

    <!-- Question Form -->
    <div x-data="{ 
        questionType: 'multiple_choice',
        optionCount: 4,
        removeOption(index) {
            if (this.optionCount > 2) {
                this.optionCount--;
            }
        }
    }" class="bg-white rounded-xl shadow-sm p-8">
        <form id="questionForm" action="{{ route('admin.question-banks.questions.store', $questionBank) }}" method="POST">
            @csrf

            <!-- Question Type -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tipe Soal <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-blue-500 transition"
                           :class="questionType === 'multiple_choice' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                        <input type="radio" 
                               name="type" 
                               value="multiple_choice"
                               x-model="questionType"
                               class="sr-only"
                               required>
                        <div class="text-center w-full">
                            <i class="fas fa-list-ul text-2xl mb-2 text-blue-600"></i>
                            <div class="font-medium text-sm">Pilihan Ganda</div>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-green-500 transition"
                           :class="questionType === 'true_false' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                        <input type="radio" 
                               name="type" 
                               value="true_false"
                               x-model="questionType"
                               class="sr-only">
                        <div class="text-center w-full">
                            <i class="fas fa-check-circle text-2xl mb-2 text-green-600"></i>
                            <div class="font-medium text-sm">Benar/Salah</div>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-purple-500 transition"
                           :class="questionType === 'short_answer' ? 'border-purple-500 bg-purple-50' : 'border-gray-200'">
                        <input type="radio" 
                               name="type" 
                               value="short_answer"
                               x-model="questionType"
                               class="sr-only">
                        <div class="text-center w-full">
                            <i class="fas fa-keyboard text-2xl mb-2 text-purple-600"></i>
                            <div class="font-medium text-sm">Jawaban Singkat</div>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-orange-500 transition"
                           :class="questionType === 'essay' ? 'border-orange-500 bg-orange-50' : 'border-gray-200'">
                        <input type="radio" 
                               name="type" 
                               value="essay"
                               x-model="questionType"
                               class="sr-only">
                        <div class="text-center w-full">
                            <i class="fas fa-file-alt text-2xl mb-2 text-orange-600"></i>
                            <div class="font-medium text-sm">Essay</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Question Text -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pertanyaan <span class="text-red-500">*</span>
                </label>
                <textarea name="question_text" 
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Tulis pertanyaan Anda di sini..."
                          required></textarea>
            </div>

            <!-- Points -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Poin <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="points" 
                       value="1"
                       min="1"
                       class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       required>
            </div>

            <!-- Multiple Choice Options -->
            <div x-show="questionType === 'multiple_choice'" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pilihan Jawaban <span class="text-red-500">*</span>
                </label>
                <div id="optionsContainer" class="space-y-3">
                    <template x-for="i in optionCount" :key="i">
                        <div class="flex gap-3 items-start">
                            <input type="radio" 
                                   :name="'correct_option'"
                                   :value="i"
                                   class="mt-3 text-blue-600 focus:ring-blue-500">
                            <input type="text" 
                                   :name="'options[' + i + ']'"
                                   :placeholder="'Pilihan ' + String.fromCharCode(64 + i)"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <button type="button" 
                                    @click="removeOption(i)"
                                    x-show="optionCount > 2"
                                    class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </template>
                </div>
                <button type="button" 
                        @click="optionCount++"
                        class="mt-3 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-plus mr-2"></i>Tambah Pilihan
                </button>
                <p class="text-sm text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>Pilih radio button untuk menandai jawaban yang benar
                </p>
            </div>

            <!-- True/False Options -->
            <div x-show="questionType === 'true_false'" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Jawaban yang Benar <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-green-500 transition flex-1">
                        <input type="radio" 
                               name="correct_option" 
                               value="true"
                               class="mr-3 text-green-600 focus:ring-green-500">
                        <span class="font-medium">Benar</span>
                    </label>
                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-red-500 transition flex-1">
                        <input type="radio" 
                               name="correct_option" 
                               value="false"
                               class="mr-3 text-red-600 focus:ring-red-500">
                        <span class="font-medium">Salah</span>
                    </label>
                </div>
            </div>

            <!-- Short Answer -->
            <div x-show="questionType === 'short_answer'" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Jawaban yang Benar <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="correct_answer"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Jawaban yang diharapkan...">
                <p class="text-sm text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>Pencocokan tidak case-sensitive
                </p>
            </div>

            <!-- Essay (no correct answer needed) -->
            <div x-show="questionType === 'essay'" class="mb-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Soal essay akan dinilai secara manual oleh instruktur
                    </p>
                </div>
            </div>

            <!-- Explanation -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Penjelasan (Opsional)
                </label>
                <textarea name="explanation" 
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Penjelasan untuk jawaban yang benar..."></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.question-banks.show', $questionBank) }}" 
                   class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-save mr-2"></i>Simpan Soal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-auto">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Import Soal dari Template</h3>
                <button onclick="document.getElementById('importModal').classList.add('hidden')" 
                        class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Download Template -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-blue-900 mb-2">
                    <i class="fas fa-download mr-2"></i>Download Template
                </h4>
                <p class="text-sm text-blue-800 mb-3">
                    Download template Excel untuk format soal yang benar
                </p>
                <a href="{{ route('admin.question-banks.export-template') }}" 
                   class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-file-excel mr-2"></i>Download Template Excel
                </a>
            </div>

            <!-- Upload Form -->
            <form action="{{ route('admin.question-banks.import-questions', $questionBank) }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Upload File Excel
                    </label>
                    <input type="file" 
                           name="file"
                           accept=".xlsx,.xls,.csv"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           required>
                    <p class="text-sm text-gray-500 mt-1">Format: .xlsx, .xls, atau .csv</p>
                </div>

                <!-- Template Format Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h5 class="font-semibold text-gray-800 mb-2">Format Template:</h5>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• <strong>type:</strong> multiple_choice, true_false, short_answer, atau essay</li>
                        <li>• <strong>question_text:</strong> Teks pertanyaan</li>
                        <li>• <strong>points:</strong> Poin soal (angka)</li>
                        <li>• <strong>option_1, option_2, dst:</strong> Pilihan jawaban (untuk multiple_choice)</li>
                        <li>• <strong>correct_option:</strong> Nomor/teks jawaban yang benar</li>
                        <li>• <strong>explanation:</strong> Penjelasan (opsional)</li>
                    </ul>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" 
                            onclick="document.getElementById('importModal').classList.add('hidden')"
                            class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-upload mr-2"></i>Import Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
