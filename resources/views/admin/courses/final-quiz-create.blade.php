@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.instructor')

@section('content')
<div class="p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="javascript:void(0)" 
               onclick="if (history.length > 1) { history.back(); } else { window.location='{{ auth()->user()->role === 'admin' ? route('admin.courses.show', $course->id) : route('instructor.courses.show', $course->id) }}'; }"
               class="text-blue-600 hover:text-blue-700 mb-2 inline-flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <h2 class="text-2xl font-bold text-gray-800 mt-2">Buat Final Quiz</h2>
            <p class="text-gray-600">{{ $course->title }}</p>
            
            @if(auth()->user()->role === 'admin')
                <div class="mt-2 inline-flex items-center gap-2 px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-sm">
                    <i class="fas fa-shield-alt"></i>
                    <span>Admin Access</span>
                </div>
            @endif
        </div>

        <!-- Form -->
        <form action="{{ auth()->user()->role === 'admin' ? route('admin.courses.final-quiz.store', $course->id) : route('instructor.final-quiz.store', $course->id) }}" method="POST" id="finalQuizForm">
            @csrf

            <!-- Quiz Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex justify-between items-center mb-4 cursor-pointer" onclick="toggleQuizInfo()">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Quiz</h3>
                        <p class="text-sm text-gray-600 mt-1">Atur judul, durasi, dan kriteria kelulusan quiz</p>
                    </div>
                    <button type="button" class="text-gray-500 hover:text-gray-700 transition">
                        <i id="quizInfoIcon" class="fas fa-chevron-down"></i>
                    </button>
                </div>
                
                <div id="quizInfoSection" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Final Quiz *</label>
                        <input type="text" name="judul_quiz" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('judul_quiz', 'Final Quiz - ' . $course->title) }}">
                    </div>

                    <!-- Info Card -->
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-semibold mb-1">Pengaturan Nilai Kelulusan:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li><strong>Passing Grade:</strong> Persentase nilai minimal untuk mendapat sertifikat (disarankan 70-80%)</li>
                                    <li><strong>Minimum Score:</strong> Nilai minimal untuk dinyatakan lulus quiz (0-100)</li>
                                    <li>Student harus mencapai kedua nilai ini untuk lulus dan mendapat sertifikat</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Passing Grade (%) *
                                <span class="text-xs font-normal text-gray-500">- Untuk Sertifikat</span>
                            </label>
                            <input type="number" name="passing_grade" required min="0" max="100" step="0.01" id="passing_grade"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('passing_grade', 70) }}">
                            <div class="mt-2 flex gap-2">
                                <button type="button" onclick="document.getElementById('passing_grade').value=60" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">60%</button>
                                <button type="button" onclick="document.getElementById('passing_grade').value=70" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">70%</button>
                                <button type="button" onclick="document.getElementById('passing_grade').value=80" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">80%</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Minimum Score *
                                <span class="text-xs font-normal text-gray-500">- Untuk Lulus</span>
                            </label>
                            <input type="number" name="minimum_score" required min="0" max="100" id="minimum_score"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('minimum_score', 70) }}">
                            <div class="mt-2 flex gap-2">
                                <button type="button" onclick="document.getElementById('minimum_score').value=60" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">60</button>
                                <button type="button" onclick="document.getElementById('minimum_score').value=70" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">70</button>
                                <button type="button" onclick="document.getElementById('minimum_score').value=80" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">80</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Durasi (menit) *</label>
                            <input type="number" name="durasi_quiz" required min="1" id="durasi_quiz"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('durasi_quiz', 60) }}">
                            <div class="mt-2 flex gap-2">
                                <button type="button" onclick="document.getElementById('durasi_quiz').value=30" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">30m</button>
                                <button type="button" onclick="document.getElementById('durasi_quiz').value=60" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">60m</button>
                                <button type="button" onclick="document.getElementById('durasi_quiz').value=90" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">90m</button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kesempatan Mengerjakan *</label>
                        <input type="number" name="kesempatan_mengerjakan" required min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('kesempatan_mengerjakan', 3) }}">
                    </div>
                </div>
                </div>
            </div>

            <!-- Import from Bank Soal Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Import dari Bank Soal</h3>
                        <p class="text-sm text-gray-600 mt-1">Pilih soal dari bank soal untuk ditambahkan ke quiz</p>
                    </div>
                    <button type="button" onclick="toggleBankSoal()" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">
                        <i class="fas fa-database mr-2"></i>Pilih dari Bank Soal
                    </button>
                </div>

                <div id="bankSoalSection" class="hidden mt-4">
                    <!-- Filter & Controls -->
                    <div class="mb-4 flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Filter Kategori:</label>
                            <select id="filterKategori" onchange="filterBankSoal()" 
                                    class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
                                <option value="">Semua Kategori</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Filter Tipe:</label>
                            <select id="filterTipe" onchange="filterBankSoal()" 
                                    class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
                                <option value="">Semua Tipe</option>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="essay">Essay</option>
                                <option value="true_false">True/False</option>
                            </select>
                        </div>
                        <div class="ml-auto flex items-center gap-3">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="selectAllBank" class="w-4 h-4 text-purple-600 rounded">
                                <label for="selectAllBank" class="text-sm font-medium text-gray-700">Pilih Semua</label>
                            </div>
                            <button type="button" onclick="importSelectedQuestions()" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                <i class="fas fa-download mr-2"></i>Import Terpilih
                            </button>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs text-blue-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Tip:</strong> Anda dapat mengimport soal dari kategori apapun. Soal akan otomatis ditambahkan ke final quiz ini.
                        </p>
                    </div>
                    
                    <div id="bankSoalList" class="space-y-3 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                        <p class="text-gray-500 text-center py-4">Loading...</p>
                    </div>
                    
                    <div id="bankSoalStats" class="mt-3 text-sm text-gray-600 text-center"></div>
                </div>
            </div>

            <!-- Questions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Soal-soal Quiz</h3>
                    <button type="button" onclick="addQuestion()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                        <i class="fas fa-plus mr-2"></i>Tambah Soal Manual
                    </button>
                </div>

                <div id="questionsContainer" class="space-y-4">
                    <!-- Questions will be added here dynamically -->
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('instructor.courses.show', $course->id) }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Simpan Final Quiz
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let questionIndex = 0;
let importedBankSoalIds = []; // Track soal yang sudah diimport

