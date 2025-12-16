@php
    // Tentukan route prefix berdasarkan role user
    $isAdmin = auth()->user()->role === 'admin';
    $layout = $isAdmin ? 'layouts.admin' : 'layouts.instructor';
    $routePrefix = $isAdmin ? 'admin.courses' : 'instructor.courses';
    $materialRoutePrefix = $isAdmin ? 'admin.courses.materials' : 'instructor.materials';
@endphp

@extends($layout)

@section('content')
@php
    // Tentukan route prefix berdasarkan role user
    $isAdmin = auth()->user()->role === 'admin';
    $routePrefix = $isAdmin ? 'admin.courses' : 'instructor.courses';
    $materialRoutePrefix = $isAdmin ? 'admin.courses.materials' : 'instructor.materials';
@endphp

<div class="p-8">
    <!-- Header -->
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex-1">
            <h2 class="text-2xl font-bold text-gray-800 mt-2">{{ $course->judul }}</h2>
            <p class="text-gray-600 mt-1">{{ $course->deskripsi }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.courses.final-quiz.edit', $course->id) }}" 
               class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition font-medium flex items-center gap-2">
                <i class="fas fa-graduation-cap"></i>
                <span>Final Quiz</span>
            </a>
            <button onclick="openSectionModal()" 
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium flex items-center gap-2">
                <i class="fas fa-folder-plus"></i>
                <span>Tambah Modul</span>
            </button>
            <button onclick="scrollToAddMaterial()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Tambah Materi</span>
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-folder text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Modul</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sections->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Materi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalMaterials }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-video text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Video</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalVideos }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-orange-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Siswa</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $studentsCount ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Final Quiz Statistics (if exists) -->
    @if($course->require_final_quiz && $course->final_quiz_id)
        @php
            // Hitung total peserta unik dan peserta lulus unik
            $totalParticipants = DB::table('quiz_attempts')
                ->where('kursus_id', $course->id)
                ->where('quiz_id', $course->final_quiz_id)
                ->distinct('user_id')
                ->count('user_id');

            $passedCount = DB::table('quiz_attempts')
                ->where('kursus_id', $course->id)
                ->where('quiz_id', $course->final_quiz_id)
                ->where('is_passed', true)
                ->distinct('user_id')
                ->count('user_id');

            $scoreAggregate = DB::table('quiz_attempts')
                ->select(
                    DB::raw('AVG(score) as avg_score'),
                    DB::raw('MAX(score) as max_score'),
                    DB::raw('MIN(score) as min_score')
                )
                ->where('kursus_id', $course->id)
                ->where('quiz_id', $course->final_quiz_id)
                ->first();
            
            $failedCount = max($totalParticipants - $passedCount, 0);
            $avgScore = $scoreAggregate->avg_score ?? 0;
            $maxScore = $scoreAggregate->max_score ?? 0;
            $minScore = $scoreAggregate->min_score ?? 0;
            $passRate = $totalParticipants > 0 ? ($passedCount / $totalParticipants) * 100 : 0;
        @endphp

        <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg shadow-sm border-2 border-purple-200 mb-6">
            <div class="p-6 border-b border-purple-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-graduation-cap text-purple-600"></i>
                    Statistik Final Quiz
                </h3>
                <div class="flex items-center gap-2">
                    @if($course->finalQuiz->is_active)
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                            <i class="fas fa-check-circle"></i> Aktif
                        </span>
                    @else
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">
                            <i class="fas fa-pause-circle"></i> Nonaktif
                        </span>
                    @endif
                    <a href="{{ route('admin.courses.final-quiz.statistics', $course->id) }}" 
                       class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-semibold inline-flex items-center gap-2">
                        <i class="fas fa-chart-bar"></i> Detail Statistik
                    </a>
                </div>
            </div>

            @if($totalParticipants > 0)
                <div class="p-6">
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                            <div class="text-3xl font-bold text-blue-600 mb-1">{{ $totalParticipants }}</div>
                            <div class="text-xs text-gray-600 font-semibold">Total Peserta</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                            <div class="text-3xl font-bold text-green-600 mb-1">{{ $passedCount }}</div>
                            <div class="text-xs text-gray-600 font-semibold">Lulus</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                            <div class="text-3xl font-bold text-red-600 mb-1">{{ $failedCount }}</div>
                            <div class="text-xs text-gray-600 font-semibold">Belum Lulus</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                            <div class="text-3xl font-bold text-purple-600 mb-1">{{ number_format($avgScore, 1) }}%</div>
                            <div class="text-xs text-gray-600 font-semibold">Rata-rata Nilai</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                            <div class="text-3xl font-bold text-indigo-600 mb-1">{{ number_format($passRate, 1) }}%</div>
                            <div class="text-xs text-gray-600 font-semibold">Pass Rate</div>
                        </div>
                    </div>

                    <!-- Pass Rate Bar -->
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-700">Tingkat Kelulusan</span>
                            <span class="text-sm font-bold text-purple-600">{{ number_format($passRate, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden">
                            <div class="h-full flex">
                                @if($passedCount > 0)
                                    <div class="bg-gradient-to-r from-green-500 to-green-600 flex items-center justify-center text-white text-xs font-bold transition-all duration-500"
                                         style="width: {{ ($passedCount / $totalParticipants) * 100 }}%">
                                        {{ $passedCount }}
                                    </div>
                                @endif
                                @if($failedCount > 0)
                                    <div class="bg-gradient-to-r from-red-500 to-red-600 flex items-center justify-center text-white text-xs font-bold transition-all duration-500"
                                         style="width: {{ ($failedCount / $totalParticipants) * 100 }}%">
                                        {{ $failedCount }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-2 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 bg-green-500 rounded"></span>
                                <span class="text-gray-600">Lulus (≥{{ $course->min_passing_score }}%)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 bg-red-500 rounded"></span>
                                <span class="text-gray-600">Belum Lulus</span>
                            </div>
                        </div>
                    </div>

                    <!-- Score Distribution -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 text-center">
                            <div class="text-sm text-gray-600 mb-1">Nilai Tertinggi</div>
                            <div class="text-2xl font-bold text-green-600">{{ number_format($maxScore, 1) }}%</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 text-center">
                            <div class="text-sm text-gray-600 mb-1">Nilai Rata-rata</div>
                            <div class="text-2xl font-bold text-blue-600">{{ number_format($avgScore, 1) }}%</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 text-center">
                            <div class="text-sm text-gray-600 mb-1">Nilai Terendah</div>
                            <div class="text-2xl font-bold text-red-600">{{ number_format($minScore, 1) }}%</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-8 text-center">
                    <i class="fas fa-inbox text-gray-300 text-5xl mb-3"></i>
                    <p class="text-gray-600 font-semibold">Belum ada peserta yang mengerjakan final quiz</p>
                    <p class="text-gray-500 text-sm mt-1">Statistik akan muncul setelah ada peserta yang mengerjakan</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Progress Peserta -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-users text-blue-600"></i>
                Progress & Nilai Peserta ({{ $participantProgress->count() }})
            </h3>
            <span class="text-xs px-2 py-1 bg-blue-50 text-blue-700 rounded-full font-semibold">
                {{ $totalMaterials }} materi • {{ $assignmentCount }} tugas/quiz
            </span>
        </div>

        @if($participantProgress->isEmpty())
            <div class="p-6 text-center text-gray-500">
                <i class="fas fa-user-slash text-3xl mb-2"></i>
                <p class="text-sm">Belum ada peserta yang terdaftar</p>
            </div>
        @else
        <div class="flex flex-col sm:flex-row gap-3 mb-4">
            <input id="insParticipantSearch" type="text" placeholder="Cari peserta..." class="w-full sm:w-1/3 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            <select id="insParticipantFilter" class="w-full sm:w-40 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="all">Semua status</option>
                <option value="passed">Lulus</option>
                <option value="failed">Tidak lulus</option>
                <option value="none">Belum ada nilai</option>
            </select>
            <select id="insParticipantSort" class="w-full sm:w-48 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="progress_desc">Progress tertinggi</option>
                <option value="progress_asc">Progress terendah</option>
                <option value="score_desc">Nilai tertinggi</option>
                <option value="score_asc">Nilai terendah</option>
                <option value="last_submit_desc">Terakhir submit terbaru</option>
                <option value="last_submit_asc">Terakhir submit terlama</option>
            </select>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full" id="insParticipantTable">
                <thead class="bg-gray-50 border-b sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Peserta</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Progress</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Nilai Terbaik</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Terakhir Submit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($participantProgress as $participant)
                        @php
                            $user = $participant['user'];
                            $avatar = $user->avatar_url ?? $user->avatar ?? $user->profile_url ?? null;
                            $initials = $user->initials ?? strtoupper(substr($user->name ?? 'U', 0, 2));
                            $bestPct = $participant['best_percentage'];
                            $passStatus = $participant['is_passed'] === null ? 'none' : ($participant['is_passed'] ? 'passed' : 'failed');
                            $progressVal = $participant['progress'] ?? 0;
                            $lastSubmitTs = $participant['last_submitted_at'] ? \Carbon\Carbon::parse($participant['last_submitted_at'])->timestamp : 0;
                            $barColor = $progressVal >= 80 ? 'bg-emerald-500' : ($progressVal >= 50 ? 'bg-amber-400' : 'bg-red-500');
                        @endphp
                        <tr class="hover:bg-gray-50 ins-participant-row"
                            data-name="{{ strtolower($user->name ?? '') }}"
                            data-email="{{ strtolower($user->email ?? '') }}"
                            data-progress="{{ $progressVal }}"
                            data-score="{{ $bestPct ?? -1 }}"
                            data-last="{{ $lastSubmitTs }}"
                            data-status="{{ $passStatus }}">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($avatar)
                                        <img src="{{ $avatar }}" alt="{{ $user->name ?? 'avatar' }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-semibold text-sm">
                                            {{ $initials }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $user->name ?? 'Peserta' }}</p>
                                        <p class="text-xs text-gray-600 truncate">{{ $user->email ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="h-full {{ $barColor }}" style="width: {{ $progressVal }}%"></div>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-800 min-w-[56px] text-right">
                                        {{ $progressVal }}%
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $participant['completed'] }}/{{ $participant['total'] }} materi selesai
                                </p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(!is_null($bestPct))
                                    <div class="flex flex-col items-center gap-1">
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ number_format($bestPct, 1) }}% <span class="text-xs text-gray-500">(attempt {{ $participant['best_attempt'] ?? '-' }})</span>
                                        </div>
                                        <div class="text-xs text-gray-500">Skor: {{ number_format($participant['best_score'] ?? 0, 1) }}</div>
                                        @if($participant['is_passed'] === true)
                                            <span class="px-2 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[11px] font-semibold">Lulus</span>
                                        @elseif($participant['is_passed'] === false)
                                            <span class="px-2 py-1 bg-red-50 text-red-700 rounded-full text-[11px] font-semibold">Tidak lulus</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-[11px] font-semibold">Belum ada nilai</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">
                                {{ $participant['last_submitted_at'] ? \Carbon\Carbon::parse($participant['last_submitted_at'])->format('d M Y H:i') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

   

    <!-- Modul Kursus -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Modul Kursus</h3>
            <button onclick="toggleAllSections()" class="text-sm text-blue-600 hover:text-blue-700">
                <i class="fas fa-expand-alt mr-1"></i> Toggle Semua
            </button>
        </div>

        @if($sections->isEmpty())
            <div class="p-12 text-center">
                <i class="fas fa-folder-open text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Modul</h3>
                <p class="text-gray-500 mb-6">Mulai dengan menambahkan modul/section untuk kursus ini</p>
                <button onclick="openSectionModal()" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                    <i class="fas fa-folder-plus"></i>
                    <span>Tambah Modul Pertama</span>
                </button>
            </div>
        @else
            <div class="divide-y divide-gray-200">
                @foreach($sections as $section)
                    <div class="section-container" data-section-id="{{ $section->id }}">
                        <!-- Section Header -->
                        <div class="p-4 hover:bg-gray-50 cursor-pointer flex items-center justify-between" 
                             onclick="toggleSection({{ $section->id }})">
                            <div class="flex items-center gap-3 flex-1">
                                <button class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 transition">
                                    <i class="fas fa-chevron-right section-chevron transition-transform" id="chevron-{{ $section->id }}"></i>
                                </button>
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $section->title }}</h4>
                                    @if($section->description)
                                        <p class="text-sm text-gray-600">{{ $section->description }}</p>
                                    @endif
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ $section->materials->count() }} materi
                                </span>
                            </div>
                            <div class="flex items-center gap-2 ml-4" onclick="event.stopPropagation()">
                                <button onclick="editSection({{ $section->id }})" 
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteSection({{ $section->id }})" 
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Section Content (Materials) -->
                        <div class="section-content hidden" id="section-{{ $section->id }}">
                            <div class="bg-gray-50 p-4">
                                <!-- Section Pembelajaran Header -->
                                <div class="flex items-center justify-between mb-3">
                                    <h5 class="text-sm font-medium text-gray-700">Section Pembelajaran</h5>
                                    <div class="flex gap-2">
                                        <button onclick="openMaterialModal({{ $section->id }}, 'video')" 
                                                class="px-3 py-1.5 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition flex items-center gap-1">
                                            <i class="fas fa-video"></i> Upload Video
                                        </button>
                                        <button onclick="openMaterialModal({{ $section->id }}, 'pdf')" 
                                                class="px-3 py-1.5 bg-red-600 text-white rounded text-xs hover:bg-red-700 transition flex items-center gap-1">
                                            <i class="fas fa-file-pdf"></i> Upload PDF
                                        </button>
                                        <button onclick="openMaterialModal({{ $section->id }}, 'text')" 
                                                class="px-3 py-1.5 bg-green-600 text-white rounded text-xs hover:bg-green-700 transition flex items-center gap-1">
                                            <i class="fas fa-align-left"></i> Buat Teks
                                        </button>
                                        <button onclick="openQuizModal({{ $section->id }})" 
                                                class="px-3 py-1.5 bg-yellow-600 text-white rounded text-xs hover:bg-yellow-700 transition flex items-center gap-1">
                                            <i class="fas fa-question-circle"></i> Buat Quiz
                                        </button>
                                    </div>
                                </div>

                                <!-- Materials List -->
                                @if($section->materials->isEmpty())
                                    <div class="bg-white rounded-lg border border-gray-200 p-6 text-center">
                                        <i class="fas fa-inbox text-gray-300 text-3xl mb-2"></i>
                                        <p class="text-gray-500 text-sm">Belum ada materi di section ini</p>
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        @foreach($section->materials as $material)
                                            <div class="bg-white rounded-lg border border-gray-200 p-3 hover:border-blue-300 transition group">
                                                <div class="flex items-center gap-3">
                                                    <!-- Icon based on type -->
                                                    <div class="w-8 h-8 rounded flex items-center justify-center flex-shrink-0
                                                        {{ $material->type === 'video' ? 'bg-blue-100' : '' }}
                                                        {{ $material->type === 'pdf' ? 'bg-red-100' : '' }}
                                                        {{ $material->type === 'quiz' ? 'bg-yellow-100' : '' }}
                                                        {{ $material->type === 'text' ? 'bg-green-100' : '' }}">
                                                        @if($material->type === 'video')
                                                            <i class="fas fa-play text-blue-600"></i>
                                                        @elseif($material->type === 'pdf')
                                                            <i class="fas fa-file-pdf text-red-600"></i>
                                                        @elseif($material->type === 'quiz')
                                                            <i class="fas fa-question-circle text-yellow-600"></i>
                                                        @else
                                                            <i class="fas fa-align-left text-green-600"></i>
                                                        @endif
                                                    </div>

                                                    <!-- Content - Clickable Title -->
                                                    <div class="flex-1 min-w-0">
                                                        <a href="{{ route('admin.courses.materials.preview', [$course, $material]) }}" 
                                                           class="block group/title">
                                                            <h6 class="font-medium text-gray-900 truncate group-hover/title:text-blue-600 transition">
                                                                {{ $material->judul }}
                                                            </h6>
                                                        </a>
                                                        <p class="text-xs text-gray-500">
                                                            {{ ucfirst($material->type) }}
                                                            @if($material->duration)
                                                                • {{ $material->duration }} menit
                                                            @endif
                                                        </p>
                                                    </div>

                                                    <!-- Actions -->
                                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                        @if($material->type === 'quiz' && $material->assignment)
                                                        <a href="{{ route('admin.assignments.edit-questions', $material->assignment) }}"
                                                           class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded transition"
                                                           title="Kelola Soal">
                                                            <i class="fas fa-list text-sm"></i>
                                                        </a>
                                                        @endif
                                                        <a href="{{ route('admin.courses.materials.preview', [$course, $material]) }}" 
                                                           class="p-1.5 text-green-600 hover:bg-green-50 rounded transition"
                                                           title="Preview">
                                                            <i class="fas fa-eye text-sm"></i>
                                                        </a>
                                                        <a href="{{ $isAdmin ? route('admin.courses.materials.edit', [$course, $material]) : '#' }}" 
                                                           class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition"
                                                           title="Edit">
                                                            <i class="fas fa-edit text-sm"></i>
                                                        </a>
                                                        <form action="{{ $isAdmin ? route('admin.courses.materials.destroy', [$course, $material]) : route('instructor.materials.destroy', [$course, $material]) }}" 
                                                              method="POST" 
                                                              class="inline"
                                                              onsubmit="return confirm('Yakin ingin menghapus materi ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="p-1.5 text-red-600 hover:bg-red-50 rounded transition"
                                                                    title="Hapus">
                                                                <i class="fas fa-trash text-sm"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah/Edit Section -->
