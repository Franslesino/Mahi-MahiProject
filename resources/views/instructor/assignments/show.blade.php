@extends('layouts.instructor')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">{{ $assignment->title }}</h2>
            <p class="text-gray-600 mt-2">{{ $assignment->kursus->judul }}</p>
            <div class="flex items-center gap-3 mt-3">
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
                @if($assignment->is_published)
                <span class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full font-medium">
                    <i class="fas fa-check-circle"></i> Published
                </span>
                @else
                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full font-medium">
                    <i class="fas fa-clock"></i> Draft
                </span>
                @endif
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('instructor.assignments.edit', $assignment) }}" 
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('instructor.assignments.edit-questions', $assignment) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                <i class="fas fa-list mr-2"></i>Kelola Soal
            </a>
        </div>
    </div>

    <!-- Assignment Info -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Soal</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $assignment->questions->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-question-circle text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Submission</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $assignment->submissions->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Passing Score</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $assignment->passing_score }}%</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-trophy text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Details -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Assignment</h3>
        
        @if($assignment->description)
        <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-1">Deskripsi</h4>
            <p class="text-gray-600">{{ $assignment->description }}</p>
        </div>
        @endif

        <div class="grid grid-cols-2 gap-4 text-sm">
            @if($assignment->duration_minutes)
            <div>
                <span class="text-gray-600">Durasi:</span>
                <span class="font-medium text-gray-800">{{ $assignment->duration_minutes }} menit</span>
            </div>
            @endif

            @if($assignment->start_date)
            <div>
                <span class="text-gray-600">Mulai:</span>
                <span class="font-medium text-gray-800">{{ $assignment->start_date->format('d M Y H:i') }}</span>
            </div>
            @endif

            @if($assignment->due_date)
            <div>
                <span class="text-gray-600">Deadline:</span>
                <span class="font-medium text-gray-800">{{ $assignment->due_date->format('d M Y H:i') }}</span>
            </div>
            @endif

            <div>
                <span class="text-gray-600">Total Poin:</span>
                <span class="font-medium text-gray-800">{{ $assignment->total_points }}</span>
            </div>

            <div>
                <span class="text-gray-600">Multiple Attempts:</span>
                <span class="font-medium text-gray-800">
                    {{ $assignment->allow_multiple_attempts ? 'Ya' : 'Tidak' }}
                    @if($assignment->allow_multiple_attempts && $assignment->max_attempts)
                        (Max: {{ $assignment->max_attempts }})
                    @endif
                </span>
            </div>

            <div>
                <span class="text-gray-600">Tampilkan Hasil:</span>
                <span class="font-medium text-gray-800">
                    {{ $assignment->show_results_immediately ? 'Segera' : 'Manual' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Recent Submissions -->
    @if($assignment->submissions->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Submission Terbaru</h3>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Siswa
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Submitted At
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Score
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($assignment->submissions->take(10) as $submission)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-blue-600 font-medium text-sm">
                                        {{ substr($submission->user->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $submission->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $submission->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $submission->submitted_at?->format('d M Y H:i') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($submission->isGraded())
                            <div class="text-sm">
                                <div class="font-bold text-gray-900">{{ number_format($submission->score, 1) }}</div>
                                <div class="text-gray-500">{{ number_format($submission->percentage, 1) }}%</div>
                            </div>
                            @else
                            <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($submission->status == 'graded')
                                @if($submission->isPassed())
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">
                                    <i class="fas fa-check"></i> Lulus
                                </span>
                                @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full font-medium">
                                    <i class="fas fa-times"></i> Tidak Lulus
                                </span>
                                @endif
                            @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full font-medium">
                                {{ ucfirst($submission->status) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <button class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
