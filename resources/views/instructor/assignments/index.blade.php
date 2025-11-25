@extends('layouts.instructor')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Assignment & Quiz</h2>
            <p class="text-gray-600 mt-2">Kelola quiz dan assignment untuk kursus Anda</p>
        </div>
        <a href="{{ route('instructor.assignments.create') }}" 
           class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            <i class="fas fa-plus"></i>
            Buat Assignment Baru
        </a>
    </div>

    @if($assignments->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Assignment</h3>
            <p class="text-gray-600 mb-6">Buat quiz atau assignment pertama untuk kursus Anda</p>
            <a href="{{ route('instructor.assignments.create') }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                <i class="fas fa-plus"></i>
                Buat Assignment
            </a>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Assignment</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Kursus</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Tipe</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Soal</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Submission</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($assignments as $assignment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <div class="font-medium text-gray-900">{{ $assignment->title }}</div>
                                @if($assignment->due_date)
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    Due: {{ $assignment->due_date->format('d M Y H:i') }}
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $assignment->kursus->judul }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $typeColors = [
                                    'quiz' => 'bg-blue-100 text-blue-700',
                                    'assignment' => 'bg-purple-100 text-purple-700',
                                    'exam' => 'bg-red-100 text-red-700'
                                ];
                            @endphp
                            <span class="inline-block px-3 py-1 {{ $typeColors[$assignment->type] }} text-xs rounded-full font-medium">
                                {{ ucfirst($assignment->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-700">
                            {{ $assignment->questions_count }}
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-700">
                            {{ $assignment->submissions_count }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($assignment->is_published)
                            <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">
                                <i class="fas fa-check-circle"></i> Published
                            </span>
                            @else
                            <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full font-medium">
                                <i class="fas fa-clock"></i> Draft
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('instructor.assignments.show', $assignment) }}" 
                                   class="px-3 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 text-sm"
                                   title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('instructor.assignments.edit', $assignment) }}" 
                                   class="px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('instructor.assignments.destroy', $assignment) }}" 
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus assignment ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-3 py-1 bg-red-50 text-red-600 rounded hover:bg-red-100 text-sm"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $assignments->links() }}
        </div>
    @endif
</div>
@endsection