<div id="sectionModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-12 -left-8 w-28 h-28 bg-purple-100 rounded-full opacity-60"></div>
        <div class="absolute -bottom-12 -right-10 w-32 h-32 bg-indigo-100 rounded-full opacity-60"></div>
        <h3 class="relative z-10 text-xl font-bold text-gray-800 mb-4 flex items-center gap-2" id="sectionModalTitle">
            <i class="fas fa-folder-plus text-purple-600"></i>
            <span>Tambah Modul</span>
        </h3>
        <div class="relative z-10 mb-4">
            <div class="flex items-start gap-3 bg-purple-50 border border-purple-100 text-purple-800 rounded-xl px-3 py-2 text-sm">
                <i class="fas fa-lightbulb text-yellow-500 mt-0.5"></i>
                <div>Modul akan muncul sesuai urutan. Pastikan nama dan urutan unik.</div>
            </div>
        </div>
        <form id="sectionForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="sectionMethod" value="POST">
            
            <div class="mb-4 relative z-10">
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Modul *</label>
                <input type="text" name="title" id="sectionTitle" required
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 shadow-sm">
            </div>

            <div class="mb-4 relative z-10">
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" id="sectionDescription" rows="3"
                          class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 shadow-sm"></textarea>
            </div>

            <div class="mb-4 relative z-10">
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                <input type="number" name="order" id="sectionOrder" min="0"
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 shadow-sm">
            </div>

            <div class="flex gap-3 justify-end relative z-10">
                <button type="button" onclick="closeSectionModal()" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 shadow-md">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Materi -->
