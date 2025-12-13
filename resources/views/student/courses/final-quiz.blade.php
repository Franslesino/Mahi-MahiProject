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
                <a href="{{ route('student.courses.show', $kursus->id) }}" class="btn btn-secondary">
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

            <!-- Quiz Info Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-graduation-cap"></i> {{ $finalQuiz->judul_quiz }}</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Nilai Minimum</small>
                                    <strong>{{ $kursus->min_passing_score }}%</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-redo text-info me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Maksimal Percobaan</small>
                                    <strong>{{ $kursus->max_quiz_attempts }}x</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Durasi</small>
                                    <strong>{{ $finalQuiz->durasi_quiz ?? 'Tidak Terbatas' }} menit</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($hasPassed)
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle"></i> 
                            <strong>Selamat!</strong> Anda telah lulus final quiz dengan nilai <strong>{{ number_format($latestAttempt->score, 2) }}%</strong>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Perhatian!</strong> Anda harus lulus final quiz ini untuk mendapatkan sertifikat.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Attempts History -->
            @if($attempts->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Riwayat Percobaan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Percobaan</th>
                                        <th>Tanggal</th>
                                        <th class="text-center">Nilai</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attempts as $attempt)
                                        <tr>
                                            <td>
                                                <strong>Percobaan ke-{{ $attempt->attempt_number }}</strong>
                                            </td>
                                            <td>{{ $attempt->completed_at ? $attempt->completed_at->format('d M Y, H:i') : 'Sedang Berlangsung' }}</td>
                                            <td class="text-center">
                                                @if($attempt->score !== null)
                                                    <span class="badge {{ $attempt->is_passed ? 'bg-success' : 'bg-danger' }}">
                                                        {{ number_format($attempt->score, 2) }}%
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($attempt->completed_at)
                                                    @if($attempt->is_passed)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check"></i> Lulus
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times"></i> Tidak Lulus
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-hourglass-half"></i> Berlangsung
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($attempt->completed_at)
                                                    <a href="{{ route('student.courses.final-quiz.result', [$kursus->id, $attempt->id]) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Lihat Detail
                                                    </a>
                                                @else
                                                    <a href="{{ route('student.courses.final-quiz.take', [$kursus->id, $attempt->id]) }}" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-play"></i> Lanjutkan
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Card -->
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    @if($hasPassed)
                        <i class="fas fa-trophy fa-4x text-warning mb-3"></i>
                        <h4 class="text-success mb-3">Anda Telah Lulus!</h4>
                        <p class="text-muted mb-4">Selamat! Anda telah menyelesaikan final quiz.</p>
                        <a href="{{ route('student.courses.show', $kursus->id) }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Kursus
                        </a>
                    @elseif($canRetake)
                        <i class="fas fa-pen-to-square fa-4x text-primary mb-3"></i>
                        <h4 class="mb-3">
                            @if($attemptCount > 0)
                                Coba Lagi
                            @else
                                Mulai Final Quiz
                            @endif
                        </h4>
                        <p class="text-muted mb-4">
                            Percobaan: <strong>{{ $attemptCount }}</strong> / <strong>{{ $kursus->max_quiz_attempts }}</strong>
                        </p>
                        <button type="button" class="btn btn-primary btn-lg" onclick="startQuiz()">
                            <i class="fas fa-play"></i> 
                            @if($attemptCount > 0)
                                Mulai Percobaan ke-{{ $attemptCount + 1 }}
                            @else
                                Mulai Quiz
                            @endif
                        </button>
                    @else
                        <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                        <h4 class="text-danger mb-3">Batas Percobaan Tercapai</h4>
                        <p class="text-muted mb-4">
                            Anda telah menggunakan semua percobaan ({{ $attemptCount }}/{{ $kursus->max_quiz_attempts }}) 
                            dan belum mencapai nilai minimum.
                        </p>
                        <p class="text-muted">Silakan hubungi instruktur untuk bantuan lebih lanjut.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function startQuiz() {
    if (!confirm('Apakah Anda yakin ingin memulai final quiz? Pastikan Anda siap.')) {
        return;
    }

    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memulai...';

    fetch('{{ route("student.courses.final-quiz.start", $kursus->id) }}', {
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
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-play"></i> Mulai Quiz';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memulai quiz');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-play"></i> Mulai Quiz';
    });
}
</script>
@endsection