function addQuestion() {
    const container = document.getElementById('questionsContainer');
    const questionDiv = document.createElement('div');
    questionDiv.className = 'border border-gray-200 rounded-lg p-4 question-item';
    questionDiv.dataset.index = questionIndex;
    
    questionDiv.innerHTML = `
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-medium text-gray-900">Soal ${questionIndex + 1}</h4>
            <button type="button" onclick="removeQuestion(this)" 
                    class="text-red-600 hover:text-red-700">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pertanyaan *</label>
                <textarea name="questions[${questionIndex}][pertanyaan]" required rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Soal *</label>
                <select name="questions[${questionIndex}][tipe_soal]" required onchange="toggleQuestionType(this, ${questionIndex})"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="true_false">True/False</option>
                    <option value="essay">Essay</option>
                </select>
            </div>
            
            <div class="options-container-${questionIndex}">
                <label class="block text-sm font-medium text-gray-700 mb-2">Opsi Jawaban *</label>
                <p class="text-xs text-gray-600 mb-3">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    Centang checkbox untuk menandai jawaban yang benar. Minimal pilih 1 jawaban benar.
                </p>
                <div class="space-y-2" id="options-${questionIndex}">
                    ${createOption(questionIndex, 0)}
                    ${createOption(questionIndex, 1)}
                </div>
                <button type="button" onclick="addOption(${questionIndex})" 
                        class="mt-2 text-sm text-blue-600 hover:text-blue-700">
                    <i class="fas fa-plus mr-1"></i>Tambah Opsi
                </button>
            </div>
            
            <div class="true-false-container-${questionIndex}" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jawaban Benar *</label>
                <select name="questions[${questionIndex}][correct_answer]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="true">True</option>
                    <option value="false">False</option>
                </select>
            </div>
            
            <div class="essay-container-${questionIndex}" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kunci Jawaban / Rubrik Penilaian</label>
                <p class="text-xs text-gray-600 mb-2">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    Isi dengan kunci jawaban atau kriteria penilaian untuk membantu penilaian jawaban essay
                </p>
                <textarea name="questions[${questionIndex}][essay_answer]" rows="4"
                          placeholder="Contoh: Jawaban harus mencakup 3 poin utama: 1) Definisi, 2) Contoh, 3) Kesimpulan"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-200">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="questions[${questionIndex}][save_to_bank]" value="1"
                           class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">
                        <i class="fas fa-database text-blue-600 mr-1"></i>
                        Simpan soal ini ke Bank Soal untuk digunakan kembali
                    </span>
                </label>
            </div>
        </div>
    `;
    
    container.appendChild(questionDiv);
    questionIndex++;
}

