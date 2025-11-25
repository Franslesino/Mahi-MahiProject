@extends('layouts.instructor')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">{{ $questionBank->title }}</h2>
            <p class="text-gray-600 mt-2">{{ $questionBank->description }}</p>
            @if($questionBank->category)
            <span class="inline-block mt-2 px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded-full font-medium">
                {{ $questionBank->category }}
            </span>
            @endif
        </div>
        <div class="flex gap-2">
            @if($questionBank->created_by == auth()->id())
            <a href="{{ route('instructor.question-banks.create-question', $questionBank) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                <i class="fas fa-plus mr-2"></i>Tambah Soal
            </a>
            <a href="{{ route('instructor.question-banks.export-questions', $questionBank) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                <i class="fas fa-file-export mr-2"></i>Export Soal
            </a>
            <a href="{{ route('instructor.question-banks.edit', $questionBank) }}" 
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                <i class="fas fa-edit mr-2"></i>Edit Bank
            </a>
            @else
            <a href="{{ route('instructor.question-banks.export-questions', $questionBank) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                <i class="fas fa-file-export mr-2"></i>Export Soal
            </a>
            @endif
            <a href="{{ route('instructor.question-banks.index') }}" 
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Questions List -->
    @if($questionBank->created_by == auth()->id())
    <div x-data="{ showForm: false }" class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-list text-blue-600 mr-2"></i>
                Daftar Soal ({{ $questionBank->questions->count() }})
            </h3>
        </div>

    <!-- Legacy inline form - hidden by default, keeping for backward compatibility -->
    <div x-show="showForm" x-cloak class="border-t pt-6 mb-6">
        <form action="{{ route('instructor.question-banks.questions.store', $questionBank) }}" method="POST" id="questionForm">
            @csrf

            <div class="space-y-4">
                <!-- Question Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Soal</label>
                    <select name="type" 
                            id="questionType"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            required>
                        <option value="multiple_choice" selected>Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="essay">Essay</option>
                        <option value="short_answer">Short Answer</option>
                    </select>
                </div>

                <!-- Question Text -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pertanyaan <span class="text-red-500">*</span></label>
                    <textarea name="question_text" 
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                              placeholder="Tulis pertanyaan di sini..."
                              required>{{ old('question_text') }}</textarea>
                </div>

                <!-- Points -->
                <div class="w-32">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Poin</label>
                    <input type="number" 
                           name="points" 
                           value="{{ old('points', 1) }}"
                           min="1"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                           required>
                </div>

                <!-- Options Container (for multiple choice and true/false) -->
                <div id="optionsContainer">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilihan Jawaban</label>
                    <div id="optionsList" class="space-y-2">
                        <!-- Options will be added here dynamically -->
                    </div>
                    <button type="button" 
                            id="addOption"
                            class="mt-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                        <i class="fas fa-plus mr-1"></i> Tambah Pilihan
                    </button>
                </div>

                <!-- Correct Answer (for essay/short answer) -->
                <div id="correctAnswerContainer" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kunci Jawaban (opsional)</label>
                    <textarea name="correct_answer" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                              placeholder="Tulis kunci jawaban untuk referensi...">{{ old('correct_answer') }}</textarea>
                </div>

                <!-- Explanation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Penjelasan (opsional)</label>
                    <textarea name="explanation" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                              placeholder="Penjelasan tentang jawaban yang benar...">{{ old('explanation') }}</textarea>
                </div>

                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-save mr-2"></i>Simpan Soal
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Questions List -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-list text-blue-600 mr-2"></i>
            Daftar Soal ({{ $questionBank->questions->count() }})
        </h3>

        @if($questionBank->questions->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500">Belum ada soal dalam bank ini</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($questionBank->questions as $index => $question)
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <span class="inline-block px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded font-medium mr-2">
                                #{{ $index + 1 }}
                            </span>
                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded font-medium">
                                {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                            </span>
                            <span class="inline-block px-2 py-1 bg-green-100 text-green-700 text-xs rounded font-medium">
                                {{ $question->points }} poin
                            </span>
                        </div>
                        @if($questionBank->created_by == auth()->id())
                        <form action="{{ route('instructor.question-banks.questions.destroy', [$questionBank, $question]) }}" 
                              method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-3 py-1 text-red-600 hover:bg-red-50 rounded transition text-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>

                    <p class="text-gray-800 mb-3">{{ $question->question_text }}</p>

                    @if($question->type == 'multiple_choice' || $question->type == 'true_false')
                        <div class="space-y-2">
                            @foreach($question->options as $option)
                            <div class="flex items-center gap-2 text-sm">
                                @if($option->is_correct)
                                    <i class="fas fa-check-circle text-green-600"></i>
                                    <span class="text-green-700 font-medium">{{ $option->option_text }}</span>
                                @else
                                    <i class="far fa-circle text-gray-400"></i>
                                    <span class="text-gray-600">{{ $option->option_text }}</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @endif

                    @if($question->explanation)
                    <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-900"><strong>Penjelasan:</strong> {{ $question->explanation }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const questionType = document.getElementById('questionType');
    const optionsContainer = document.getElementById('optionsContainer');
    const correctAnswerContainer = document.getElementById('correctAnswerContainer');
    const optionsList = document.getElementById('optionsList');
    const addOptionBtn = document.getElementById('addOption');

    let optionCount = 0;

    function updateFormByType() {
        const type = questionType.value;
        
        if (type === 'multiple_choice') {
            optionsContainer.style.display = 'block';
            correctAnswerContainer.style.display = 'none';
            if (optionCount === 0) {
                addOption();
                addOption();
                addOption();
                addOption();
            }
        } else if (type === 'true_false') {
            optionsContainer.style.display = 'block';
            correctAnswerContainer.style.display = 'none';
            optionsList.innerHTML = '';
            optionCount = 0;
            addTrueFalseOptions();
        } else {
            optionsContainer.style.display = 'none';
            correctAnswerContainer.style.display = 'block';
            optionsList.innerHTML = '';
            optionCount = 0;
        }
    }

    function addOption() {
        optionCount++;
        const optionDiv = document.createElement('div');
        optionDiv.className = 'flex gap-2';
        optionDiv.innerHTML = `
            <input type="text" 
                   name="options[${optionCount}][text]" 
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                   placeholder="Pilihan ${optionCount}"
                   required>
            <label class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                <input type="checkbox" 
                       name="options[${optionCount}][is_correct]" 
                       value="1"
                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span class="text-sm text-gray-700">Benar</span>
            </label>
            <button type="button" 
                    onclick="this.parentElement.remove()"
                    class="px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100">
                <i class="fas fa-trash"></i>
            </button>
        `;
        optionsList.appendChild(optionDiv);
    }

    function addTrueFalseOptions() {
        optionCount = 0;
        
        ['Benar', 'Salah'].forEach((text, index) => {
            optionCount++;
            const optionDiv = document.createElement('div');
            optionDiv.className = 'flex gap-2';
            optionDiv.innerHTML = `
                <input type="text" 
                       name="options[${optionCount}][text]" 
                       value="${text}"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                       readonly
                       required>
                <label class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" 
                           name="true_false_correct" 
                           value="${optionCount}"
                           onchange="document.querySelectorAll('[name^=\\'options\\'][name$=\\'[is_correct]\\']').forEach(cb => cb.checked = false); document.querySelector('[name=\\'options[${optionCount}][is_correct]\\']').checked = true;"
                           class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Benar</span>
                </label>
                <input type="hidden" name="options[${optionCount}][is_correct]" value="0">
            `;
            optionsList.appendChild(optionDiv);
        });
    }

    questionType.addEventListener('change', updateFormByType);
    addOptionBtn.addEventListener('click', addOption);

    // Initialize
    updateFormByType();
});
</script>
@endsection
