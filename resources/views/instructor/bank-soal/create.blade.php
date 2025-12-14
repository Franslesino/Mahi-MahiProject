@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.instructor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Tambah Soal ke Bank Soal</h1>
                    <p class="text-gray-600 mt-2">Buat soal yang dapat digunakan kembali dalam berbagai quiz</p>
                </div>
                <a href="{{ route('instructor.bank-soal.index') }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <p class="font-semibold">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('instructor.bank-soal.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow">
            @csrf

            <!-- Kategori -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-tag mr-2"></i>Kategori
                </label>
                <input type="text" 
                       name="kategori" 
                       value="{{ old('kategori') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Contoh: matematika, pemrograman, final_quiz">
            </div>

            <!-- Tipe Soal -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-list mr-2"></i>Tipe Soal <span class="text-red-500">*</span>
                </label>
                <select name="tipe_soal" 
                        id="tipe_soal"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required>
                    <option value="multiple_choice" {{ old('tipe_soal') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                    <option value="essay" {{ old('tipe_soal') == 'essay' ? 'selected' : '' }}>Essay</option>
                    <option value="true_false" {{ old('tipe_soal') == 'true_false' ? 'selected' : '' }}>True/False</option>
                </select>
            </div>

            <!-- Pertanyaan -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-question-circle mr-2"></i>Pertanyaan <span class="text-red-500">*</span>
                </label>
                <textarea name="pertanyaan" 
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Masukkan pertanyaan..."
                          required>{{ old('pertanyaan') }}</textarea>
            </div>

            <!-- Options for Multiple Choice -->
            <div id="options_container" class="mb-4" style="display: none;">
                <label class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-list-ul mr-2"></i>Pilihan Jawaban <span class="text-red-500">*</span>
                </label>
                <div id="options_list" class="space-y-3">
                    <!-- Options will be added here -->
                </div>
                <button type="button" 
                        id="add_option_btn"
                        class="mt-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i>Tambah Opsi
                </button>
            </div>

            <!-- True/False Options -->
            <div id="true_false_container" class="mb-4" style="display: none;">
                <label class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-check-circle mr-2"></i>Jawaban Benar <span class="text-red-500">*</span>
                </label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="radio" name="correct_answer" value="true" class="mr-2" {{ old('correct_answer') == 'true' ? 'checked' : '' }}>
                        <span>True</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="correct_answer" value="false" class="mr-2" {{ old('correct_answer') == 'false' ? 'checked' : '' }}>
                        <span>False</span>
                    </label>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Simpan Soal
                </button>
                <a href="{{ route('instructor.bank-soal.index') }}" 
                   class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    let optionCount = 0;

    function updateQuestionType() {
        const tipeSoal = document.getElementById('tipe_soal').value;
        const optionsContainer = document.getElementById('options_container');
        const trueFalseContainer = document.getElementById('true_false_container');

        if (tipeSoal === 'multiple_choice') {
            optionsContainer.style.display = 'block';
            trueFalseContainer.style.display = 'none';
            // Add default 2 options if none exist
            if (optionCount === 0) {
                addOption();
                addOption();
            }
        } else if (tipeSoal === 'true_false') {
            optionsContainer.style.display = 'none';
            trueFalseContainer.style.display = 'block';
        } else {
            optionsContainer.style.display = 'none';
            trueFalseContainer.style.display = 'none';
        }
    }

    function addOption() {
        const optionsList = document.getElementById('options_list');
        const optionDiv = document.createElement('div');
        optionDiv.className = 'flex items-center gap-3 p-3 bg-gray-50 rounded-lg';
        optionDiv.innerHTML = `
            <input type="text" 
                   name="options[${optionCount}][teks_opsi]" 
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="Teks opsi jawaban"
                   required>
            <label class="flex items-center whitespace-nowrap">
                <input type="checkbox" 
                       name="options[${optionCount}][is_benar]" 
                       value="1"
                       class="mr-2">
                <span class="text-sm">Jawaban Benar</span>
            </label>
            <button type="button" 
                    onclick="this.parentElement.remove()"
                    class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                <i class="fas fa-trash"></i>
            </button>
        `;
        optionsList.appendChild(optionDiv);
        optionCount++;
    }

    // Initialize
    document.getElementById('tipe_soal').addEventListener('change', updateQuestionType);
    document.getElementById('add_option_btn').addEventListener('click', addOption);

    // Set initial state
    updateQuestionType();
</script>
@endsection
