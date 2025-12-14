@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Final Quiz</h2>
                    <p class="text-muted mb-0">{{ $kursus->judul }}</p>
                </div>
                <a href="{{ route('courses.show', $kursus->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- Alert Messages -->
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Hero / Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-white" style="background: linear-gradient(120deg, #0ea5e9, #22c55e);">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <div class="mb-3 mb-md-0">
                            <h4 class="fw-bold mb-1"><i class="fas fa-graduation-cap me-2"></i>{{ $finalQuiz->judul_quiz }}</h4>
                            <p class="mb-0 opacity-75">Final Quiz untuk kursus {{ $kursus->judul }}</p>
                        </div>
                        <div class="d-flex gap-3 flex-wrap">
                            <div class="bg-white bg-opacity-20 rounded-3 px-3 py-2">
                                <small class="d-block opacity-75">Nilai Minimum</small>
                                <span class="fw-bold">{{ $kursus->min_passing_score }}%</span>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-3 px-3 py-2">
                                <small class="d-block opacity-75">Maks. Percobaan</small>
                                <span class="fw-bold">{{ $kursus->max_quiz_attempts }}x</span>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-3 px-3 py-2">
                                <small class="d-block opacity-75">Durasi</small>
                                <span class="fw-bold">{{ $finalQuiz->durasi_quiz ?? 'Tidak Terbatas' }} menit</span>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-3 px-3 py-2">
                                <small class="d-block opacity-75">Status</small>
                                @if($hasPassed)
                                    <span class="badge bg-success text-white"><i class="fas fa-check"></i> Lulus</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half"></i> Belum Lulus</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Left: actions / info -->
                <div class="col-lg-7">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            @if($hasPassed)
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-trophy fa-2x text-warning me-3"></i>
                                    <div>
                                        <h5 class="mb-1 text-success fw-bold">Anda Telah Lulus!</h5>
                                        <p class="mb-0 text-muted">Nilai terakhir: <strong>{{ number_format($latestAttempt->score, 2) }}%</strong></p>
                                    </div>
                                </div>
                                <a href="{{ route('courses.show', $kursus->id) }}" class="btn btn-success">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Kursus
                                </a>
                            @elseif($canRetake)
                                <div class="mb-3">
                                    <h5 class="fw-bold mb-1">{{ $attemptCount > 0 ? 'Coba Lagi' : 'Mulai Final Quiz' }}</h5>
                                    <p class="text-muted mb-0">
                                        Percobaan: <strong>{{ $attemptCount }}</strong> / <strong>{{ $kursus->max_quiz_attempts }}</strong>
                                    </p>
                                </div>
                                <button type="button" class="btn btn-primary btn-lg" onclick="startQuiz(this)">
                                    <i class="fas fa-play"></i>
                                    {{ $attemptCount > 0 ? 'Mulai Percobaan ke-' . ($attemptCount + 1) : 'Mulai Quiz' }}
                                </button>
                                <p class="text-muted small mt-3 mb-0">Anda harus lulus final quiz ini untuk mendapatkan sertifikat.</p>
                            @else
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-times-circle fa-2x text-danger me-3"></i>
                                    <div>
                                        <h5 class="text-danger fw-bold mb-1">Batas Percobaan Tercapai</h5>
                                        <p class="text-muted mb-0">Anda telah menggunakan semua percobaan ({{ $attemptCount }}/{{ $kursus->max_quiz_attempts }}) dan belum mencapai nilai minimum.</p>
                                    </div>
                                </div>
                                <p class="text-muted mb-3">Silakan hubungi instruktur untuk bantuan lebih lanjut.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: history -->
                <div class="col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light d-flex align-items-center">
                            <i class="fas fa-history text-primary me-2"></i>
                            <strong class="mb-0">Riwayat Percobaan</strong>
                        </div>
                        <div class="card-body">
                            @if($attempts->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($attempts as $attempt)
                                        <div class="list-group-item px-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="fw-bold">Percobaan ke-{{ $attempt->attempt_number }}</div>
                                                    <div class="text-muted small">
                                                        {{ $attempt->completed_at ? $attempt->completed_at->format('d M Y, H:i') : 'Sedang berlangsung' }}
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    @if($attempt->score !== null)
                                                        <span class="badge {{ $attempt->is_passed ? 'bg-success' : 'bg-danger' }}">{{ number_format($attempt->score, 2) }}%</span>
                                                    @else
                                                        <span class="badge bg-secondary">-</span>
                                                    @endif
                                                    <div class="mt-2">
                                                        @if($attempt->completed_at)
                                                            <a href="{{ route('courses.final-quiz.result', [$kursus->id, $attempt->id]) }}" class="btn btn-sm btn-outline-primary">
                                                                Lihat Detail
                                                            </a>
                                                        @else
                                                            <a href="{{ route('courses.final-quiz.take', [$kursus->id, $attempt->id]) }}" class="btn btn-sm btn-primary">
                                                                Lanjutkan
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">Belum ada percobaan. Mulai final quiz sekarang.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function startQuiz(btnElem) {
    if (!confirm('Apakah Anda yakin ingin memulai final quiz? Pastikan Anda siap.')) {
        return;
    }

    const btn = btnElem || event?.target;
    if (btn) {
        btn.disabled = true;
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
            alert(data.error || 'Terjadi kesalahan');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-play"></i> Mulai Quiz';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memulai quiz');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-play"></i> Mulai Quiz';
        }
    });
}
</script>
@endsection
