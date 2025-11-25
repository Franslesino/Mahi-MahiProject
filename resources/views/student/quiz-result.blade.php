@extends('layouts.app')

@section('title', 'Hasil Quiz - ' . ($material->judul ?? $material->title))

@section('content')
<div class="min-h-screen bg-[#f4f2f0] py-8">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('student.course.learn', $course) }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900 transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kursus Saya</span>
            </a>
            <div class="flex items-center gap-4 text-gray-700">
                <span class="font-semibold">Skor:</span>
                <span class="text-xl font-bold">{{ $score !== null ? $score . '%' : 'Pending' }}</span>
            </div>
            <a href="{{ route('student.course.learn', $course) }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900 transition">
                <span>Lanjut materi berikutnya</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow p-6">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Quiz {{ $material->judul ?? $material->title }}</h2>
                <p class="text-sm text-gray-600">Ringkasan jawaban Anda beserta koreksi</p>
            </div>

            <div class="divide-y divide-gray-200">
                @foreach($results as $idx => $res)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-start sm:gap-4">
                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center font-semibold text-gray-800 mb-3 sm:mb-0">
                            {{ $idx + 1 }}
                        </div>
                        <div class="flex-1 min-w-0 space-y-2">
                            <p class="text-gray-900 font-medium">{{ $res['text'] }}</p>
                            @if($res['correct_answer'])
                                <p class="text-sm text-gray-600">Jawaban benar: <span class="font-semibold text-gray-800">{{ $res['correct_answer'] }}</span></p>
                            @endif
                            @if($res['user_answer'])
                                <p class="text-sm text-gray-600">Jawaban Anda: <span class="font-semibold text-gray-800">{{ $res['user_answer'] }}</span></p>
                            @else
                                <p class="text-sm text-red-600">Anda belum menjawab soal ini.</p>
                            @endif
                        </div>
                        <div class="mt-3 sm:mt-0">
                            @if($res['is_correct'] === true)
                                <span class="text-green-600 text-2xl"><i class="fas fa-check-circle"></i></span>
                            @elseif($res['is_correct'] === false)
                                <span class="text-red-500 text-2xl"><i class="fas fa-times-circle"></i></span>
                            @else
                                <span class="text-gray-400 text-2xl"><i class="fas fa-question-circle"></i></span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
