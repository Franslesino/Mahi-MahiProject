@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Assignment & Quiz</h2>

    @if($assignments->isEmpty())
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-clipboard-list text-gray-400 text-3xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Assignment</h3>
        <p class="text-gray-500">Tidak ada assignment atau quiz yang tersedia saat ini.</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($assignments as $assignment)
        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6">
            <!-- Header -->
            <div class="flex justify-between items-start mb-4">
                @php
                    $typeColors = [
                        'quiz' => 'bg-blue-100 text-blue-700',
                        'assignment' => 'bg-purple-100 text-purple-700',
                        'exam' => 'bg-red-100 text-red-700'
                    ];
                @endphp
                <span class="px-3 py-1 {{ $typeColors[$assignment->type] }} text-xs rounded-full font-medium">
                    {{ ucfirst($assignment->type) }}
                </span>
                
                @if($assignment->submissions->first())
                    @php $submission = $assignment->submissions->first(); @endphp
                    @if($submission->status === 'in_progress')
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-medium">
                        <i class="fas fa-hourglass-half"></i> Sedang Dikerjakan
                    </span>
                    @elseif($submission->isGraded())
                        @if($submission->isPassed())
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">
                            <i class="fas fa-check"></i> Lulus
                        </span>
                        @else
                        <span class="px-3 py-1 bg-red-100 text-red-700 text-xs rounded-full font-medium">
                            <i class="fas fa-times"></i> Tidak Lulus
                        </span>
                        @endif
                    @else
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full font-medium">
                        <i class="fas fa-clock"></i> Menunggu Penilaian
                    </span>
                    @endif
                @endif
            </div>

            <!-- Title -->
            <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2">{{ $assignment->title }}</h3>
            <p class="text-sm text-gray-600 mb-4">{{ $assignment->kursus->judul }}</p>

            <!-- Details -->
            <div class="space-y-2 text-sm text-gray-600 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-question-circle w-5"></i>
                    <span>{{ $assignment->questions->count() }} Soal</span>
                </div>
                
                @if($assignment->duration_minutes)
                <div class="flex items-center">
                    <i class="fas fa-clock w-5"></i>
                    <span>{{ $assignment->duration_minutes }} menit</span>
                </div>
                @endif

                @if($assignment->due_date)
                <div class="flex items-center">
                    <i class="fas fa-calendar w-5"></i>
                    <span>
                        @if($assignment->due_date->isPast())
                            <span class="text-red-600">Terlewat</span>
                        @else
                            {{ $assignment->due_date->format('d M Y H:i') }}
                        @endif
                    </span>
                </div>
                @endif

                @if($assignment->submissions->first() && $assignment->submissions->first()->isGraded())
                <div class="flex items-center font-semibold text-gray-800">
                    <i class="fas fa-star w-5"></i>
                    <span>{{ number_format($assignment->submissions->first()->percentage, 1) }}%</span>
                </div>
                @endif
            </div>

            <!-- Action Button -->
            @php
                $submission = $assignment->submissions->first();
            @endphp
            
            @if($submission && $submission->status === 'in_progress')
            <a href="{{ route('student.assignments.take', [$assignment, $submission]) }}" 
               class="block w-full text-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-medium">
                <i class="fas fa-play mr-2"></i>Lanjutkan
            </a>
            @elseif($submission && $submission->isGraded())
            <a href="{{ route('student.assignments.result', [$assignment, $submission]) }}" 
               class="block w-full text-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-medium">
                <i class="fas fa-eye mr-2"></i>Lihat Hasil
            </a>
            @else
            <a href="{{ route('student.assignments.show', $assignment) }}" 
               class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                <i class="fas fa-arrow-right mr-2"></i>Lihat Detail
            </a>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
