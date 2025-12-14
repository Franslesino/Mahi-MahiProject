@extends('layouts.instructor')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('instructor.courses.final-quiz.edit', $kursus->id) }}" 
               class="text-blue-600 hover:text-blue-700 mb-2 inline-flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <h2 class="text-2xl font-bold text-gray-800 mt-2">Statistik Final Quiz</h2>
            <p class="text-gray-600 mt-1">{{ $kursus->judul }}</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border-2 border-blue-200 p-6 text-center hover:shadow-md transition">
            <div class="text-4xl font-bold text-blue-600 mb-2">{{ $statistics->count() }}</div>
            <p class="text-gray-600 font-semibold">Total Peserta</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border-2 border-green-200 p-6 text-center hover:shadow-md transition">
            <div class="text-4xl font-bold text-green-600 mb-2">{{ $statistics->where('has_passed', 1)->count() }}</div>
            <p class="text-gray-600 font-semibold">Lulus</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border-2 border-red-200 p-6 text-center hover:shadow-md transition">
            <div class="text-4xl font-bold text-red-600 mb-2">{{ $statistics->where('has_passed', 0)->count() }}</div>
            <p class="text-gray-600 font-semibold">Belum Lulus</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border-2 border-purple-200 p-6 text-center hover:shadow-md transition">
            <div class="text-4xl font-bold text-purple-600 mb-2">
                {{ $statistics->count() > 0 ? number_format($statistics->avg('best_score'), 2) : 0 }}%
            </div>
            <p class="text-gray-600 font-semibold">Rata-rata Nilai</p>
        </div>
    </div>

    <!-- Statistics Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-4 rounded-t-lg">
            <h5 class="text-lg font-semibold mb-0">
                <i class="fas fa-chart-bar"></i> Detail Statistik Peserta
            </h5>
        </div>
        <div class="p-6">
            @if($statistics->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b-2 border-gray-200">
                                <th class="px-4 py-3 text-left text-sm font-bold text-gray-700">#</th>
                                <th class="px-4 py-3 text-left text-sm font-bold text-gray-700">Nama Peserta</th>
                                <th class="px-4 py-3 text-left text-sm font-bold text-gray-700">Email</th>
                                <th class="px-4 py-3 text-center text-sm font-bold text-gray-700">Total Percobaan</th>
                                <th class="px-4 py-3 text-center text-sm font-bold text-gray-700">Nilai Terbaik</th>
                                <th class="px-4 py-3 text-center text-sm font-bold text-gray-700">Nilai Rata-rata</th>
                                <th class="px-4 py-3 text-center text-sm font-bold text-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($statistics as $index => $stat)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                {{ substr($stat->user->name ?? 'U', 0, 1) }}
                                            </div>
                                            <span class="font-semibold text-gray-900">{{ $stat->user->name ?? 'Unknown' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600">{{ $stat->user->email ?? '-' }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-semibold">
                                            {{ $stat->total_attempts }} / {{ $kursus->max_quiz_attempts }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="text-lg font-bold {{ $stat->best_score >= $kursus->min_passing_score ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($stat->best_score, 2) }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center text-sm font-semibold text-gray-700">
                                        {{ number_format($stat->avg_score, 2) }}%
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        @if($stat->has_passed)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                                                <i class="fas fa-check-circle"></i>
                                                Lulus
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">
                                                <i class="fas fa-times-circle"></i>
                                                Belum Lulus
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="mb-4">
                        <i class="fas fa-chart-line text-gray-300 text-6xl"></i>
                    </div>
                    <h5 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Data Statistik</h5>
                    <p class="text-gray-500">
                        Belum ada peserta yang mengerjakan final quiz. Data akan muncul setelah ada peserta yang mengerjakan.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Additional styles if needed */
    .avatar-circle {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 14px;
    }
</style>
@endpush