<!-- Modal Tambah Materi -->
<div id="materialModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto shadow-2xl relative">
        <div class="absolute -top-12 -left-10 w-32 h-32 bg-blue-100 rounded-full opacity-50"></div>
        <div class="absolute -bottom-12 -right-10 w-36 h-36 bg-teal-100 rounded-full opacity-50"></div>
        <div class="relative z-10 mb-4">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-file-upload text-blue-600"></i>
                <span>Tambah Materi</span>
            </h3>
        </div>
        <form action="{{ route('admin.courses.materials.store', [$course]) }}" method="POST" enctype="multipart/form-data" id="materialForm">
            @csrf
            <input type="hidden" name="section_id" id="materialSectionId">
            <input type="hidden" name="type" id="materialType">

            <div class="mb-4 relative z-10">
                <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 text-blue-800 rounded-xl px-3 py-2 text-sm mb-3" id="materialAlert">
                    <i class="fas fa-info-circle mt-0.5"></i>
                    <div id="materialAlertText">Unggah video (.mp4/.mov) atau PDF sesuai tipe materi. Untuk teks, isi konten tanpa unggah file.</div>
                </div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Materi *</label>
                <input type="text" name="judul" required
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm">
            </div>

            <div class="mb-4 relative z-10">
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm"></textarea>
            </div>

            <div class="mb-4 relative z-10" id="fileUploadSection">
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
                <input type="file" name="file" accept=".pdf,.mp4,.avi,.mov"
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg shadow-sm">
            </div>

            <div class="mb-4 relative z-10" id="contentSection" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Konten</label>
                <textarea name="content" rows="6"
                          class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm"></textarea>
            </div>

            <div class="flex gap-3 justify-end relative z-10">
                <button type="button" onclick="closeMaterialModal()" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md">
                    Simpan Materi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Buat Quiz -->
