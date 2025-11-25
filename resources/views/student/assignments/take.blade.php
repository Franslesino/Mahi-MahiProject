@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-4xl">
    <!-- Timer & Header -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 sticky top-0 z-10">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $assignment->title }}</h2>
                <p class="text-sm text-gray-600">Percobaan #{{ $submission->attempt_number }}</p>
            </div>
            
            @if($assignment->duration_minutes)
            <div class="text-center">
                <div id="timer" class="text-3xl font-bold text-blue-600"></div>
                <div class="text-sm text-gray-600">Waktu Tersisa</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Questions Form -->
    <form action="{{ route('student.assignments.submit', [$assignment, $submission]) }}" method="POST" id="assignmentForm">
        @csrf
        
        <div class="space-y-6">
            @foreach($questions as $index => $question)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <!-- Question Header -->
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full font-medium mb-2">
                            Soal #{{ $index + 1 }}
                        </span>
                        @php
                            $typeLabels = [
                                'multiple_choice' => 'Pilihan Ganda',
                                'true_false' => 'Benar/Salah',
                                'essay' => 'Essay',
                                'short_answer' => 'Jawaban Singkat'
                            ];
                        @endphp
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded-full font-medium mb-2 ml-2">
                            {{ $typeLabels[$question->type] }}
                        </span>
                    </div>
                    <span class="text-sm font-medium text-gray-600">{{ $question->pivot->points ?? $question->points }} poin</span>
                </div>

                <!-- Question Text -->
                <div class="mb-4">
                    <p class="text-lg text-gray-800 font-medium">{{ $question->question_text }}</p>
                </div>

                <!-- Answer Options -->
                @if($question->type === 'multiple_choice' || $question->type === 'true_false')
                <div class="space-y-3">
                    @foreach($question->options as $option)
                    <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:border-blue-300 transition">
                        <input type="radio" 
                               name="answers[{{ $question->id }}]" 
                               value="{{ $option->id }}"
                               class="mt-1 mr-3 text-blue-600 focus:ring-blue-500"
                               {{ old("answers.{$question->id}") == $option->id ? 'checked' : '' }}>
                        <span class="text-gray-800">{{ $option->option_text }}</span>
                    </label>
                    @endforeach
                </div>

                @elseif($question->type === 'short_answer')
                <input type="text" 
                       name="answers[{{ $question->id }}]" 
                       value="{{ old("answers.{$question->id}") }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Ketik jawaban Anda...">

                @elseif($question->type === 'essay')
                <textarea name="answers[{{ $question->id }}]" 
                          rows="6"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Ketik jawaban essay Anda...">{{ old("answers.{$question->id}") }}</textarea>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Submit Button -->
        <div class="bg-white rounded-xl shadow-sm p-6 mt-6 sticky bottom-0">
            <div class="flex justify-between items-center">
                <button type="button" 
                        onclick="if(confirm('Yakin ingin keluar? Progress Anda akan hilang.')) window.location.href='{{ route('student.assignments.show', $assignment) }}'"
                        class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
                
                <button type="submit" 
                        onclick="return confirm('Yakin ingin submit jawaban Anda?')"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                    <i class="fas fa-check mr-2"></i>Submit Jawaban
                </button>
            </div>
        </div>
    </form>
</div>

@if($assignment->duration_minutes)
<script>
    // Calculate end time
    const startedAt = new Date('{{ $submission->started_at }}');
    const durationMinutes = {{ $assignment->duration_minutes }};
    const endTime = new Date(startedAt.getTime() + durationMinutes * 60000);

    function updateTimer() {
        const now = new Date();
        const remainingMs = endTime - now;

        if (remainingMs <= 0) {
            // Time's up, auto submit
            document.getElementById('assignmentForm').submit();
            return;
        }

        const minutes = Math.floor(remainingMs / 60000);
        const seconds = Math.floor((remainingMs % 60000) / 1000);

        const timerElement = document.getElementById('timer');
        timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

        // Change color when less than 5 minutes
        if (minutes < 5) {
            timerElement.classList.remove('text-blue-600');
            timerElement.classList.add('text-red-600');
        }
    }

    // Update timer every second
    updateTimer();
    setInterval(updateTimer, 1000);
</script>
@endif
@endsection
