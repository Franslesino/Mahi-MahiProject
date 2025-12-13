@php
    $isAdmin = auth()->user()->role === 'admin';
    $layout = $isAdmin ? 'layouts.admin' : 'layouts.instructor';
    $routePrefix = $isAdmin ? 'admin.assignments' : 'instructor.assignments';
@endphp

@extends($layout)

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">{{ $assignment->title }}</h2>
        <p class="text-gray-600 mt-2">Tambah dan kelola soal untuk assignment ini</p>
    </div>

    <!-- Quiz Settings Card -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-cog text-blue-600"></i>
                Pengaturan Quiz
            </h3>
            <button type="button" onclick="toggleSettingsForm()" class="text-sm px-3 py-1.5 border rounded-lg text-gray-700 hover:bg-gray-50" id="toggleSettingsBtn">
                Sembunyikan
            </button>
        </div>
        <form action="{{ route($routePrefix . '.update', $assignment) }}" method="POST" class="space-y-4" id="settingsForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="kursus_id" value="{{ $assignment->kursus_id }}">
            <input type="hidden" name="materi_id" value="{{ $assignment->materi_id }}">
            <input type="hidden" name="title" value="{{ $assignment->title }}">
            <input type="hidden" name="description" value="{{ $assignment->description }}">
            <input type="hidden" name="type" value="{{ $assignment->type }}">
            <input type="hidden" name="start_date" value="{{ $assignment->start_date?->format('Y-m-d\TH:i') }}">
            <input type="hidden" name="due_date" value="{{ $assignment->due_date?->format('Y-m-d\TH:i') }}">
            <input type="hidden" name="show_results_immediately" value="{{ $assignment->show_results_immediately ? '1' : '0' }}">
            <input type="hidden" name="allow_multiple_attempts" value="{{ $assignment->allow_multiple_attempts ? '1' : '0' }}">
            <input type="hidden" name="max_attempts" value="{{ $assignment->max_attempts }}">
            <input type="hidden" name="randomize_questions" value="{{ $assignment->randomize_questions ? '1' : '0' }}">

            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock text-blue-600 mr-1"></i>
                        Batas Waktu Pengerjaan (Menit)
                    </label>
                    <input type="number" 
                           name="time_limit" 
                           value="{{ old('time_limit', $assignment->time_limit) }}"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                           placeholder="Kosongkan jika tidak ada batas waktu">
                    <p class="text-xs text-gray-500 mt-1">
                        Timer akan berjalan mundur dan quiz otomatis tersubmit saat waktu habis
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-hourglass-half text-green-600 mr-1"></i>
                        Durasi (Menit)
                    </label>
                    <input type="number" 
                           name="duration_minutes" 
                           value="{{ old('duration_minutes', $assignment->duration_minutes) }}"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                           placeholder="0 = Tanpa batas">
                    <p class="text-xs text-gray-500 mt-1">
                        Estimasi durasi pengerjaan
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-trophy text-yellow-600 mr-1"></i>
                        Nilai Minimal Lulus (%)
                    </label>
                    <input type="number" 
                           name="passing_score" 
                           value="{{ old('passing_score', $assignment->passing_score) }}"
                           min="0"
                           max="100"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                           required>
                    <p class="text-xs text-gray-500 mt-1">
                        Nilai minimum untuk lulus quiz
                    </p>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    <!-- Create Question Inline -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6" id="createQuestionCard">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-pen text-purple-600"></i>
                Buat Soal Baru
            </h3>
            <button type="button" onclick="toggleCreateForm()" class="text-sm px-3 py-1.5 border rounded-lg text-gray-700 hover:bg-gray-50" id="toggleCreateFormBtn">
                Sembunyikan
            </button>
        </div>
        <form action="{{ route($routePrefix . '.store-question', $assignment) }}" method="POST" class="space-y-4" id="createQuestionForm">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Soal (opsional)</label>
                    <select name="question_bank_id" class="w-full border rounded-lg px-3 py-2" id="questionBankSelect">
                        <option value="">Bank otomatis: Bank Kursus {{ $assignment->kursus->judul }}</option>
                        @foreach($ownedBanks as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->title }} ({{ $bank->questions_count }} soal)</option>
                        @endforeach
                    </select>
                    <div class="mt-2 flex items-center gap-2 text-sm text-gray-700">
                        <input type="hidden" name="save_to_bank" value="0">
                        <input type="checkbox" name="save_to_bank" value="1" id="saveToBankCheckbox" class="h-4 w-4 text-purple-600" checked>
                        <label for="saveToBankCheckbox">Simpan ke Bank Soal</label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1" id="saveToBankHint">Jika dimatikan, soal hanya tersimpan di assignment ini.</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Soal</label>
                        <select name="type" id="questionType" class="w-full border rounded-lg px-3 py-2">
                            <option value="multiple_choice">Pilihan Ganda</option>
                            <option value="true_false">Benar / Salah</option>
                            <option value="short_answer">Jawaban Singkat</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Poin</label>
                        <input type="number" name="points" value="1" min="1" class="w-full border rounded-lg px-3 py-2" required>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teks Soal</label>
                <textarea name="question_text" rows="3" class="w-full border rounded-lg px-3 py-2" required></textarea>
            </div>

            <div id="multipleChoiceFields" class="space-y-3">
                <label class="block text-sm font-medium text-gray-700">Opsi Jawaban</label>
                @for($i = 0; $i < 4; $i++)
                <div class="flex items-center gap-3">
                    <input type="text" name="options[{{ $i }}][text]" class="flex-1 border rounded-lg px-3 py-2" placeholder="Opsi {{ $i + 1 }}">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="options[{{ $i }}][is_correct]" value="1" class="h-4 w-4 text-blue-600">
                        Jawaban Benar
                    </label>
                </div>
                @endfor
            </div>

            <div id="trueFalseFields" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jawaban Benar</label>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="correct_answer" value="true" class="h-4 w-4 text-blue-600" checked>
                        Benar
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="correct_answer" value="false" class="h-4 w-4 text-blue-600">
                        Salah
                    </label>
                </div>
            </div>

            <div id="shortAnswerFields" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jawaban Singkat</label>
                <input type="text" name="correct_answer" class="w-full border rounded-lg px-3 py-2" placeholder="Masukkan jawaban kunci">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Penjelasan (opsional)</label>
                <textarea name="explanation" rows="2" class="w-full border rounded-lg px-3 py-2" placeholder="Berikan penjelasan atau referensi jawaban"></textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                    <i class="fas fa-save mr-2"></i>Simpan & Tambah ke Quiz
                </button>
            </div>
        </form>
    </div>

    <!-- Add Questions from Bank -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6" id="bankSection">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-plus-circle text-blue-600"></i>
                Tambah Soal dari Bank Soal
            </h3>
            <button type="button" onclick="toggleBankSection()" class="text-sm px-3 py-1.5 border rounded-lg text-gray-700 hover:bg-gray-50" id="toggleBankSectionBtn">
                Sembunyikan
            </button>
        </div>

        <form action="{{ route($routePrefix . '.add-questions', $assignment) }}" method="POST" id="addQuestionsForm">
            @csrf

            <div id="bankSectionBody">
                @if($questionBanks->isEmpty())
                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                        <i class="fas fa-folder-open text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-600">Belum ada bank soal tersedia</p>
                        <a href="{{ route('instructor.question-banks.create') }}" 
                           class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                            Buat Bank Soal
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($questionBanks as $bank)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $bank->title }}</h4>
                                    @if($bank->category)
                                    <span class="inline-block mt-1 px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
                                        {{ $bank->category }}
                                    </span>
                                    @endif
                                    <p class="text-sm text-gray-600 mt-1">{{ $bank->questions_count }} soal</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" 
                                            onclick="toggleBank('bank-{{ $bank->id }}')"
                                            class="px-3 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 text-sm">
                                        <i class="fas fa-chevron-down"></i> Lihat Soal
                                    </button>
                                    <button type="button"
                                            onclick="selectBankQuestions('bank-{{ $bank->id }}')"
                                            class="px-3 py-1 bg-green-50 text-green-700 rounded hover:bg-green-100 text-sm">
                                        Pilih Semua
                                    </button>
                                </div>
                            </div>

                            <div id="bank-{{ $bank->id }}" class="hidden mt-4 space-y-2 pl-4 border-l-2 border-blue-200">
                                @foreach($bank->questions as $question)
                                <label class="flex items-start gap-3 p-3 bg-gray-50 rounded hover:bg-gray-100 cursor-pointer">
                                    <input type="checkbox" 
                                           name="question_ids[]" 
                                           value="{{ $question->id }}"
                                           {{ $assignment->questions->contains($question->id) ? 'disabled checked' : '' }}
                                           class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded font-medium">
                                                {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                                            </span>
                                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded font-medium">
                                                {{ $question->points }} poin
                                            </span>
                                            @if($assignment->questions->contains($question->id))
                                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded font-medium">
                                                <i class="fas fa-check"></i> Sudah ditambahkan
                                            </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-700">{{ Str::limit($question->question_text, 100) }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach

                        <button type="submit" 
                                class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            <i class="fas fa-plus mr-2"></i>Tambah Soal yang Dipilih
                        </button>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Current Questions -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-list text-blue-600 mr-2"></i>
            Soal dalam Assignment ({{ $assignment->questions->count() }})
        </h3>

        @if($assignment->questions->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500">Belum ada soal ditambahkan ke assignment ini</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($assignment->questions as $index => $question)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded font-medium">
                                    #{{ $index + 1 }}
                                </span>
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded font-medium">
                                    {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                                </span>
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded font-medium">
                                    {{ $question->pivot->points ?? $question->points }} poin
                                </span>
                            </div>
                            <p class="text-gray-800">{{ $question->question_text }}</p>
                        </div>
                        <form action="{{ route($routePrefix . '.remove-question', [$assignment, $question]) }}" 
                              method="POST"
                              data-confirm="Yakin ingin menghapus soal ini dari assignment?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-3 py-1 text-red-600 hover:bg-red-50 rounded transition text-sm">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>

                    @if($question->type == 'multiple_choice' || $question->type == 'true_false')
                        <div class="space-y-1 pl-4 mt-2">
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
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="font-semibold text-gray-800 mb-1">Status Assignment</h4>
                <p class="text-sm text-gray-600">
                    @if($assignment->is_published)
                        <span class="text-green-600"><i class="fas fa-check-circle"></i> Assignment sudah dipublikasi</span>
                    @else
                        <span class="text-gray-600"><i class="fas fa-clock"></i> Assignment masih draft</span>
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                <button type="button"
                        onclick="handleBack()"
                        class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </button>
                @if($assignment->is_published)
                <form action="{{ route($routePrefix . '.unpublish', $assignment) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-medium">
                        <i class="fas fa-eye-slash mr-2"></i>Unpublish
                    </button>
                </form>
                @else
                <form action="{{ route($routePrefix . '.publish', $assignment) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium"
                            {{ $assignment->questions->isEmpty() ? 'disabled' : '' }}>
                        <i class="fas fa-check mr-2"></i>Publish Assignment
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function toggleBank(id) {
    const element = document.getElementById(id);
    element.classList.toggle('hidden');
}

const createFormStorageKey = 'assignment_{{ $assignment->id }}_create_form_hidden';
const bankSectionStorageKey = 'assignment_{{ $assignment->id }}_bank_section_hidden';
const settingsFormStorageKey = 'assignment_{{ $assignment->id }}_settings_form_hidden';

function toggleSettingsForm() {
    const form = document.getElementById('settingsForm');
    const btn = document.getElementById('toggleSettingsBtn');
    const hidden = form.classList.toggle('hidden');
    btn.textContent = hidden ? 'Tampilkan' : 'Sembunyikan';
    localStorage.setItem(settingsFormStorageKey, hidden ? '1' : '0');
}

function toggleBankSection() {
    const body = document.getElementById('bankSectionBody');
    const btn = document.getElementById('toggleBankSectionBtn');
    const hidden = body.classList.toggle('hidden');
    btn.textContent = hidden ? 'Tampilkan' : 'Sembunyikan';
    localStorage.setItem(bankSectionStorageKey, hidden ? '1' : '0');
}

function selectBankQuestions(containerId, checked = true) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.classList.remove('hidden');
    container.querySelectorAll('input[type="checkbox"]:not(:disabled)').forEach(el => {
        el.checked = checked;
    });
}

function toggleQuestionFields() {
    const type = document.getElementById('questionType').value;
    const isMultipleChoice = type === 'multiple_choice';
    const isTrueFalse = type === 'true_false';
    const isShortAnswer = type === 'short_answer';

    document.getElementById('multipleChoiceFields').classList.toggle('hidden', !isMultipleChoice);
    document.getElementById('trueFalseFields').classList.toggle('hidden', !isTrueFalse);
    document.getElementById('shortAnswerFields').classList.toggle('hidden', !isShortAnswer);

    document.querySelectorAll('#multipleChoiceFields input').forEach(el => el.disabled = !isMultipleChoice);
    document.querySelectorAll('#trueFalseFields input').forEach(el => el.disabled = !isTrueFalse);
    document.querySelectorAll('#shortAnswerFields input').forEach(el => el.disabled = !isShortAnswer);
}

function toggleCreateForm() {
    const card = document.getElementById('createQuestionCard');
    const form = document.getElementById('createQuestionForm');
    const btn = document.getElementById('toggleCreateFormBtn');
    const hidden = form.classList.toggle('hidden');
    btn.textContent = hidden ? 'Tampilkan' : 'Sembunyikan';
    localStorage.setItem(createFormStorageKey, hidden ? '1' : '0');
}

document.addEventListener('DOMContentLoaded', () => {
    const typeSelect = document.getElementById('questionType');
    typeSelect.addEventListener('change', toggleQuestionFields);
    toggleQuestionFields();

    const saveToBankCheckbox = document.getElementById('saveToBankCheckbox');
    const bankSelect = document.getElementById('questionBankSelect');
    const saveHint = document.getElementById('saveToBankHint');
    const syncBankToggle = () => {
        const enabled = saveToBankCheckbox.checked;
        bankSelect.disabled = !enabled;
        saveHint.textContent = enabled
            ? 'Soal akan tersimpan di bank soal yang dipilih.'
            : 'Soal hanya tersimpan di assignment ini (tidak masuk bank soal).';
    };
    saveToBankCheckbox.addEventListener('change', syncBankToggle);
    syncBankToggle();

    // Restore toggle states from localStorage
    const settingsFormHidden = localStorage.getItem(settingsFormStorageKey);
    if (settingsFormHidden === '1') {
        const form = document.getElementById('settingsForm');
        form.classList.add('hidden');
        document.getElementById('toggleSettingsBtn').textContent = 'Tampilkan';
    }

    const createFormHidden = localStorage.getItem(createFormStorageKey);
    if (createFormHidden === '1' || createFormHidden === null) {
        const form = document.getElementById('createQuestionForm');
        form.classList.add('hidden');
        document.getElementById('toggleCreateFormBtn').textContent = 'Tampilkan';
        localStorage.setItem(createFormStorageKey, '1');
    }

    const bankHidden = localStorage.getItem(bankSectionStorageKey);
    if (bankHidden === '1' || bankHidden === null) {
        const body = document.getElementById('bankSectionBody');
        body.classList.add('hidden');
        document.getElementById('toggleBankSectionBtn').textContent = 'Tampilkan';
        localStorage.setItem(bankSectionStorageKey, '1');
    }
});

function handleBack() {
    // Redirect langsung ke halaman course detail
    @if($isAdmin)
        window.location.href = "{{ route('admin.courses.detail', $assignment->kursus_id) }}";
    @else
        window.location.href = "{{ route('instructor.courses.show', $assignment->kursus_id) }}";
    @endif
}
</script>
@endsection