<div id="quizModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 max-h-[90vh] overflow-y-auto shadow-2xl relative">
        <div class="absolute -top-10 -left-8 w-24 h-24 bg-yellow-100 rounded-full opacity-60"></div>
        <div class="absolute -bottom-12 -right-10 w-28 h-28 bg-orange-100 rounded-full opacity-50"></div>
        <div class="relative z-10 mb-4 flex items-center gap-2">
            <div class="w-12 h-12 rounded-2xl bg-yellow-100 text-yellow-600 flex items-center justify-center shadow">
                <i class="fas fa-question-circle text-xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-800 leading-tight">Buat Quiz</h3>
                <p class="text-sm text-gray-600">Hubungkan quiz ke modul dan isi detailnya.</p>
            </div>
        </div>
        <form action="{{ $isAdmin ? route('admin.courses.quizzes.store', $course) : route('instructor.courses.quizzes.store', $course) }}" method="POST">
            @csrf
            <input type="hidden" name="section_id" id="quizSectionId">

            <div class="mb-4 relative z-10">
                <div class="flex items-start gap-3 bg-yellow-50 border border-yellow-100 text-yellow-800 rounded-xl px-3 py-2 text-sm mb-3">
                    <i class="fas fa-lightbulb mt-0.5"></i>
                    <div>Pastikan bank soal/assignment tersedia. Kelola soal setelah quiz dibuat.</div>
                </div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Quiz *</label>
                <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-500 shadow-sm">
            </div>

            <div class="mb-4 relative z-10">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-500 shadow-sm"></textarea>
            </div>

            <div class="mb-4 relative z-10">
                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Soal (opsional)</label>
                <select name="question_bank_id" class="w-full px-4 py-2 border border-gray-200 rounded-lg shadow-sm">
                    <option value="">Tanpa bank soal (buat kosong dulu)</option>
                    @foreach($questionBanks as $bank)
                        <option value="{{ $bank->id }}">{{ $bank->title }} ({{ $bank->questions_count }} soal)</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Jika dipilih, semua soal di bank ini akan ditambahkan ke quiz.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4 relative z-10">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Passing Score</label>
                    <input type="number" name="passing_score" value="60" min="0" max="100" class="w-full px-4 py-2 border border-gray-200 rounded-lg shadow-sm">
                </div>
            </div>

            <label class="inline-flex items-center gap-2 mb-6 relative z-10">
                <input type="checkbox" name="randomize_questions" class="h-4 w-4 text-yellow-600">
                <span class="text-sm text-gray-700">Acak urutan soal</span>
            </label>

            <div class="flex gap-3 justify-end relative z-10">
                <button type="button" onclick="closeQuizModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 shadow-md">
                    Simpan Quiz
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleSection(sectionId) {
    const content = document.getElementById(`section-${sectionId}`);
    const chevron = document.getElementById(`chevron-${sectionId}`);
    
    content.classList.toggle('hidden');
    chevron.classList.toggle('rotate-90');
}