function createOption(questionIdx, optionIdx) {
    return `
        <div class="flex gap-2 items-center option-item">
            <input type="checkbox" name="questions[${questionIdx}][options][${optionIdx}][is_benar]" value="1"
                   class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
            <input type="text" name="questions[${questionIdx}][options][${optionIdx}][teks_opsi]" required
                   placeholder="Teks opsi"
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <button type="button" onclick="removeOption(this)" 
                    class="text-red-600 hover:text-red-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
}

function addOption(questionIdx) {
    const container = document.getElementById(`options-${questionIdx}`);
    const optionCount = container.querySelectorAll('.option-item').length;
    const optionDiv = document.createElement('div');
    optionDiv.className = 'flex gap-2 items-center option-item';
    optionDiv.innerHTML = `
        <input type="checkbox" name="questions[${questionIdx}][options][${optionCount}][is_benar]" value="1"
               class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
        <input type="text" name="questions[${questionIdx}][options][${optionCount}][teks_opsi]" required
               placeholder="Teks opsi"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <button type="button" onclick="removeOption(this)" 
                class="text-red-600 hover:text-red-700">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(optionDiv);
}

function removeOption(btn) {
    btn.closest('.option-item').remove();
}

function removeQuestion(btn) {
    if (confirm('Hapus soal ini?')) {
        const questionItem = btn.closest('.question-item');
        
        // Cek apakah soal ini dari bank soal
        const bankSoalIdInput = questionItem.querySelector('input[name*="[bank_soal_id]"]');
        if (bankSoalIdInput) {
            const bankSoalId = bankSoalIdInput.value;
            // Hapus dari tracking agar bisa diimport lagi
            importedBankSoalIds = importedBankSoalIds.filter(id => id !== bankSoalId);
            
            // Uncheck checkbox di modal
            const checkbox = document.querySelector(`.bank-soal-checkbox[value="${bankSoalId}"]`);
            if (checkbox) {
                checkbox.checked = false;
            }
        }
        
        questionItem.remove();
        updateQuestionNumbers();
    }
}

// Bank Soal Functions
let bankSoalData = [];
let allBankSoalData = [];

function toggleBankSoal() {
    const section = document.getElementById('bankSoalSection');
    if (section.classList.contains('hidden')) {
        section.classList.remove('hidden');
        loadBankSoal();
    } else {
        section.classList.add('hidden');
    }
}

