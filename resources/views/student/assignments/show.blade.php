@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-4xl">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm p-8 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                @php
                    $typeColors = [
                        'quiz' => 'bg-blue-100 text-blue-700',
                        'assignment' => 'bg-purple-100 text-purple-700',
                        'exam' => 'bg-red-100 text-red-700'
                    ];
                @endphp
                <span class="px-3 py-1 {{ $typeColors[$assignment->type] }} text-sm rounded-full font-medium">
                    {{ ucfirst($assignment->type) }}
                </span>
                
                <h2 class="text-3xl font-bold text-gray-800 mt-4">{{ $assignment->title }}</h2>
                <p class="text-gray-600 mt-2">{{ $assignment->kursus->judul }}</p>
            </div>
        </div>

        @if($assignment->description)
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-gray-800 mb-2">Deskripsi</h3>
            <p class="text-gray-700">{{ $assignment->description }}</p>
        </div>
        @endif

        <!-- Assignment Info -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <i class="fas fa-question-circle text-blue-600 text-2xl mb-2"></i>
                <div class="text-2xl font-bold text-gray-800">{{ $assignment->questions->count() }}</div>
                <div class="text-sm text-gray-600">Soal</div>
            </div>

            @if($assignment->duration_minutes)
            <div class="bg-purple-50 rounded-lg p-4 text-center">
                <i class="fas fa-clock text-purple-600 text-2xl mb-2"></i>
                <div class="text-2xl font-bold text-gray-800">{{ $assignment->duration_minutes }}</div>
                <div class="text-sm text-gray-600">Menit</div>
            </div>
            @endif

            <div class="bg-green-50 rounded-lg p-4 text-center">
                <i class="fas fa-trophy text-green-600 text-2xl mb-2"></i>
                <div class="text-2xl font-bold text-gray-800">{{ $assignment->passing_score }}%</div>
                <div class="text-sm text-gray-600">Passing Score</div>
            </div>

            <div class="bg-yellow-50 rounded-lg p-4 text-center">
                <i class="fas fa-star text-yellow-600 text-2xl mb-2"></i>
                <div class="text-2xl font-bold text-gray-800">{{ $assignment->total_points }}</div>
                <div class="text-sm text-gray-600">Total Poin</div>
            </div>
        </div>

        <!-- Date Info -->
        <div class="border-t pt-4 space-y-2 text-sm text-gray-700">
            @if($assignment->start_date)
            <div class="flex items-center">
                <i class="fas fa-calendar-check w-6 text-gray-500"></i>
                <span>Mulai: <strong>{{ $assignment->start_date->format('d M Y H:i') }}</strong></span>
            </div>
            @endif

            @if($assignment->due_date)
            <div class="flex items-center">
                <i class="fas fa-calendar-times w-6 text-gray-500"></i>
                <span>Deadline: <strong class="{{ $assignment->due_date->isPast() ? 'text-red-600' : '' }}">
                    {{ $assignment->due_date->format('d M Y H:i') }}
                </strong></span>
            </div>
            @endif

            @if($assignment->allow_multiple_attempts)
            <div class="flex items-center">
                <i class="fas fa-redo w-6 text-gray-500"></i>
                <span>Multiple attempts diperbolehkan
                    @if($assignment->max_attempts)
                        (Max: {{ $assignment->max_attempts }})
                    @endif
                </span>
            </div>
            @endif
        </div>
    </div>

    <!-- Previous Attempts -->
    @if($submissions->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Riwayat Percobaan</h3>
        
        <div class="space-y-3">
            @foreach($submissions as $submission)
            <div class="border rounded-lg p-4 flex justify-between items-center">
                <div>
                    <div class="font-semibold text-gray-800">Percobaan #{{ $submission->attempt_number }}</div>
                    <div class="text-sm text-gray-600">
                        {{ $submission->submitted_at ? $submission->submitted_at->format('d M Y H:i') : 'Belum selesai' }}
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    @if($submission->status === 'in_progress')
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm rounded-full font-medium">
                        Sedang Dikerjakan
                    </span>
                    @elseif($submission->isGraded())
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-800">{{ number_format($submission->percentage, 1) }}%</div>
                        <div class="text-sm {{ $submission->isPassed() ? 'text-green-600' : 'text-red-600' }}">
                            {{ $submission->isPassed() ? 'Lulus' : 'Tidak Lulus' }}
                        </div>
                    </div>
                    @else
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full font-medium">
                        Menunggu Penilaian
                    </span>
                    @endif

                    @if($submission->status !== 'in_progress')
                    <a href="{{ route('student.assignments.result', [$assignment, $submission]) }}" 
                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        <i class="fas fa-eye"></i>
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Start Button -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        @if($inProgressSubmission)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3 mt-1"></i>
                <div>
                    <h4 class="font-semibold text-yellow-800">Anda memiliki percobaan yang belum selesai</h4>
                    <p class="text-sm text-yellow-700 mt-1">Silakan lanjutkan atau submit percobaan sebelumnya terlebih dahulu.</p>
                </div>
            </div>
        </div>
        <a href="{{ route('student.assignments.take', [$assignment, $inProgressSubmission]) }}" 
           class="block w-full text-center px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-semibold text-lg">
            <i class="fas fa-play mr-2"></i>Lanjutkan Mengerjakan
        </a>
        @elseif($canStartNew)
        <form action="{{ route('student.assignments.start', $assignment) }}" method="POST">
            @csrf
            <button type="submit" 
                    class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-lg">
                <i class="fas fa-play mr-2"></i>Mulai Mengerjakan
            </button>
        </form>
        @else
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
            <i class="fas fa-times-circle text-red-600 text-2xl mb-2"></i>
            <p class="text-red-700 font-medium">Anda sudah mencapai batas maksimal percobaan</p>
        </div>
        @endif
    </div>
</div>
@endsection
