@extends('layouts.app')

@section('title', 'Quiz - ' . ($material->judul ?? $material->title))

@section('content')
<div class="min-h-screen bg-[#f4f2f0] py-6">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex items-center justify-between mb-4">
            <button type="button" onclick="exitQuiz()"
               class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900 transition cursor-pointer bg-gray-100 px-4 py-2 rounded-lg"
               style="position: relative; z-index: 9999; pointer-events: auto !important;">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Kursus</span>
            </button>
            <div class="text-sm text-gray-600 font-semibold">{{ $material->judul ?? $material->title }}</div>
        </div>

        <div class="bg-white rounded-2xl shadow p-6 grid grid-cols-1 lg:grid-cols-5 gap-6">
            <div class="lg:col-span-3">
                <div class="flex items-center justify-between mb-4">
                    <div class="px-4 py-2 rounded-full bg-gray-100 text-sm font-semibold" id="question-counter">1/{{ $assignment->questions->count() }}</div>

                    <div class="text-gray-700 font-semibold">Quiz Materi</div>
                </div>
                <div class="border border-gray-300 rounded-2xl p-6 min-h-[320px] flex flex-col justify-between">
                    <div>
                        <p class="text-base text-gray-800 font-medium mb-4" id="question-text"></p>
                        <div class="space-y-3" id="options-container"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-4 gap-3">
                    <button id="prev-btn" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Sebelumnya</button>
                    <button id="next-btn" class="px-4 py-2 bg-emerald-700 text-white rounded-lg hover:bg-emerald-800 transition">Selanjutnya</button>
                </div>
            </div>
            <div class="lg:col-span-2 bg-gray-100 rounded-xl p-4">
                <div class="flex items-center justify-between mb-3 text-sm font-semibold text-gray-700">
                    <span>Nomor Soal</span>
                </div>
                <div class="grid grid-cols-5 gap-2" id="nav-grid"></div>
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
            <button type="button" id="modal-back-btn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">Cek kembali</button>
            <button type="button" id="modal-finish-btn" class="px-4 py-2 bg-emerald-700 text-white rounded-lg hover:bg-emerald-800 transition">Selesai</button>
        </div>
    </div>
</div>