function toggleAllSections() {
    document.querySelectorAll('.section-content').forEach(content => {
        content.classList.toggle('hidden');
    });
    document.querySelectorAll('.section-chevron').forEach(chevron => {
        chevron.classList.toggle('rotate-90');
    });
}

function openSectionModal() {
    document.getElementById('sectionModal').classList.remove('hidden');
    document.getElementById('sectionModalTitle').textContent = 'Tambah Modul';
    document.getElementById('sectionForm').action = "{{ $isAdmin ? route('admin.courses.modules.store', $course) : route('instructor.courses.sections.store', $course) }}";
    document.getElementById('sectionMethod').value = 'POST';
    document.getElementById('sectionTitle').value = '';
    document.getElementById('sectionDescription').value = '';
    document.getElementById('sectionOrder').value = '';
}

function closeSectionModal() {
    document.getElementById('sectionModal').classList.add('hidden');
}

// Data modul untuk edit
@php
    $sectionsJson = $sections->map(function ($s) {
        return [
            'id' => $s->id,
            'title' => $s->title,
            'description' => $s->description,
            'order' => $s->order,
        ];
    })->values()->toJson();
@endphp
const sectionsData = {!! $sectionsJson !!};

function editSection(id) {
    const data = sectionsData.find(s => s.id === id);
    if (!data) {
        alert('Data modul tidak ditemukan.');
        return;
    }
    document.getElementById('sectionModal').classList.remove('hidden');
    document.getElementById('sectionModalTitle').textContent = 'Edit Modul';
    document.getElementById('sectionForm').action = "{{ $isAdmin ? route('admin.courses.modules.update', [$course, '__ID__']) : route('instructor.sections.update', '__ID__') }}".replace('__ID__', id);
    document.getElementById('sectionMethod').value = 'PUT';
    document.getElementById('sectionTitle').value = data.title || '';
    document.getElementById('sectionDescription').value = data.description || '';
    document.getElementById('sectionOrder').value = data.order ?? '';
}

