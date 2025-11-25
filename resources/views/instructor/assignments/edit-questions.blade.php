@extends('layouts.instructor')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">{{ $assignment->title }}</h2>
        <p class="text-gray-600 mt-2">Tambah dan kelola soal untuk assignment ini</p>
    </div>

    <!-- Add Questions from Bank -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
            Tambah Soal dari Bank Soal
        </h3>

        <form action="{{ route('instructor.assignments.add-questions', $assignment) }}" method="POST" id="addQuestionsForm">
            @csrf

            @if($questionBanks->isEmpty())
                <div class="text-center py-8 bg-gray-50 rounded-lg">
                    <i class="fas fa-folder-open text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-600">Belum ada bank soal tersedia</p>
                    <a href="{{ route('instructor.question-banks.create') }}" 
                       class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                        Buat Bank Soal
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($questionBanks as $bank)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $bank->title }}</h4>
                                @if($bank->category)
                                <span class="inline-block mt-1 px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
                                    {{ $bank->category }}
                                </span>
                                @endif
                                <p class="text-sm text-gray-600 mt-1">{{ $bank->questions_count }} soal</p>
                            </div>
                            <button type="button" 
                                    onclick="toggleBank('bank-{{ $bank->id }}')"
                                    class="px-3 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 text-sm">
                                <i class="fas fa-chevron-down"></i> Lihat Soal
                            </button>
                        </div>

                        <div id="bank-{{ $bank->id }}" class="hidden mt-4 space-y-2 pl-4 border-l-2 border-blue-200">
                            @foreach($bank->questions as $question)
                            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded hover:bg-gray-100 cursor-pointer">
                                <input type="checkbox" 
                                       name="question_ids[]" 
                                       value="{{ $question->id }}"
                                       {{ $assignment->questions->contains($question->id) ? 'disabled checked' : '' }}
                                       class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded font-medium">
                                            {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                                        </span>
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded font-medium">
                                            {{ $question->points }} poin
                                        </span>
                                        @if($assignment->questions->contains($question->id))
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded font-medium">
                                            <i class="fas fa-check"></i> Sudah ditambahkan
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-700">{{ Str::limit($question->question_text, 100) }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    <button type="submit" 
                            class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        <i class="fas fa-plus mr-2"></i>Tambah Soal yang Dipilih
                    </button>
                </div>
            @endif
        </form>
    </div>

    <!-- Current Questions -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-list text-blue-600 mr-2"></i>
            Soal dalam Assignment ({{ $assignment->questions->count() }})
        </h3>

        @if($assignment->questions->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500">Belum ada soal ditambahkan ke assignment ini</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($assignment->questions as $index => $question)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded font-medium">
                                    #{{ $index + 1 }}
                                </span>
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded font-medium">
                                    {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                                </span>
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded font-medium">
                                    {{ $question->pivot->points ?? $question->points }} poin
                                </span>
                            </div>
                            <p class="text-gray-800">{{ $question->question_text }}</p>
                        </div>
                        <form action="{{ route('instructor.assignments.remove-question', [$assignment, $question]) }}" 
                              method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus soal ini dari assignment?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-3 py-1 text-red-600 hover:bg-red-50 rounded transition text-sm">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>

                    @if($question->type == 'multiple_choice' || $question->type == 'true_false')
                        <div class="space-y-1 pl-4 mt-2">
                            @foreach($question->options as $option)
                            <div class="flex items-center gap-2 text-sm">
                                @if($option->is_correct)
                                    <i class="fas fa-check-circle text-green-600"></i>
                                    <span class="text-green-700 font-medium">{{ $option->option_text }}</span>
                                @else
                                    <i class="far fa-circle text-gray-400"></i>
                                    <span class="text-gray-600">{{ $option->option_text }}</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="font-semibold text-gray-800 mb-1">Status Assignment</h4>
                <p class="text-sm text-gray-600">
                    @if($assignment->is_published)
                        <span class="text-green-600"><i class="fas fa-check-circle"></i> Assignment sudah dipublikasi</span>
                    @else
                        <span class="text-gray-600"><i class="fas fa-clock"></i> Assignment masih draft</span>
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('instructor.assignments.show', $assignment) }}" 
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                @if($assignment->is_published)
                <form action="{{ route('instructor.assignments.unpublish', $assignment) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-medium">
                        <i class="fas fa-eye-slash mr-2"></i>Unpublish
                    </button>
                </form>
                @else
                <form action="{{ route('instructor.assignments.publish', $assignment) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium"
                            {{ $assignment->questions->isEmpty() ? 'disabled' : '' }}>
                        <i class="fas fa-check mr-2"></i>Publish Assignment
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function toggleBank(id) {
    const element = document.getElementById(id);
    element.classList.toggle('hidden');
}
</script>
@endsection
