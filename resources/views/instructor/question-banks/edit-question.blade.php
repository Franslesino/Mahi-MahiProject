@extends('layouts.instructor')

@section('content')
@php
    $question->loadMissing('options');
    $type = old('type', $question->type);
    $points = old('points', $question->points);
    $questionText = old('question_text', $question->question_text);
    $explanation = old('explanation', $question->explanation);
    $correctAnswer = old('correct_answer', $question->correct_answer);

    $options = old('options', $question->options->sortBy('order')->pluck('option_text')->values()->toArray());
    $mcCorrect = old('correct_option');
    if ($mcCorrect === null && $type === 'multiple_choice') {
        $correctOption = $question->options->firstWhere('is_correct', true);
        $mcCorrect = $correctOption ? $correctOption->order : null;
    }
    $tfCorrect = old('correct_option');
    if ($tfCorrect === null && $type === 'true_false') {
        $tfCorrect = ($question->options->firstWhere('is_correct', true)?->option_text === 'Benar') ? 'true' : 'false';
    }
@endphp

<div class="p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('instructor.question-banks.show', $questionBank) }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-3xl font-bold text-gray-800">Edit Soal</h2>
            </div>
            <p class="text-gray-600">{{ $questionBank->title }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle mt-0.5"></i>
                <div>
                    <p class="font-semibold mb-1">Terdapat kesalahan:</p>
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div x-data="questionEditor()" x-init="initData()" class="bg-white rounded-xl shadow-sm p-8">
        <form method="POST" action="{{ route('instructor.question-banks.questions.update', [$questionBank, $question]) }}">
            @csrf
            @method('PUT')

            <input type="hidden" id="initial-type" value="{{ $type }}">
            <input type="hidden" id="initial-points" value="{{ $points }}">
            <input type="hidden" id="initial-question-text" value="{{ $questionText }}">
            <input type="hidden" id="initial-explanation" value="{{ $explanation }}">
            <input type="hidden" id="initial-correct-answer" value="{{ $correctAnswer }}">
            <input type="hidden" id="initial-options" value='@json($options)'>
            <input type="hidden" id="initial-mc-correct" value="{{ $mcCorrect }}">
            <input type="hidden" id="initial-tf-correct" value="{{ $tfCorrect }}">

            <!-- Question Type -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tipe Soal <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <template x-for="item in types" :key="item.value">
                        <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-blue-500 transition"
                               :class="type === item.value ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                            <input type="radio"
                                   name="type"
                                   :value="item.value"
                                   x-model="type"
                                   class="sr-only"
                                   required>
                            <div class="text-center w-full">
                                <i :class="item.icon" class="text-2xl mb-2" :style="'color:' + item.color"></i>
                                <div class="font-medium text-sm" x-text="item.label"></div>
                            </div>
                        </label>
                    </template>
                </div>
            </div>

            <!-- Question Text -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pertanyaan <span class="text-red-500">*</span>
                </label>
                <textarea name="question_text"
                          rows="4"
                          x-model="questionText"
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
                       x-model="points"
                       min="1"
                       class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       required>
            </div>

            <!-- Multiple Choice Options -->
            <div x-show="type === 'multiple_choice'" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pilihan Jawaban <span class="text-red-500">*</span>
                </label>
                <div class="space-y-3">
                    <template x-for="(opt, index) in options" :key="index">
                        <div class="flex gap-3 items-start">
                            <input type="radio"
                                   name="correct_option"
                                   :value="index + 1"
                                   class="mt-3 text-blue-600 focus:ring-blue-500"
                                   x-model="mcCorrect">
                            <input type="text"
                                   :name="'options[' + (index + 1) + ']'"
                                   x-model="options[index]"
                                   :placeholder="'Pilihan ' + String.fromCharCode(65 + index)"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                            <button type="button"
                                    @click="removeOption(index)"
                                    x-show="options.length > 2"
                                    class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </template>
                </div>
                <button type="button"
                        @click="addOption()"
                        class="mt-3 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-plus mr-2"></i>Tambah Pilihan
                </button>
                <p class="text-sm text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>Pilih radio button untuk menandai jawaban yang benar.
                </p>
            </div>

            <!-- True/False -->
            <div x-show="type === 'true_false'" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih jawaban yang benar <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="correct_option" value="true" x-model="tfCorrect" class="text-blue-600 focus:ring-blue-500">
                        <span>Benar</span>
                    </label>
                    <label class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="correct_option" value="false" x-model="tfCorrect" class="text-blue-600 focus:ring-blue-500">
                        <span>Salah</span>
                    </label>
                </div>
            </div>

            <!-- Short Answer -->
            <div x-show="type === 'short_answer'" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kunci Jawaban (opsional)
                </label>
                <input type="text"
                       name="correct_answer"
                       x-model="correctAnswer"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Isi kunci jawaban jika ada">
            </div>

            <!-- Explanation -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Penjelasan (opsional)</label>
                <textarea name="explanation"
                          rows="3"
                          x-model="explanation"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Penjelasan tentang jawaban yang benar..."></textarea>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('instructor.question-banks.show', $questionBank) }}"
                   class="px-5 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function questionEditor() {
        return {
            type: 'multiple_choice',
            questionText: '',
            points: 1,
            explanation: '',
            correctAnswer: '',
            options: ['',''],
            mcCorrect: 1,
            tfCorrect: 'true',
            types: [
                { value: 'multiple_choice', label: 'Pilihan Ganda', icon: 'fas fa-list-ul', color: '#2563eb' },
                { value: 'true_false', label: 'Benar/Salah', icon: 'fas fa-check-circle', color: '#059669' },
                { value: 'short_answer', label: 'Jawaban Singkat', icon: 'fas fa-keyboard', color: '#8b5cf6' },
            ],
            initData() {
                this.type = document.getElementById('initial-type').value || 'multiple_choice';
                this.questionText = document.getElementById('initial-question-text').value || '';
                this.points = Number(document.getElementById('initial-points').value || 1);
                this.explanation = document.getElementById('initial-explanation').value || '';
                this.correctAnswer = document.getElementById('initial-correct-answer').value || '';

                const initOptions = JSON.parse(document.getElementById('initial-options').value || '[]');
                if (initOptions.length > 0) {
                    this.options = initOptions;
                }
                if (this.options.length < 2) {
                    this.options = ['',''];
                }

                const mcCorrect = document.getElementById('initial-mc-correct').value;
                this.mcCorrect = mcCorrect || 1;
                const tfCorrect = document.getElementById('initial-tf-correct').value;
                this.tfCorrect = tfCorrect || 'true';
            },
            addOption() {
                this.options.push('');
            },
            removeOption(index) {
                if (this.options.length > 2) {
                    this.options.splice(index, 1);
                    if (Number(this.mcCorrect) > this.options.length) {
                        this.mcCorrect = this.options.length;
                    }
                }
            },
        };
    }
</script>
@endsection