function scrollToFirstModuleAndAddMaterial() {
    // Cari modul pertama
    const firstSection = document.querySelector('.section-content');
    
    if (!firstSection) {
        alert('Belum ada modul. Silakan buat modul terlebih dahulu!');
        openSectionModal();
        return;
    }
    
    // Scroll ke modul pertama dengan smooth animation
    firstSection.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center' 
    });
    
    // Tunggu scroll selesai, lalu buka expand dan highlight
    setTimeout(() => {
        // Expand modul jika collapsed
        if (firstSection.classList.contains('hidden')) {
            const sectionId = firstSection.id.replace('section-', '');
            toggleSection(sectionId);
        }
        
        // Highlight modul dengan animasi
        firstSection.style.transition = 'all 0.3s ease';
        firstSection.style.backgroundColor = '#fef3c7'; // Yellow highlight
        firstSection.style.transform = 'scale(1.02)';
        
        // Cari tombol add material di modul pertama
        const addButtons = firstSection.querySelectorAll('button[onclick*="openMaterialModal"]');
        if (addButtons.length > 0) {
            // Highlight tombol
            addButtons.forEach(btn => {
                btn.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.5)';
                btn.style.transform = 'scale(1.1)';
            });
            
            // Kembalikan highlight setelah 2 detik
            setTimeout(() => {
                firstSection.style.backgroundColor = '';
                firstSection.style.transform = '';
                addButtons.forEach(btn => {
                    btn.style.boxShadow = '';
                    btn.style.transform = '';
                });
            }, 2000);
        }
        
        // Show info tooltip
        const tooltip = document.createElement('div');
        tooltip.className = 'fixed bottom-8 left-1/2 transform -translate-x-1/2 bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-bounce';
        tooltip.innerHTML = '<i class="fas fa-arrow-down mr-2"></i>Klik tombol hijau untuk menambah materi';
        document.body.appendChild(tooltip);
        
        setTimeout(() => {
            tooltip.remove();
        }, 3000);
        
    }, 500);
}

