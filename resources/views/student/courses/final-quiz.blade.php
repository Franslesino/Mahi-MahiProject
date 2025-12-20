@extends('layouts.app')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap');

        :root {
            --primary: #0d9488;
            --primary-dark: #0f766e;
            --primary-light: #14b8a6;
            --accent: #06b6d4;
            --success: #10b981;
            --success-dark: #059669;
            --warning: #f59e0b;
            --danger: #ef4444;
            --danger-dark: #dc2626;
            --neutral-50: #fafafa;
            --neutral-100: #f5f5f5;
            --neutral-200: #e5e5e5;
            --neutral-300: #d4d4d4;
            --neutral-600: #525252;
            --neutral-700: #404040;
            --neutral-800: #262626;
            --neutral-900: #171717;
            --teal-50: #f0fdfa;
            --teal-100: #ccfbf1;
        }

        .quiz-page {
            font-family: 'Outfit', sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .page-header {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(13, 148, 136, 0.1);
            margin-bottom: 2rem;
            border-top: 4px solid var(--primary);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--neutral-900);
            margin: 0 0 0.5rem 0;
            letter-spacing: -0.02em;
        }

        .page-subtitle {
            color: var(--neutral-600);
            font-size: 1rem;
            margin: 0;
            font-weight: 400;
        }

        .hero-card {
            background: linear-gradient(135deg, #0d9488 0%, #14b8a6 50%, #06b6d4 100%);
            border-radius: 24px;
            padding: 3rem;
            color: white;
            box-shadow: 0 20px 40px rgba(13, 148, 136, 0.25);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .hero-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-card::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.3) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .hero-subtitle {
            font-size: 1rem;
            opacity: 0.95;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
        }

        .stat-box {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .stat-box:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        .stat-label {
            font-size: 0.75rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 500;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            font-family: 'Space Mono', monospace;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 992px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        .card-modern {
            background: white;
            border-radius: 20px;
            box-shadow: 0 2px 8px rgba(13, 148, 136, 0.08);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(13, 148, 136, 0.1);
        }

        .card-modern:hover {
            box-shadow: 0 12px 32px rgba(13, 148, 136, 0.15);
            transform: translateY(-2px);
        }

        .card-header-modern {
            padding: 1.5rem 2rem;
            border-bottom: 2px solid var(--teal-100);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--teal-50);
        }

        .card-header-modern h5 {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--neutral-900);
        }

        .card-body-modern {
            padding: 2rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .status-badge.success {
            background: #d1fae5;
            color: var(--success-dark);
        }

        .status-badge.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-modern {
            padding: 0.875rem 1.75rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-modern:active {
            transform: translateY(0);
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
        }

        .btn-primary-modern:hover {
            box-shadow: 0 8px 24px rgba(13, 148, 136, 0.4);
        }

        .btn-success-modern {
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-secondary-modern {
            background: var(--neutral-200);
            color: var(--neutral-800);
        }

        .btn-modern.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .history-item {
            padding: 1.5rem;
            border-bottom: 2px solid var(--teal-100);
            transition: background 0.2s ease;
        }

        .history-item:hover {
            background: var(--teal-50);
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .attempt-badge {
            display: inline-block;
            padding: 0.375rem 0.875rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.875rem;
            font-family: 'Space Mono', monospace;
        }

        .attempt-badge.passed {
            background: var(--success);
            color: white;
        }

        .attempt-badge.failed {
            background: var(--danger);
            color: white;
        }

        .attempt-badge.pending {
            background: var(--neutral-300);
            color: var(--neutral-700);
        }

        .alert-modern {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            border-left: 4px solid;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .alert-modern.info {
            background: var(--teal-100);
            border-color: var(--primary);
            color: var(--primary-dark);
        }

        .alert-modern.danger {
            background: #fee2e2;
            border-color: var(--danger);
            color: var(--danger-dark);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--neutral-600);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--primary);
            opacity: 0.3;
            margin-bottom: 1rem;
        }

        .trophy-icon {
            font-size: 4rem;
            color: var(--warning);
            margin-bottom: 1.5rem;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .animate-delay-1 {
            animation-delay: 0.1s;
            opacity: 0;
        }

        .animate-delay-2 {
            animation-delay: 0.2s;
            opacity: 0;
        }

        .animate-delay-3 {
            animation-delay: 0.3s;
            opacity: 0;
        }

        .info-box {
            background: var(--teal-50);
            border: 2px solid var(--primary);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-top: 1.5rem;
        }

        .info-box i {
            color: var(--primary);
        }

        .info-box small {
            color: var(--primary-dark);
            font-weight: 500;
        }
    </style>

    <div class="quiz-page">
        <div class="container" style="max-width: 1000px; margin: 0 auto; padding: 0 1rem;">
            <!-- Header -->
            <div class="page-header animate-in">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">Final Quiz</h1>
                        <p class="page-subtitle">{{ $kursus->judul }}</p>
                    </div>
                    <a href="{{ route('student.course.learn', $kursus->id) }}" class="btn-modern btn-secondary-modern">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('info'))
                <div class="alert-modern info animate-in animate-delay-1">
                    <i class="fas fa-info-circle"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            <!-- Hero Card -->
            <div class="hero-card animate-in animate-delay-2">
                <div class="hero-content">
                    <h2 class="hero-title">
                        <i class="fas fa-graduation-cap"></i>
                        {{ $finalQuiz->judul_quiz }}
                    </h2>
                    <p class="hero-subtitle">Final Quiz untuk kursus {{ $kursus->judul }}</p>

                    <div class="stats-grid">
                        <div class="stat-box">
                            <div class="stat-label">Nilai Minimum</div>
                            <div class="stat-value">{{ $kursus->min_passing_score }}%</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Maks. Percobaan</div>
                            <div class="stat-value">{{ $kursus->max_quiz_attempts }}x</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Durasi</div>
                            <div class="stat-value">{{ $finalQuiz->durasi_quiz ?? '∞' }} min</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Status</div>
                            @if($hasPassed)
                                <div class="status-badge success">
                                    <i class="fas fa-check-circle"></i> Lulus
                                </div>
                            @else
                                <div class="status-badge warning">
                                    <i class="fas fa-hourglass-half"></i> Belum Lulus
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Main Action Card -->
                <div class="card-modern animate-in animate-delay-3">
                    <div class="card-body-modern">
                        @if($hasPassed)
                            <div style="text-align: center; padding: 2rem 0;">
                                <div class="trophy-icon">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <h3 style="color: var(--success); font-weight: 700; margin-bottom: 0.5rem;">Selamat! Anda Telah
                                    Lulus!</h3>
                                <p style="color: var(--neutral-600); font-size: 1.125rem; margin-bottom: 2rem;">
                                    Nilai terakhir: <strong
                                        style="font-family: 'Space Mono', monospace; color: var(--success);">{{ number_format($latestAttempt->score, 2) }}%</strong>
                                </p>
                                <a href="{{ route('courses.show', $kursus->id) }}" class="btn-modern btn-success-modern">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Kursus
                                </a>
                            </div>
                        @elseif($canRetake)
                            <div style="margin-bottom: 2rem;">
                                <h3 style="font-weight: 700; color: var(--neutral-900); margin-bottom: 0.75rem;">
                                    {{ $attemptCount > 0 ? 'Coba Lagi' : 'Mulai Final Quiz' }}
                                </h3>
                                <p style="color: var(--neutral-600); margin-bottom: 0;">
                                    Percobaan: <strong style="color: var(--primary);">{{ $attemptCount }}</strong> /
                                    <strong>{{ $kursus->max_quiz_attempts }}</strong>
                                </p>
                            </div>

                            <button type="button" class="btn-modern btn-primary-modern" onclick="openStartConfirm(this)"
                                style="width: 100%; justify-content: center; padding: 1.25rem;">
                                <i class="fas fa-play"></i>
                                {{ $attemptCount > 0 ? 'Mulai Percobaan ke-' . ($attemptCount + 1) : 'Mulai Quiz' }}
                            </button>

                            <div class="info-box">
                                <small style="display: block;">
                                    <i class="fas fa-info-circle"></i>
                                    Anda harus lulus final quiz ini untuk mendapatkan sertifikat.
                                </small>
                            </div>
                        @else
                            <div style="text-align: center; padding: 2rem 0;">
                                <i class="fas fa-times-circle"
                                    style="font-size: 4rem; color: var(--danger); margin-bottom: 1.5rem;"></i>
                                <h3 style="color: var(--danger); font-weight: 700; margin-bottom: 0.75rem;">Batas Percobaan
                                    Tercapai</h3>
                                <p style="color: var(--neutral-600); margin-bottom: 2rem;">
                                    Anda telah menggunakan semua percobaan
                                    ({{ $attemptCount }}/{{ $kursus->max_quiz_attempts }}) dan belum mencapai nilai minimum.
                                </p>
                                <div class="alert-modern danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Silakan hubungi instruktur untuk bantuan lebih lanjut.</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- History Card -->
                <div class="card-modern animate-in animate-delay-3">
                    <div class="card-header-modern">
                        <i class="fas fa-history" style="color: var(--primary);"></i>
                        <h5>Riwayat Percobaan</h5>
                    </div>
                    <div style="max-height: 500px; overflow-y: auto;">
                        @if($attempts->count() > 0)
                            @foreach($attempts as $attempt)
                                <div class="history-item">
                                    <div class="d-flex justify-content-between align-items-start" style="margin-bottom: 0.75rem;">
                                        <div>
                                            <div style="font-weight: 600; color: var(--neutral-900); margin-bottom: 0.25rem;">
                                                Percobaan ke-{{ $attempt->attempt_number }}
                                            </div>
                                            <div style="font-size: 0.875rem; color: var(--neutral-600);">
                                                {{ $attempt->completed_at ? $attempt->completed_at->format('d M Y, H:i') : 'Sedang berlangsung' }}
                                            </div>
                                        </div>
                                        @if($attempt->score !== null)
                                            <span class="attempt-badge {{ $attempt->is_passed ? 'passed' : 'failed' }}">
                                                {{ number_format($attempt->score, 2) }}%
                                            </span>
                                        @else
                                            <span class="attempt-badge pending">-</span>
                                        @endif
                                    </div>
                                    <div>
                                        @if($attempt->completed_at)
                                            <a href="{{ route('courses.final-quiz.result', [$kursus->id, $attempt->id]) }}"
                                                class="btn-modern btn-primary-modern"
                                                style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                                <i class="fas fa-eye"></i> Lihat Detail
                                            </a>
                                        @else
                                            <a href="{{ route('courses.final-quiz.take', [$kursus->id, $attempt->id]) }}"
                                                class="btn-modern btn-primary-modern"
                                                style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                                <i class="fas fa-arrow-right"></i> Lanjutkan
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="fas fa-clipboard-list"></i>
                                <p>Belum ada percobaan.<br>Mulai final quiz sekarang.</p>
                            </div>
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
        let startModal;
        let pendingBtn = null;

        document.addEventListener('DOMContentLoaded', () => {
            startModal = document.getElementById('startQuizModal');
        });

        function openStartConfirm(btn) {
            pendingBtn = btn;
            if (startModal) startModal.classList.remove('hidden');
        }

        function closeStartConfirm() {
            if (startModal) startModal.classList.add('hidden');
        }

        function confirmStartQuiz() {
            closeStartConfirm();
            startQuiz(pendingBtn);
        }

        function startQuiz(btnElem) {
            const btn = btnElem || event?.target;
            const statusEl = document.getElementById('startStatus');
            if (statusEl) {
                statusEl.classList.add('d-none');
                statusEl.textContent = '';
            }

            if (btn) {
                btn.disabled = true;
                btn.classList.add('disabled');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memulai...';
            }

            fetch('{{ route("courses.final-quiz.start", $kursus->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (statusEl) {
                            statusEl.textContent = data.error || 'Terjadi kesalahan. Coba lagi.';
                            statusEl.classList.remove('d-none');
                        } else {
                            alert(data.error || 'Terjadi kesalahan');
                        }
                        if (btn) {
                            btn.disabled = false;
                            btn.classList.remove('disabled');
                            btn.innerHTML = '<i class="fas fa-play"></i> Mulai Quiz';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (statusEl) {
                        statusEl.textContent = 'Terjadi kesalahan saat memulai quiz. Coba lagi.';
                        statusEl.classList.remove('d-none');
                    } else {
                        alert('Terjadi kesalahan saat memulai quiz');
                    }
                    if (btn) {
                        btn.disabled = false;
                        btn.classList.remove('disabled');
                        btn.innerHTML = '<i class="fas fa-play"></i> Mulai Quiz';
                    }
                });
        }
    </script>

    <!-- Modal Konfirmasi Mulai Quiz - Item #10: Tampilkan info nilai minimum dan durasi -->
    <div id="startQuizModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-xl">
                    <i class="fas fa-question-circle"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-1">Mulai Final Quiz?</h4>
                    <p class="text-sm text-gray-600 mb-0">Apakah Anda yakin ingin memulai final quiz?</p>
                </div>
            </div>
            
            <!-- Info Box: Nilai Minimum & Durasi -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6 space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600"><i class="fas fa-check-circle mr-2 text-emerald-500"></i>Nilai Minimum Kelulusan</span>
                    <span class="font-bold text-emerald-700">{{ $kursus->min_passing_score }}%</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600"><i class="fas fa-clock mr-2 text-blue-500"></i>Durasi Pengerjaan</span>
                    <span class="font-bold text-blue-700">{{ $finalQuiz->durasi_quiz ?? '∞' }} menit</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600"><i class="fas fa-list mr-2 text-purple-500"></i>Jumlah Soal</span>
                    <span class="font-bold text-purple-700">{{ $finalQuiz->soal->count() }} soal</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600"><i class="fas fa-redo mr-2 text-amber-500"></i>Sisa Percobaan</span>
                    <span class="font-bold text-amber-700">{{ $kursus->max_quiz_attempts - $attemptCount }} dari {{ $kursus->max_quiz_attempts }}</span>
                </div>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                <p class="text-xs text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Setelah memulai quiz, timer akan berjalan. Pastikan Anda siap sebelum memulai.
                </p>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button"
                    class="px-4 py-2 rounded-lg bg-gray-100 text-gray-800 font-semibold hover:bg-gray-200 transition"
                    onclick="closeStartConfirm()">Batal</button>
                <button type="button"
                    class="px-4 py-2 rounded-lg bg-teal-600 text-white font-semibold hover:bg-teal-700 transition"
                    onclick="confirmStartQuiz()">
                    <i class="fas fa-play mr-1"></i> Mulai Sekarang
                </button>
            </div>
        </div>
    </div>
@endsection