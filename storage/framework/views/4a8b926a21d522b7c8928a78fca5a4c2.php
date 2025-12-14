<?php $__env->startSection('content'); ?>
<div class="p-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="<?php echo e(route('instructor.courses.final-quiz.edit', $kursus->id)); ?>" class="text-blue-600 hover:text-blue-700 mb-2 inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Pengaturan Final Quiz</span>
        </a>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Buat Final Quiz Baru</h2>
        <p class="text-gray-600 mt-1"><?php echo e($kursus->judul); ?></p>
        <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3 inline-flex items-start gap-2">
            <i class="fas fa-info-circle text-yellow-600 mt-0.5"></i>
            <p class="text-sm text-yellow-800">
                <strong>Catatan:</strong> Setiap kursus hanya dapat memiliki 1 final quiz. Setelah dibuat, Anda tidak dapat membuat quiz baru lagi, tetapi dapat menambah/mengurangi soal.
            </p>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if($errors->any()): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <ul class="mb-0 ml-4">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="bg-teal-600 text-white px-6 py-4 rounded-t-lg">
            <h5 class="text-lg font-semibold mb-0"><i class="fas fa-graduation-cap"></i> Informasi Quiz</h5>
        </div>
        <div class="p-6">
            <form action="<?php echo e(route('instructor.courses.final-quiz.store-quiz', $kursus->id)); ?>" method="POST" id="createQuizForm">
                <?php echo csrf_field(); ?>

                <!-- Judul Quiz -->
                <div class="mb-6">
                    <label for="judul_quiz" class="block text-sm font-bold text-gray-700 mb-2">
                        Judul Quiz <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="judul_quiz" name="judul_quiz" 
                           value="<?php echo e(old('judul_quiz')); ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 <?php $__errorArgs = ['judul_quiz'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="Contoh: Final Quiz - Dasar Programming">
                    <?php $__errorArgs = ['judul_quiz'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Durasi Quiz -->
                    <div>
                        <label for="durasi_quiz" class="block text-sm font-bold text-gray-700 mb-2">
                            Durasi Quiz (menit)
                        </label>
                        <input type="number" id="durasi_quiz" name="durasi_quiz" 
                               value="<?php echo e(old('durasi_quiz', 60)); ?>" min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 <?php $__errorArgs = ['durasi_quiz'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="60">
                        <small class="text-gray-600 text-sm">Kosongkan jika tidak ada batas waktu</small>
                        <?php $__errorArgs = ['durasi_quiz'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Kesempatan Mengerjakan -->
                    <div>
                        <label for="kesempatan_mengerjakan" class="block text-sm font-bold text-gray-700 mb-2">
                            Maksimal Percobaan <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="kesempatan_mengerjakan" name="kesempatan_mengerjakan" 
                               value="<?php echo e(old('kesempatan_mengerjakan', 3)); ?>" required
                               min="1" max="10"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 <?php $__errorArgs = ['kesempatan_mengerjakan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <small class="text-gray-600 text-sm">1-10 kali</small>
                        <?php $__errorArgs = ['kesempatan_mengerjakan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Passing Grade -->
                    <div>
                        <label for="passing_grade" class="block text-sm font-bold text-gray-700 mb-2">
                            Nilai Minimal Lulus (%) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="passing_grade" name="passing_grade" 
                               value="<?php echo e(old('passing_grade', 70)); ?>" required
                               min="0" max="100" step="0.01"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 <?php $__errorArgs = ['passing_grade'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <small class="text-gray-600 text-sm">0-100%</small>
                        <?php $__errorArgs = ['passing_grade'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <!-- Import Soal dari Question Banks -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-bold text-gray-700">
                            Soal untuk Quiz
                        </label>
                    </div>

                    <div class="bg-gradient-to-r from-purple-50 to-blue-50 border-2 border-purple-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-700 mb-2">
                                    <i class="fas fa-info-circle text-purple-600"></i>
                                    Pilih soal dari Question Banks untuk quiz ini
                                </p>
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="px-3 py-1 bg-white rounded-md font-bold text-purple-700" id="selectedCount">0</span>
                                    <span class="text-gray-600">soal dipilih</span>
                                </div>
                            </div>
                            <button type="button" onclick="openQuestionModal()" 
                                    class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition shadow-md hover:shadow-lg">
                                <i class="fas fa-folder-open"></i> Pilih Soal dari Bank
                            </button>
                        </div>
                    </div>

                    <!-- Selected Questions Preview -->
                    <div id="selectedQuestionsPreview" class="mt-4 hidden">
                        <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-600"></i>
                            Soal yang Dipilih
                        </h4>
                        <div id="selectedQuestionsList" class="space-y-2 max-h-60 overflow-y-auto bg-gray-50 rounded-lg p-3 border-2 border-gray-200">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>

                    <?php if($questionBanks->count() === 0): ?>
                        <div class="mt-4 bg-yellow-50 border-2 border-yellow-200 text-yellow-700 px-6 py-4 rounded-lg flex items-center gap-3">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Belum ada Question Banks tersedia. 
                            <a href="<?php echo e(route('instructor.question-banks.create')); ?>" target="_blank" class="text-blue-600 hover:underline font-semibold">Buat Question Bank baru</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Modal Pilih Soal -->
                <div id="questionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col">
                        <!-- Modal Header -->
                        <div class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-4 rounded-t-2xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-folder-open text-2xl"></i>
                                <h3 class="text-xl font-bold">Pilih Soal dari Question Banks</h3>
                            </div>
                            <button type="button" onclick="closeQuestionModal()" class="text-white hover:text-gray-200 transition">
                                <i class="fas fa-times text-2xl"></i>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="flex-1 overflow-hidden flex flex-col p-6">
                            <!-- Search & Controls -->
                            <div class="mb-4">
                                <input type="text" id="modalSearch" placeholder="🔍 Cari soal berdasarkan teks pertanyaan..." 
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 shadow-sm"
                                       onkeyup="filterModal()">
                            </div>

                            <div class="flex flex-wrap gap-2 mb-4">
                                <button type="button" onclick="selectAllModal()" class="px-4 py-2 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition font-semibold">
                                    <i class="fas fa-check-double"></i> Pilih Semua
                                </button>
                                <button type="button" onclick="deselectAllModal()" class="px-4 py-2 text-sm bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition font-semibold">
                                    <i class="fas fa-times"></i> Batal Pilih
                                </button>
                                <button type="button" onclick="expandAllModal()" class="px-4 py-2 text-sm bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition font-semibold">
                                    <i class="fas fa-chevron-down"></i> Buka Semua
                                </button>
                                <button type="button" onclick="collapseAllModal()" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-semibold">
                                    <i class="fas fa-chevron-up"></i> Tutup Semua
                                </button>
                            </div>

                            <!-- Question Banks List (Scrollable) -->
                            <div class="flex-1 overflow-y-auto pr-2 space-y-3">
                                <?php $__currentLoopData = $questionBanks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="border-2 border-gray-200 rounded-xl bg-white shadow-sm hover:shadow-md transition-shadow">
                                        <div class="flex items-center gap-3 p-4 cursor-pointer hover:bg-gray-50 transition rounded-t-xl" onclick="toggleBankModal('modal-bank-<?php echo e($bank->id); ?>')">
                                            <i class="fas fa-chevron-right transition-transform duration-200 text-gray-500" id="icon-modal-bank-<?php echo e($bank->id); ?>"></i>
                                            <i class="fas fa-folder text-purple-600 text-lg"></i>
                                            <h4 class="font-bold text-gray-800 flex-1"><?php echo e($bank->title); ?></h4>
                                            <span class="px-3 py-1 bg-purple-100 text-purple-700 text-sm font-semibold rounded-full">
                                                <?php echo e($bank->questions->count()); ?> soal
                                            </span>
                                        </div>

                                        <div id="modal-bank-<?php echo e($bank->id); ?>" class="modal-bank-content border-t border-gray-200 p-4 space-y-2.5 hidden bg-gray-50">
                                            <?php if($bank->questions->count() === 0): ?>
                                                <div class="text-center py-6 text-gray-500">
                                                    <i class="fas fa-inbox text-3xl mb-2"></i>
                                                    <p>Belum ada soal di bank ini</p>
                                                </div>
                                            <?php else: ?>
                                                <?php $__currentLoopData = $bank->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="modal-question-item bg-white border-2 border-gray-200 rounded-lg p-3 hover:border-purple-400 hover:shadow-sm transition-all">
                                                        <div class="flex items-start gap-3">
                                                            <input type="checkbox" 
                                                                   value="<?php echo e($question->id); ?>" 
                                                                   id="modal_qb_question_<?php echo e($question->id); ?>"
                                                                   data-question-text="<?php echo e($question->question_text); ?>"
                                                                   data-question-type="<?php echo e($question->type); ?>"
                                                                   data-question-points="<?php echo e($question->points ?? 0); ?>"
                                                                   class="modal-checkbox mt-1 w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500 cursor-pointer"
                                                                   onchange="updateSelectedCount()">
                                                            <label for="modal_qb_question_<?php echo e($question->id); ?>" class="flex-1 cursor-pointer">
                                                                <div class="flex items-center gap-2 mb-2">
                                                                    <span class="px-2.5 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full uppercase">
                                                                        <?php echo e(str_replace('_', ' ', $question->type)); ?>

                                                                    </span>
                                                                    <?php if($question->points): ?>
                                                                        <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                                                            <?php echo e($question->points); ?> poin
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="text-gray-800 font-medium modal-question-text leading-relaxed">
                                                                    <?php echo nl2br(e($question->question_text)); ?>

                                                                </div>
                                                                <?php if($question->options->count() > 0): ?>
                                                                    <div class="text-sm text-gray-600 mt-2 flex items-center gap-1">
                                                                        <i class="fas fa-list-ul text-xs"></i>
                                                                        <span class="font-semibold"><?php echo e($question->options->count()); ?></span> opsi jawaban
                                                                    </div>
                                                                <?php endif; ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="border-t-2 border-gray-200 px-6 py-4 bg-gray-50 rounded-b-2xl flex items-center justify-between">
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <i class="fas fa-check-circle text-green-600"></i>
                                <span class="font-semibold" id="modalSelectedCount">0</span>
                                <span>soal dipilih</span>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" onclick="closeQuestionModal()" 
                                        class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-semibold">
                                    <i class="fas fa-times"></i> Batal
                                </button>
                                <button type="button" onclick="confirmSelection()" 
                                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold shadow-md">
                                    <i class="fas fa-check"></i> Konfirmasi Pilihan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-3 pt-6 border-t">
                    <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">
                        <i class="fas fa-save"></i> Simpan & Buat Final Quiz
                    </button>
                    <a href="<?php echo e(route('instructor.courses.final-quiz.edit', $kursus->id)); ?>" 
                       class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <a href="<?php echo e(route('instructor.question-banks.create')); ?>" target="_blank"
                       class="ml-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-plus"></i> Buat Question Bank Baru
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Modal Functions
function openQuestionModal() {
    document.getElementById('questionModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeQuestionModal() {
    document.getElementById('questionModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function toggleBankModal(bankId) {
    const content = document.getElementById(bankId);
    const icon = document.getElementById('icon-' + bankId);
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');
    }
}

function expandAllModal() {
    document.querySelectorAll('.modal-bank-content').forEach(bank => {
        bank.classList.remove('hidden');
    });
    document.querySelectorAll('[id^="icon-modal-bank-"]').forEach(icon => {
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');
    });
}

function collapseAllModal() {
    document.querySelectorAll('.modal-bank-content').forEach(bank => {
        bank.classList.add('hidden');
    });
    document.querySelectorAll('[id^="icon-modal-bank-"]').forEach(icon => {
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');
    });
}

function selectAllModal() {
    document.querySelectorAll('.modal-checkbox').forEach(cb => {
        cb.checked = true;
    });
    updateSelectedCount();
}

function deselectAllModal() {
    document.querySelectorAll('.modal-checkbox').forEach(cb => {
        cb.checked = false;
    });
    updateSelectedCount();
}

function filterModal() {
    const searchTerm = document.getElementById('modalSearch').value.toLowerCase();
    const questions = document.querySelectorAll('.modal-question-item');
    
    // Jika ada pencarian, buka semua bank
    if (searchTerm) {
        expandAllModal();
    }
    
    questions.forEach(question => {
        const text = question.querySelector('.modal-question-text').textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            question.style.display = 'block';
        } else {
            question.style.display = 'none';
        }
    });
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.modal-checkbox:checked').length;
    document.getElementById('modalSelectedCount').textContent = count;
}

function confirmSelection() {
    const selectedCheckboxes = document.querySelectorAll('.modal-checkbox:checked');
    const selectedCount = selectedCheckboxes.length;
    
    if (selectedCount === 0) {
        alert('Silakan pilih minimal 1 soal');
        return;
    }
    
    // Clear existing hidden inputs
    document.querySelectorAll('input[name="question_bank_questions[]"]').forEach(input => {
        input.remove();
    });
    
    // Add hidden inputs for selected questions
    const form = document.getElementById('createQuizForm');
    const previewContainer = document.getElementById('selectedQuestionsList');
    previewContainer.innerHTML = '';
    
    selectedCheckboxes.forEach((checkbox, index) => {
        // Add hidden input
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'question_bank_questions[]';
        hiddenInput.value = checkbox.value;
        form.appendChild(hiddenInput);
        
        // Add to preview
        const questionText = checkbox.getAttribute('data-question-text');
        const questionType = checkbox.getAttribute('data-question-type');
        const questionPoints = checkbox.getAttribute('data-question-points');
        
        const previewItem = document.createElement('div');
        previewItem.className = 'flex items-center gap-3 p-3 bg-white border border-gray-300 rounded-lg';
        previewItem.innerHTML = `
            <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center bg-purple-600 text-white rounded-full text-xs font-bold">${index + 1}</span>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-semibold rounded uppercase">
                        ${questionType.replace('_', ' ')}
                    </span>
                    ${questionPoints > 0 ? `<span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded">${questionPoints} poin</span>` : ''}
                </div>
                <p class="text-sm text-gray-800 line-clamp-2">${questionText}</p>
            </div>
            <button type="button" onclick="removeQuestion(this, '${checkbox.value}')" class="text-red-600 hover:text-red-700">
                <i class="fas fa-times-circle"></i>
            </button>
        `;
        previewContainer.appendChild(previewItem);
    });
    
    // Update main count
    document.getElementById('selectedCount').textContent = selectedCount;
    
    // Show preview section
    document.getElementById('selectedQuestionsPreview').classList.remove('hidden');
    
    // Close modal
    closeQuestionModal();
}

function removeQuestion(button, questionId) {
    // Remove hidden input
    const hiddenInputs = document.querySelectorAll(`input[name="question_bank_questions[]"][value="${questionId}"]`);
    hiddenInputs.forEach(input => input.remove());
    
    // Remove preview item
    button.closest('.flex').remove();
    
    // Update count
    const newCount = document.querySelectorAll('input[name="question_bank_questions[]"]').length;
    document.getElementById('selectedCount').textContent = newCount;
    
    // Hide preview if no questions
    if (newCount === 0) {
        document.getElementById('selectedQuestionsPreview').classList.add('hidden');
    }
    
    // Uncheck in modal if it's open
    const modalCheckbox = document.querySelector(`.modal-checkbox[value="${questionId}"]`);
    if (modalCheckbox) {
        modalCheckbox.checked = false;
        updateSelectedCount();
    }
}

// Close modal when clicking outside
document.getElementById('questionModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeQuestionModal();
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.instructor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/instructor/courses/create-final-quiz.blade.php ENDPATH**/ ?>