function scrollToAddMaterial() {
    const firstSection = document.querySelector('.section-container');
    if (firstSection) {
        // Scroll ke section pertama
        firstSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Buka section pertama jika belum terbuka
        const firstSectionId = firstSection.getAttribute('data-section-id');
        if (firstSectionId) {
            const sectionContent = document.getElementById('section-' + firstSectionId);
            if (sectionContent && sectionContent.classList.contains('hidden')) {
                toggleSection(firstSectionId);
            }
        }
        
        // Highlight tombol tambah materi di section pertama
        setTimeout(() => {
            const addButtons = firstSection.querySelectorAll('button[onclick*="openMaterialModal"]');
            if (addButtons.length > 0) {
                addButtons[0].classList.add('ring-4', 'ring-blue-300');
                setTimeout(() => {
                    addButtons[0].classList.remove('ring-4', 'ring-blue-300');
                }, 2000);
            }
        }, 500);
    } else {
        alert('Silakan buat modul terlebih dahulu sebelum menambah materi.');
    }
}

function openMaterialModal(sectionId, type = 'video') {
    document.getElementById('materialModal').classList.remove('hidden');
    document.getElementById('materialSectionId').value = sectionId;
    document.getElementById('materialType').value = type;
    
    // Update form action dengan section ID yang benar
    const form = document.getElementById('materialForm');
    form.action = "{{ route('admin.courses.materials.store', [$course]) }}";
    document.getElementById('materialSectionId').value = sectionId;
    
    // Update alert berdasarkan tipe materi
    const alertText = document.getElementById('materialAlertText');
    const fileInput = document.querySelector('#fileUploadSection input[type="file"]');
    
    if (type === 'video') {
        alertText.textContent = 'Unggah file video dengan format .mp4 atau .mov. Maksimal ukuran file: 100 MB.';
        fileInput.accept = '.mp4,.mov,.avi';
        document.getElementById('fileUploadSection').style.display = 'block';
        document.getElementById('contentSection').style.display = 'none';
    } else if (type === 'pdf') {
        alertText.textContent = 'Unggah file PDF. Maksimal ukuran file: 100 MB.';
        fileInput.accept = '.pdf';
        document.getElementById('fileUploadSection').style.display = 'block';
        document.getElementById('contentSection').style.display = 'none';
    } else if (type === 'text') {
        alertText.textContent = 'Buat konten teks langsung di editor. Tidak perlu mengunggah file.';
        document.getElementById('fileUploadSection').style.display = 'none';
        document.getElementById('contentSection').style.display = 'block';
    } else {
        alertText.textContent = 'Unggah file sesuai dengan tipe materi yang dipilih. Maksimal ukuran: 100 MB.';
        fileInput.accept = '.pdf,.mp4,.avi,.mov';
        document.getElementById('fileUploadSection').style.display = 'block';
        document.getElementById('contentSection').style.display = 'none';
    }
}

