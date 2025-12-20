@extends('layouts.app')

@section('title', 'Final Quiz - ' . $quiz->judul_quiz)

@section('content')
    <style>
        /* Hide sidebar and navbar during quiz */
        aside.fixed.w-80,
        .fixed.w-80,
        aside[class*="w-80"],
        div[x-data]>aside,
        div[x-data]>button.fixed,
        div[x-data]>div.fixed,
        header,
        nav,
        .navbar,
        [class*="navbar"],
        .flex-1.flex.flex-col>header,
        .flex.min-h-screen>.flex-1>header {
            display: none !important;
            visibility: hidden !important;
        }

        .flex-1.flex.flex-col {
            margin-left: 0 !important;
            width: 100% !important;
        }

        body {
            padding-top: 0 !important;
        }

        /* Bigger question card */
        .quiz-question-card {
            min-height: 450px !important;
            padding: 2.5rem !important;
        }

        .quiz-question-card p {
            font-size: 1.25rem !important;
            line-height: 1.8 !important;
        }

        /* Bigger navigation buttons */
        .quiz-nav-btn {
            width: 52px !important;
            height: 52px !important;
            font-size: 1.125rem !important;
            font-weight: 700 !important;
        }
    </style>
    <div class="min-h-screen bg-[#f4f2f0] py-8">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between mb-6">
                <button type="button" id="back-link" data-href="{{ route('student.course.learn', $kursusId) }}"
                    class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900 transition text-lg">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali ke Kursus</span>
                </button>
                <div class="text-lg text-gray-600 font-semibold">{{ $quiz->judul_quiz }}</div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <div class="px-5 py-2.5 rounded-full bg-gray-100 text-base font-bold" id="question-counter">
                            1/{{ $questions->count() }}</div>
                        @if($quiz->durasi_quiz)
                            <div class="px-5 py-2.5 rounded-full text-base font-bold bg-green-100 text-green-700"
                                id="timer-display">
                                <i class="fas fa-clock mr-2"></i>
                                <span id="timer-text">--:--</span>
                            </div>
                        @endif
                        <div class="text-gray-700 font-bold text-lg">Final Quiz</div>
                    </div>
                    <div class="border-2 border-gray-200 rounded-2xl p-8 quiz-question-card flex flex-col justify-between">
                        <div>
                            <p class="text-xl text-gray-800 font-medium mb-6" id="question-text"></p>
                            <div id="question-image-container" class="mb-6 hidden">
                                <img id="question-image" src="" alt="Question Image" class="max-w-full h-auto rounded-lg">
                            </div>
                            <div class="space-y-4" id="options-container"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-6 gap-4">
                        <button id="prev-btn"
                            class="px-6 py-3 bg-white border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition text-base font-semibold">Sebelumnya</button>
                        <button id="next-btn"
                            class="px-6 py-3 bg-emerald-700 text-white rounded-xl hover:bg-emerald-800 transition text-base font-semibold">Selanjutnya</button>
                    </div>
                </div>
                <div class="lg:col-span-1 bg-gray-100 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4 text-base font-bold text-gray-700">
                        <span>Nomor Soal</span>
                    </div>
                    <div class="grid grid-cols-4 gap-3" id="nav-grid"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal konfirmasi selesai -->
    <div id="finish-modal" class="fixed inset-0 bg-black bg-opacity-40 z-50 hidden items-center justify-center">
        <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Apakah anda yakin dengan jawaban anda?</h3>
            <p class="text-sm text-gray-600 mb-4">Anda dapat mengecek kembali sebelum menandai quiz selesai.</p>
            <div class="flex justify-end gap-2">
                <button type="button" id="modal-back-btn"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">Cek kembali</button>
                <button type="button" id="modal-finish-btn"
                    class="px-4 py-2 bg-emerald-700 text-white rounded-lg hover:bg-emerald-800 transition">Selesai</button>
            </div>
        </div>
    </div>

    <!-- Modal konfirmasi keluar -->
    <div id="exit-modal" class="fixed inset-0 bg-black bg-opacity-40 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-md text-center">
            <div class="mb-4 text-amber-500">
                <i class="fas fa-clock text-4xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Yakin ingin keluar?</h3>
            <p class="text-sm text-gray-600 mb-4 font-medium">
                Anda dapat melanjutkan quiz ini nanti, tetapi <strong class="text-red-600">waktu tetap berjalan</strong>.
            </p>
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-6 text-left">
                <p class="text-xs text-amber-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    Progress jawaban Anda akan tersimpan dan bisa dilanjutkan.
                </p>
            </div>
            <div class="flex justify-center gap-3">
                <button type="button" id="exit-cancel-btn"
                    class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-medium">Batal</button>
                <button type="button" id="exit-confirm-btn"
                    class="px-6 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition font-medium">Ya,
                    Keluar</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const questions = {!! json_encode(
        $questions->map(function ($q) {
            return [
                'id' => $q->id,
                'text' => $q->question_text,
                'image' => $q->image ? asset('storage/' . $q->image) : null,
                'type' => $q->type,
                'options' => $q->options ? $q->options->map(function ($o) {
                    return [
                        'id' => $o->id,
                        'text' => $o->option_text,
                    ];
                })->values() : [],
            ];
        })->values(),
        JSON_UNESCAPED_UNICODE
    ) !!};

            const QUIZ_KEY = 'final_quiz_{{ $attempt->id }}';
            const QUIZ_INDEX_KEY = 'final_quiz_index_{{ $attempt->id }}';
            const quizDuration = {{ $quiz->durasi_quiz ?? 0 }} * 60;

            let answers = {};
            try {
                const saved = localStorage.getItem(QUIZ_KEY);
                if (saved) answers = JSON.parse(saved) || {};
            } catch (_) { }

            let current = 0;
            try {
                const savedIdx = localStorage.getItem(QUIZ_INDEX_KEY);
                if (savedIdx !== null) {
                    current = Math.max(0, Math.min(questions.length - 1, parseInt(savedIdx, 10) || 0));
                }
            } catch (_) { }

            const questionText = document.getElementById('question-text');
            const optionsContainer = document.getElementById('options-container');
            const questionImageContainer = document.getElementById('question-image-container');
            const questionImage = document.getElementById('question-image');
            const counter = document.getElementById('question-counter');
            const navGrid = document.getElementById('nav-grid');
            const nextBtn = document.getElementById('next-btn');
            const prevBtn = document.getElementById('prev-btn');
            const finishModal = document.getElementById('finish-modal');
            const modalBackBtn = document.getElementById('modal-back-btn');
            const modalFinishBtn = document.getElementById('modal-finish-btn');

            // Exit modal elements
            const backLink = document.getElementById('back-link');
            const exitModal = document.getElementById('exit-modal');
            const exitCancelBtn = document.getElementById('exit-cancel-btn');
            const exitConfirmBtn = document.getElementById('exit-confirm-btn');

            // Timer functionality
            @if($quiz->durasi_quiz)
                const timerDisplay = document.getElementById('timer-display');
                const timerText = document.getElementById('timer-text');
                let timeRemaining;

                // Use started_at from database (more reliable than localStorage)
                const startedAtFromDB = {{ $attempt->started_at->timestamp * 1000 }};
                const elapsed = Math.floor((Date.now() - startedAtFromDB) / 1000);
                timeRemaining = Math.max(0, quizDuration - elapsed);

                function formatTime(seconds) {
                    const mins = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    return `${mins}:${secs.toString().padStart(2, '0')}`;
                }

                function updateTimerDisplay() {
                    timerText.textContent = formatTime(timeRemaining);

                    if (timeRemaining <= 60) {
                        timerDisplay.className = 'px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700';
                    } else if (timeRemaining <= 300) {
                        timerDisplay.className = 'px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700';
                    } else {
                        timerDisplay.className = 'px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700';
                    }
                }

                function autoSubmitQuiz() {
                    clearQuizData();
                    alert('⏰ Waktu habis! Quiz akan otomatis disubmit.');
                    submitQuiz();
                }

                const timerInterval = setInterval(() => {
                    timeRemaining--;
                    updateTimerDisplay();

                    if (timeRemaining === 300) {
                        alert('⚠️ Perhatian! Waktu tersisa 5 menit lagi.');
                    }

                    if (timeRemaining <= 0) {
                        clearInterval(timerInterval);
                        autoSubmitQuiz();
                    }
                }, 1000);

                updateTimerDisplay();
            @endif

                function clearQuizData() {
                    localStorage.removeItem(QUIZ_KEY);
                    localStorage.removeItem(QUIZ_INDEX_KEY);
                }

            // Poll for quiz status every 30 seconds
            const checkStatusUrl = '{{ route("courses.final-quiz.check-status", $kursusId) }}';
            const courseLearnUrl = '{{ route("student.course.learn", $kursusId) }}';

            function checkQuizStatus() {
                fetch(checkStatusUrl, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.is_active) {
                            // Quiz has been deactivated
                            clearQuizData();
                            isSubmitting = true; // prevent beforeunload alert
                            alert('⚠️ Final quiz sedang dalam proses maintenance. Anda akan dialihkan ke halaman materi.');
                            window.location.href = courseLearnUrl;
                        }
                    })
                    .catch(err => {
                        console.error('Error checking quiz status:', err);
                    });
            }

            // Check status every 30 seconds
            const statusCheckInterval = setInterval(checkQuizStatus, 30000);
            // Also check immediately on load
            checkQuizStatus();

            const allAnswered = () => {
                return questions.every(q => {
                    return answers[q.id] !== undefined && answers[q.id] !== null && answers[q.id] !== '';
                });
            };

            function renderQuestion(index) {
                const q = questions[index];
                counter.textContent = `${index + 1}/${questions.length}`;
                questionText.textContent = q.text;

                // Handle image
                if (q.image) {
                    questionImage.src = q.image;
                    questionImageContainer.classList.remove('hidden');
                } else {
                    questionImageContainer.classList.add('hidden');
                }

                optionsContainer.innerHTML = '';

                if (q.type === 'essay' || !q.options || q.options.length === 0) {
                    // Essay question
                    const textarea = document.createElement('textarea');
                    textarea.className = 'w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-emerald-500';
                    textarea.rows = 6;
                    textarea.placeholder = 'Tuliskan jawaban Anda...';
                    if (answers[q.id]) textarea.value = answers[q.id];
                    textarea.addEventListener('input', () => {
                        answers[q.id] = textarea.value;
                        localStorage.setItem(QUIZ_KEY, JSON.stringify(answers));
                        updateNavStatus();
                        updateActionButton();
                    });
                    optionsContainer.appendChild(textarea);
                } else {
                    // Multiple choice - 2 column grid
                    const grid = document.createElement('div');
                    grid.className = 'grid grid-cols-2 gap-3';

                    q.options.forEach(opt => {
                        const id = `q${q.id}_opt${opt.id}`;
                        const wrap = document.createElement('label');
                        wrap.className = 'flex items-center gap-3 text-gray-800 cursor-pointer p-2 rounded-lg hover:bg-gray-50 transition';
                        wrap.innerHTML = `
                                                    <input type="radio" name="question_${q.id}" id="${id}" value="${opt.id}" class="text-emerald-600 w-4 h-4">
                                                    <span>${opt.text}</span>
                                                `;
                        if (answers[q.id] == opt.id) {
                            wrap.querySelector('input').checked = true;
                            wrap.classList.add('bg-emerald-50');
                        }
                        wrap.querySelector('input').addEventListener('change', () => {
                            answers[q.id] = opt.id;
                            localStorage.setItem(QUIZ_KEY, JSON.stringify(answers));
                            updateNavStatus();
                            updateActionButton();
                            // Update visual
                            grid.querySelectorAll('label').forEach(l => l.classList.remove('bg-emerald-50'));
                            wrap.classList.add('bg-emerald-50');
                        });
                        grid.appendChild(wrap);
                    });
                    optionsContainer.appendChild(grid);
                }

                updateNavStatus();
                updateActionButton();
            }

            function updateNavStatus() {
                navGrid.innerHTML = '';
                questions.forEach((q, idx) => {
                    const btn = document.createElement('button');
                    const answered = answers[q.id] !== undefined && answers[q.id] !== null && answers[q.id] !== '';
                    btn.className = `w-12 h-12 rounded-lg text-base font-bold ${idx === current ? 'bg-emerald-700 text-white' : (answered ? 'bg-emerald-500 text-white' : 'bg-gray-300 text-gray-800')}`;
                    btn.textContent = idx + 1;
                    btn.addEventListener('click', () => {
                        current = idx;
                        localStorage.setItem(QUIZ_INDEX_KEY, current);
                        renderQuestion(current);
                    });
                    navGrid.appendChild(btn);
                });
            }

            function updateActionButton() {
                if (current === questions.length - 1) {
                    nextBtn.textContent = 'Selesai';
                    // Disable if not all answered
                    if (!allAnswered()) {
                        nextBtn.disabled = true;
                        nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        nextBtn.classList.remove('hover:bg-emerald-800');
                    } else {
                        nextBtn.disabled = false;
                        nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        nextBtn.classList.add('hover:bg-emerald-800');
                    }
                } else {
                    nextBtn.textContent = 'Selanjutnya';
                    nextBtn.disabled = false;
                    nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    nextBtn.classList.add('hover:bg-emerald-800');
                }
            }

            prevBtn.addEventListener('click', () => {
                if (current > 0) {
                    current--;
                    localStorage.setItem(QUIZ_INDEX_KEY, current);
                    renderQuestion(current);
                }
            });

            nextBtn.addEventListener('click', () => {
                if (current < questions.length - 1) {
                    current++;
                    localStorage.setItem(QUIZ_INDEX_KEY, current);
                    renderQuestion(current);
                } else if (allAnswered()) {
                    finishModal.classList.remove('hidden');
                    finishModal.classList.add('flex');
                } else {
                    alert('⚠️ Harap jawab semua soal terlebih dahulu sebelum menyelesaikan quiz.');
                }
            });

            modalBackBtn.addEventListener('click', () => {
                finishModal.classList.add('hidden');
                finishModal.classList.remove('flex');
            });

            // Flag to track if quiz is being submitted
            let isSubmitting = false;

            // Named function for beforeunload so we can remove it later
            function handleBeforeUnload(e) {
                // Don't show alert if quiz is being submitted successfully
                if (isSubmitting) {
                    return;
                }
                e.preventDefault();
                e.returnValue = '';
            }

            // Prevent accidental page close
            window.addEventListener('beforeunload', handleBeforeUnload);

            function submitQuiz() {
                @if($quiz->durasi_quiz)
                    clearInterval(timerInterval);
                @endif

                modalFinishBtn.disabled = true;
                modalFinishBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

                fetch('{{ route("courses.final-quiz.submit", [$kursusId, $attempt->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ answers: answers })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.redirect) {
                            clearQuizData();
                            // Disable beforeunload alert before redirect
                            isSubmitting = true;
                            window.removeEventListener('beforeunload', handleBeforeUnload);
                            window.location.href = data.redirect;
                        } else {
                            alert(data.error || 'Terjadi kesalahan');
                            modalFinishBtn.disabled = false;
                            modalFinishBtn.textContent = 'Selesai';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Terjadi kesalahan saat menyimpan jawaban');
                        modalFinishBtn.disabled = false;
                        modalFinishBtn.textContent = 'Selesai';
                    });
            }

            modalFinishBtn.addEventListener('click', submitQuiz);

            // Exit confirmation logic
            backLink.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('Final quiz back link clicked');
                exitModal.classList.remove('hidden');
                exitModal.classList.add('flex');
            });

            exitCancelBtn.addEventListener('click', () => {
                exitModal.classList.add('hidden');
                exitModal.classList.remove('flex');
            });

            exitConfirmBtn.addEventListener('click', () => {
                // Don't clear quiz data - progress is preserved in localStorage
                isSubmitting = true; // prevent beforeunload alert
                window.location.href = backLink.dataset.href;
            });

            renderQuestion(current);
        });
    </script>
@endsection