<form id="complete-form" action="{{ route('courses.materials.quiz.submit', [$course, $material->id]) }}" method="POST" class="hidden">
    @csrf
    <textarea name="answers_json" id="answers_json" class="hidden"></textarea>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const questions = {!! json_encode(
        $assignment->questions->map(function($q){
            return [
                'id' => $q->id,
                'text' => $q->question_text,
                'options' => $q->options->map(function($o){
                    return [
                        'id' => $o->id,
                        'text' => $o->option_text,
                        'is_correct' => $o->is_correct,
                    ];
                })->values(),
            ];
        })->values(),
        JSON_UNESCAPED_UNICODE
    ) !!};

    const storageKey = 'quiz_answers_assignment_{{ $assignment->id }}';
    const storageIndexKey = 'quiz_current_assignment_{{ $assignment->id }}';
    const timerStorageKey = 'quiz_timer_assignment_{{ $assignment->id }}';
    
    window.exitQuiz = function() {
        if(confirm('Yakin ingin keluar? Soal yang anda kerjakan akan mulai lagi dari 0.')) {
            localStorage.removeItem(storageKey);
            localStorage.removeItem(storageIndexKey);
            localStorage.removeItem(timerStorageKey);
            window.location.href = "{{ route('student.course.learn', $course) }}";
        }
    };

    let answers = {};
    try {
        const saved = localStorage.getItem(storageKey);
        if (saved) {
            answers = JSON.parse(saved) || {};
        }
    } catch (_) {}
    let current = 0;
    try {
        const savedIdx = localStorage.getItem(storageIndexKey);
        if (savedIdx !== null) {
            current = Math.max(0, Math.min(questions.length - 1, parseInt(savedIdx, 10) || 0));
        }
    } catch (_) {}

    const questionText = document.getElementById('question-text');
    const optionsContainer = document.getElementById('options-container');
    const counter = document.getElementById('question-counter');
    const navGrid = document.getElementById('nav-grid');
    const nextBtn = document.getElementById('next-btn');
    const prevBtn = document.getElementById('prev-btn');
    const finishModal = document.getElementById('finish-modal');
    const modalBackBtn = document.getElementById('modal-back-btn');
    const modalFinishBtn = document.getElementById('modal-finish-btn');

    const allAnswered = () => {
        return questions.every(q => {
            return answers[q.id] !== undefined && answers[q.id] !== null && answers[q.id] !== '';
        });
    };

    function renderQuestion(index) {
        const q = questions[index];
        counter.textContent = `${index + 1}/${questions.length}`;
        questionText.textContent = q.text;
        optionsContainer.innerHTML = '';
        if (q.options && q.options.length > 0) {
            q.options.forEach(opt => {
                const id = `q${q.id}_opt${opt.id}`;
                const wrap = document.createElement('label');
                wrap.className = 'flex items-center gap-3 text-gray-800 cursor-pointer';
                wrap.innerHTML = `
                    <input type="radio" name="question_${q.id}" id="${id}" value="${opt.id}" class="text-emerald-600">
                    <span>${opt.text}</span>
                `;
                if (answers[q.id] === opt.id) {
                    wrap.querySelector('input').checked = true;
                }
                wrap.querySelector('input').addEventListener('change', () => {
                    answers[q.id] = opt.id;
                    localStorage.setItem(storageKey, JSON.stringify(answers));
                    updateNavStatus();
                    updateActionButton();
                });
                optionsContainer.appendChild(wrap);
            });
        } else {
            // Essay/short answer: render textarea
            const textarea = document.createElement('textarea');
            textarea.className = 'w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-emerald-500';
            textarea.rows = 6;
            textarea.placeholder = 'Tuliskan jawaban Anda...';
            if (answers[q.id]) textarea.value = answers[q.id];
            textarea.addEventListener('input', () => {
                answers[q.id] = textarea.value;
                localStorage.setItem(storageKey, JSON.stringify(answers));
                updateNavStatus();
                updateActionButton();
            });
            optionsContainer.appendChild(textarea);
        }
        updateNavStatus();
        updateActionButton();
    }

    function updateNavStatus() {
        navGrid.innerHTML = '';
        questions.forEach((q, idx) => {
            const btn = document.createElement('button');
            const answered = answers[q.id];
            btn.className = `w-10 h-10 rounded text-sm font-semibold ${idx === current ? 'bg-emerald-700 text-white' : (answered ? 'bg-emerald-500 text-white' : 'bg-gray-300 text-gray-800')}`;
            btn.textContent = idx + 1;
            btn.addEventListener('click', () => {
                current = idx;
                localStorage.setItem(storageIndexKey, current);
                renderQuestion(current);
            });
            navGrid.appendChild(btn);
        });
    }

    function updateActionButton() {
        if (current === questions.length - 1 || allAnswered()) {
            nextBtn.textContent = 'Selesai';
        } else {
            nextBtn.textContent = 'Selanjutnya';
        }
    }

    prevBtn.addEventListener('click', () => {
        if (current > 0) {
            current--;
            localStorage.setItem(storageIndexKey, current);
            renderQuestion(current);
        }
    });
    nextBtn.addEventListener('click', () => {
        if (current < questions.length - 1) {
            current++;
            localStorage.setItem(storageIndexKey, current);
            renderQuestion(current);
        } else {
            nextBtn.textContent = 'Selesai';
            finishModal.classList.remove('hidden');
            finishModal.classList.add('flex');
        }
    });

    modalBackBtn.addEventListener('click', () => {
        finishModal.classList.add('hidden');
        finishModal.classList.remove('flex');
    });

    modalFinishBtn.addEventListener('click', () => {
        // bersihkan simpanan lokal agar tidak nyangkut
        localStorage.removeItem(storageKey);
        localStorage.removeItem(storageIndexKey);
        document.getElementById('answers_json').value = JSON.stringify(answers);
        modalFinishBtn.disabled = true;
        document.getElementById('complete-form').submit();
    });

    renderQuestion(current);
});
</script>
@endsection