function closeMaterialModal() {
    document.getElementById('materialModal').classList.add('hidden');
}

function openQuizModal(sectionId = null) {
    document.getElementById('quizModal').classList.remove('hidden');
    document.getElementById('quizSectionId').value = sectionId || '';
}

function closeQuizModal() {
    document.getElementById('quizModal').classList.add('hidden');
}

// Filter/sort participants
document.addEventListener('DOMContentLoaded', () => {
    const rows = Array.from(document.querySelectorAll('.ins-participant-row'));
    const searchInput = document.getElementById('insParticipantSearch');
    const filterSelect = document.getElementById('insParticipantFilter');
    const sortSelect = document.getElementById('insParticipantSort');

    const apply = () => {
        const term = (searchInput?.value || '').toLowerCase();
        const filter = filterSelect?.value || 'all';
        const sort = sortSelect?.value || 'progress_desc';

        let filtered = rows.filter(row => {
            const name = row.dataset.name || '';
            const email = row.dataset.email || '';
            const status = row.dataset.status || 'none';
            const matchTerm = name.includes(term) || email.includes(term);
            const matchStatus = filter === 'all' ? true : filter === status;
            return matchTerm && matchStatus;
        });

        filtered.sort((a, b) => {
            const pa = Number(a.dataset.progress || 0);
            const pb = Number(b.dataset.progress || 0);
            const sa = Number(a.dataset.score || -1);
            const sb = Number(b.dataset.score || -1);
            const la = Number(a.dataset.last || 0);
            const lb = Number(b.dataset.last || 0);
            switch (sort) {
                case 'progress_asc': return pa - pb;
                case 'score_desc': return sb - sa;
                case 'score_asc': return sa - sb;
                case 'last_submit_desc': return lb - la;
                case 'last_submit_asc': return la - lb;
                default: return pb - pa; // progress_desc
            }
        });

        const tbody = document.querySelector('#insParticipantTable tbody');
        if (tbody) {
            tbody.innerHTML = '';
            filtered.forEach(row => tbody.appendChild(row));
        }
    };

    [searchInput, filterSelect, sortSelect].forEach(el => {
        el?.addEventListener('input', apply);
        el?.addEventListener('change', apply);
    });

    apply();
});

function deleteSection(sectionId) {
    const submitDelete = () => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ $isAdmin ? route('admin.courses.modules.destroy', [$course, '__SECTION_ID__']) : route('instructor.sections.destroy', '__SECTION_ID__') }}".replace('__SECTION_ID__', sectionId);
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    };
    if (typeof window.showDeleteConfirm === 'function') {
        window.showDeleteConfirm('Yakin ingin menghapus modul ini? Semua materi di dalamnya akan ikut terhapus.', submitDelete);
    } else {
        // Fallback native confirm
        if (confirm('Yakin ingin menghapus modul ini? Semua materi di dalamnya akan ikut terhapus.')) submitDelete();
    }
}
</script>
@endpush
@endsection