async function loadBankSoal() {
    const listContainer = document.getElementById('bankSoalList');
    listContainer.innerHTML = '<p class="text-gray-500 text-center py-4">Loading...</p>';
    
    try {
        const apiUrl = '{{ auth()->user()->role === "admin" ? "/admin" : "/instructor" }}/question-banks/api';
        console.log('Fetching from:', apiUrl);
        const response = await fetch(apiUrl);
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            const text = await response.text();
            console.error('Response text:', text);
            throw new Error(`HTTP ${response.status}: ${text.substring(0, 100)}`);
        }
        
        const data = await response.json();
        console.log('Received data:', data);
        
        // Check if response is an error object
        if (data.error) {
            throw new Error(data.message || 'Unknown error');
        }
        
        allBankSoalData = data;
        bankSoalData = allBankSoalData;
        
        if (bankSoalData.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-gray-400 text-4xl mb-3"></i>
                    <p class="text-gray-600 font-semibold">Tidak ada soal di bank soal</p>
                    <p class="text-sm text-gray-500 mt-2">Buat soal baru di fitur Bank Soal terlebih dahulu</p>
                    <a href="{{ route('instructor.question-banks.create') }}" target="_blank"
                       class="mt-3 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                        <i class="fas fa-plus mr-2"></i>Buat Bank Soal Baru
                    </a>
                </div>
            `;
            return;
        }
        
        // Populate kategori filter
        populateKategoriFilter();
        
        // Display all questions initially
        renderBankSoal();
        
    } catch (error) {
        console.error('Error loading bank soal:', error);
        listContainer.innerHTML = `
            <div class="text-center py-4">
                <p class="text-red-500 font-semibold">Gagal memuat bank soal</p>
                <p class="text-sm text-gray-600 mt-2">${error.message}</p>
                <p class="text-xs text-gray-500 mt-1">Lihat console browser (F12) untuk detail error</p>
                <button onclick="loadBankSoal()" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    <i class="fas fa-redo mr-2"></i>Coba Lagi
                </button>
            </div>
        `;
    }
}

function populateKategoriFilter() {
    const kategoriSet = new Set();
    allBankSoalData.forEach(soal => {
        if (soal.kategori) {
            kategoriSet.add(soal.kategori);
        }
    });
    
    const filterSelect = document.getElementById('filterKategori');
    const currentValue = filterSelect.value;
    
    filterSelect.innerHTML = '<option value="">Semua Kategori</option>';
    Array.from(kategoriSet).sort().forEach(kategori => {
        const option = document.createElement('option');
        option.value = kategori;
        option.textContent = kategori;
        filterSelect.appendChild(option);
    });
    
    filterSelect.value = currentValue;
}

function filterBankSoal() {
    const kategoriFilter = document.getElementById('filterKategori').value;
    const tipeFilter = document.getElementById('filterTipe').value;
    
    bankSoalData = allBankSoalData.filter(soal => {
        const matchKategori = !kategoriFilter || soal.kategori === kategoriFilter;
        const matchTipe = !tipeFilter || soal.tipe_soal === tipeFilter;
        return matchKategori && matchTipe;
    });
    
    renderBankSoal();
}

function renderBankSoal() {
    const listContainer = document.getElementById('bankSoalList');
    const statsContainer = document.getElementById('bankSoalStats');
    
    if (bankSoalData.length === 0) {
        listContainer.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-filter text-gray-400 text-4xl mb-3"></i>
                <p class="text-gray-600 font-semibold">Tidak ada soal yang sesuai filter</p>
                <button onclick="resetFilters()" class="mt-3 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm">
                    <i class="fas fa-redo mr-2"></i>Reset Filter
                </button>
            </div>
        `;
        statsContainer.textContent = '';
        return;
    }
    
    listContainer.innerHTML = bankSoalData.map(soal => `
        <div class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
            <input type="checkbox" name="bank_soal_ids[]" value="${soal.id}" class="bank-soal-checkbox w-4 h-4 text-purple-600 rounded mt-1">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    ${soal.bank_name ? `<span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded-full font-medium"><i class="fas fa-folder mr-1"></i>${soal.bank_name}</span>` : ''}
                    ${getTipeSoalBadge(soal.tipe_soal)}
                    ${soal.kategori ? `<span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded-full"><i class="fas fa-tag mr-1"></i>${soal.kategori}</span>` : '<span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs rounded-full">Tanpa Kategori</span>'}
                </div>
                <p class="text-gray-800 text-sm font-medium mb-2">${soal.pertanyaan}</p>
                ${(soal.opsi_jawaban || soal.opsiJawaban) && (soal.opsi_jawaban || soal.opsiJawaban).length > 0 ? `
                    <div class="space-y-1 pl-3 border-l-2 border-gray-200">
                        ${(soal.opsi_jawaban || soal.opsiJawaban).map(opsi => `
                            <div class="flex items-center gap-2 text-xs">
                                ${opsi.is_benar ? '<i class="fas fa-check-circle text-green-600"></i>' : '<i class="far fa-circle text-gray-400"></i>'}
                                <span class="${opsi.is_benar ? 'text-green-700 font-medium' : 'text-gray-600'}">${opsi.teks_opsi}</span>
                            </div>
                        `).join('')}
                    </div>
                ` : ''}
            </div>
        </div>
    `).join('');
    
    // Update stats
    statsContainer.innerHTML = `
        <i class="fas fa-info-circle mr-1"></i>
        Menampilkan <strong>${bankSoalData.length}</strong> dari <strong>${allBankSoalData.length}</strong> soal
    `;
    
    // Setup select all
    const selectAllCheckbox = document.getElementById('selectAllBank');
    selectAllCheckbox.checked = false;
    selectAllCheckbox.onclick = function() {
        document.querySelectorAll('.bank-soal-checkbox').forEach(cb => {
            cb.checked = this.checked;
        });
    };
}

function resetFilters() {
    document.getElementById('filterKategori').value = '';
    document.getElementById('filterTipe').value = '';
    bankSoalData = allBankSoalData;
    renderBankSoal();
}

function syncSelectedBankSoalInputs(selectedIds) {
    document.querySelectorAll('.selected-bank-soal-input').forEach(el => el.remove());
    const form = document.getElementById('finalQuizForm');
    selectedIds.forEach(id => {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'selected_bank_soal_ids[]';
        hidden.value = id;
        hidden.className = 'selected-bank-soal-input';
        form.appendChild(hidden);
    });
}

function getTipeSoalBadge(tipe) {
    const badges = {
        'multiple_choice': '<span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full"><i class="fas fa-list-ul mr-1"></i>Multiple Choice</span>',
        'essay': '<span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded-full"><i class="fas fa-pencil-alt mr-1"></i>Essay</span>',
        'true_false': '<span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full"><i class="fas fa-check-circle mr-1"></i>True/False</span>'
    };
    return badges[tipe] || '';
}

function importSelectedQuestions(silent = false) {
    const selectedIds = Array.from(document.querySelectorAll('.bank-soal-checkbox:checked')).map(cb => cb.value);
    
    console.log('importSelectedQuestions called. Silent:', silent, 'Selected IDs:', selectedIds);
    
    if (selectedIds.length === 0) {
        if (!silent) {
            alert('Pilih minimal 1 soal untuk diimport');
        }
        console.log('No selected IDs, returning');
        return;
    }
    
    // Filter hanya soal yang belum diimport
    const newIds = selectedIds.filter(id => !importedBankSoalIds.includes(id));
    
    console.log('New IDs to import:', newIds);
    console.log('Already imported IDs:', importedBankSoalIds);
    
    if (newIds.length === 0) {
        if (!silent) {
            alert('Semua soal yang dipilih sudah diimport sebelumnya');
        }
        console.log('All questions already imported');
        return;
    }
    
    const selectedSoal = bankSoalData.filter(soal => newIds.includes(String(soal.id)));
    
    console.log('Found', selectedSoal.length, 'questions in bankSoalData');
    
    selectedSoal.forEach(soal => {
        console.log('Adding question from bank:', soal.id);
        addQuestionFromBank(soal);
        importedBankSoalIds.push(String(soal.id)); // Track sebagai sudah diimport
    });
    
    console.log('Import complete. Total imported IDs:', importedBankSoalIds);
    
    if (!silent) {
        document.getElementById('bankSoalSection').classList.add('hidden');
        alert(`${newIds.length} soal berhasil diimport!`);
        // TIDAK uncheck checkbox, biarkan tercentang agar tidak hilang saat submit
    }
}

function addQuestionFromBank(soal) {
    console.log('addQuestionFromBank called for soal:', soal);
    
    const container = document.getElementById('questionsContainer');
    console.log('Container found:', container !== null);
    
    const questionDiv = document.createElement('div');
    questionDiv.className = 'border border-gray-200 rounded-lg p-4 question-item bg-purple-50';
    questionDiv.dataset.index = questionIndex;
    
    console.log('Creating question with index:', questionIndex);
    
    // Handle both snake_case and camelCase for relationship
    const opsiJawaban = soal.opsi_jawaban || soal.opsiJawaban || [];
    
    let optionsHtml = '';
    if (soal.tipe_soal === 'multiple_choice' || soal.tipe_soal === 'true_false') {
        optionsHtml = `
            <div class="options-container-${questionIndex}">
                <label class="block text-sm font-medium text-gray-700 mb-2">Opsi Jawaban *</label>
                <div class="space-y-2" id="options-${questionIndex}">
                    ${opsiJawaban.map((opsi, idx) => `
                        <div class="flex gap-2 items-center option-item">
                            <input type="checkbox" name="questions[${questionIndex}][options][${idx}][is_benar]" value="1"
                                   ${opsi.is_benar ? 'checked' : ''}
                                   class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                            <input type="text" name="questions[${questionIndex}][options][${idx}][teks_opsi]"
                                   value="${opsi.teks_opsi}" required
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="button" onclick="removeOption(this)" 
                                    class="text-red-600 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `).join('')}
                </div>
                ${soal.tipe_soal === 'multiple_choice' ? `
                    <button type="button" onclick="addOption(${questionIndex})" 
                            class="mt-2 text-sm text-blue-600 hover:text-blue-700">
                        <i class="fas fa-plus mr-1"></i>Tambah Opsi
                    </button>
                ` : ''}
            </div>
        `;
    }
    
    questionDiv.innerHTML = `
        <input type="hidden" name="questions[${questionIndex}][bank_soal_id]" value="${soal.id}">
        
        <div class="flex justify-between items-center mb-3">
            <div class="flex items-center gap-2">
                <h4 class="font-medium text-gray-900">Soal ${questionIndex + 1}</h4>
                <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded-full">
                    <i class="fas fa-database mr-1"></i>Dari Bank Soal
                </span>
            </div>
            <button type="button" onclick="removeQuestion(this)" 
                    class="text-red-600 hover:text-red-700">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pertanyaan *</label>
                <textarea name="questions[${questionIndex}][pertanyaan]" required rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">${soal.pertanyaan}</textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Soal *</label>
                <select name="questions[${questionIndex}][tipe_soal]" required onchange="toggleQuestionType(this, ${questionIndex})"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="multiple_choice" ${soal.tipe_soal === 'multiple_choice' ? 'selected' : ''}>Multiple Choice</option>
                    <option value="true_false" ${soal.tipe_soal === 'true_false' ? 'selected' : ''}>True/False</option>
                    <option value="essay" ${soal.tipe_soal === 'essay' ? 'selected' : ''}>Essay</option>
                </select>
            </div>
            
            ${optionsHtml}
        </div>
    `;
    
    console.log('Appending question to container');
    container.appendChild(questionDiv);
    questionIndex++;
    console.log('Question added. New questionIndex:', questionIndex);
    console.log('Total questions now:', document.querySelectorAll('.question-item').length);
    updateQuestionNumbers();
}

function updateQuestionNumbers() {
    document.querySelectorAll('.question-item').forEach((item, index) => {
        item.querySelector('h4').textContent = `Soal ${index + 1}`;
    });
}

function toggleQuestionType(select, questionIdx) {
    const optionsContainer = document.querySelector(`.options-container-${questionIdx}`);
    const trueFalseContainer = document.querySelector(`.true-false-container-${questionIdx}`);
    const essayContainer = document.querySelector(`.essay-container-${questionIdx}`);
    
    if (select.value === 'multiple_choice') {
        optionsContainer.style.display = 'block';
        trueFalseContainer.style.display = 'none';
        essayContainer.style.display = 'none';
    } else if (select.value === 'true_false') {
        optionsContainer.style.display = 'none';
        trueFalseContainer.style.display = 'block';
        essayContainer.style.display = 'none';
    } else {
        // essay
        optionsContainer.style.display = 'none';
        trueFalseContainer.style.display = 'none';
        essayContainer.style.display = 'block';
    }
}

// Validasi form sebelum submit
document.getElementById('finalQuizForm').addEventListener('submit', function(e) {
    // Cek dulu apakah ada soal yang sudah ditambahkan ke form
    let questions = document.querySelectorAll('.question-item');
    console.log('=== FORM SUBMIT DEBUG ===');
    console.log('Total questions in form:', questions.length);
    
    // Jika belum ada soal, cek apakah ada checkbox yang tercentang untuk auto-import
    if (questions.length === 0) {
        let selectedIds = Array.from(document.querySelectorAll('.bank-soal-checkbox:checked')).map(cb => cb.value);
        console.log('No questions yet. Checked boxes:', selectedIds.length);
        
        if (selectedIds.length > 0) {
            console.log('Auto-importing checked questions...');
            importSelectedQuestions(true);
            // Re-check setelah import
            questions = document.querySelectorAll('.question-item');
            console.log('Questions after auto-import:', questions.length);
        }
    }

    // Final check: apakah ada soal?
    if (questions.length === 0) {
        e.preventDefault();
        console.log('ERROR: No questions found!');
        alert('Tambahkan minimal 1 soal ke final quiz.\n\nCara 1: Klik "Pilih dari Bank Soal" → Centang soal → Klik "Import Soal Terpilih"\nCara 2: Klik "Tambah Soal Manual"');
        return false;
    }
    
    console.log('✓ Validation passed. Submitting form with', questions.length, 'questions');

    let hasError = false;
    let errorMessage = '';
    
    questions.forEach((question, index) => {
        const questionIndex = question.dataset.index;
        const tipeSelect = question.querySelector(`select[name="questions[${questionIndex}][tipe_soal]"]`);
        
        if (tipeSelect && tipeSelect.value === 'multiple_choice') {
            const checkedOptions = question.querySelectorAll(`input[name^="questions[${questionIndex}][options]"][type="checkbox"]:checked`);
            
            if (checkedOptions.length === 0) {
                hasError = true;
                errorMessage = `Soal ${index + 1}: Pilih minimal 1 jawaban benar untuk soal pilihan ganda!`;
                
                // Highlight error
                const optionsContainer = question.querySelector(`.options-container-${questionIndex}`);
                if (optionsContainer) {
                    optionsContainer.classList.add('border-2', 'border-red-500', 'rounded-lg', 'p-2');
                    setTimeout(() => {
                        optionsContainer.classList.remove('border-2', 'border-red-500', 'rounded-lg', 'p-2');
                    }, 3000);
                }
            }
        }
    });
    
    if (hasError) {
        e.preventDefault();
        alert(errorMessage);
        return false;
    }
});

// Toggle Quiz Info Section
function toggleQuizInfo() {
    const section = document.getElementById('quizInfoSection');
    const icon = document.getElementById('quizInfoIcon');
    
    if (section.style.display === 'none') {
        section.style.display = 'block';
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');
    } else {
        section.style.display = 'none';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');
    }
}

// Add first question on load
document.addEventListener('DOMContentLoaded', function() {
    addQuestion();
});
</script>
@endsection
