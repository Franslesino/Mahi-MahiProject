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
                <div class="flex items-center gap-2">
                    <span class="text-xl font-bold">{{ $score !== null ? $score . '%' : 'Pending' }}</span>
                    @if(isset($passingScore))
                        @if($score !== null && $score >= $passingScore)
                            <span class="px-2 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-semibold">Lulus</span>
                        @elseif($score !== null)
                            <span class="px-2 py-1 bg-red-50 text-red-700 rounded-full text-xs font-semibold">Tidak Lulus</span>
                        @endif
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if(!empty($canRetake))
                    <a href="{{ route('courses.materials.quiz', [$course, $material->id]) }}?retake=1"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-amber-100 text-amber-800 rounded-lg text-sm font-semibold hover:bg-amber-200 transition">
                        <i class="fas fa-redo"></i>
                        Coba Lagi
                    </a>
                @endif
                <a href="{{ route('student.course.learn', $course) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-700 text-white rounded-lg text-sm font-semibold hover:bg-emerald-800 transition">
                    <span>Lanjut Materi Berikutnya</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        @php
            $isPassed = $passingScore ? ($score !== null && $score >= $passingScore) : true;
        @endphp

        <div class="bg-white rounded-2xl shadow p-6">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Quiz {{ $material->judul ?? $material->title }}</h2>
                <p class="text-sm text-gray-600">Ringkasan jawaban Anda beserta koreksi</p>
            </div>

            @if($isPassed)
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
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">Review jawaban akan tersedia setelah Anda mencapai nilai minimum.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
