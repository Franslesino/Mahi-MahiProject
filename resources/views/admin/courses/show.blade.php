@extends('layouts.admin')

@section('content')
<div class="px-8 pt-6 pb-8 space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.courses.index') }}" 
               class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Kursus</h1>
                <p class="text-gray-600 mt-1">Informasi lengkap kursus</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.detail', $course->id) }}"
               class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                <i class="fas fa-book-open mr-2"></i>
                Kelola Materi
            </a>
            <a href="{{ route('admin.courses.edit', $course) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-edit mr-2"></i>
                Edit Kursus
            </a>
        </div>
    </div>

    {{-- STATISTICS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Peserta</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_enrollments'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Materi</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_materials'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Quiz</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_assignments'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Peserta Aktif</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['active_students'] }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- LEFT COLUMN - COURSE INFO --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- COURSE DETAIL CARD --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                @if($course->image_url)
                    <img src="{{ $course->image_url }}" 
                         class="w-full h-64 object-cover"
                         alt="{{ $course->judul }}">
                @else
                    <div class="w-full h-64 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-book text-white text-6xl opacity-50"></i>
                    </div>
                @endif

                <div class="p-6 space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                @if($course->badge)
                                    <span class="px-2.5 py-0.5 bg-{{ $course->badge_color }}-100 text-{{ $course->badge_color }}-800 text-xs font-medium rounded-full">
                                        {{ $course->badge }}
                                    </span>
                                @endif
                                <span class="px-2.5 py-0.5 bg-gray-100 text-gray-700 text-xs rounded-full">
                                    {{ $course->kategori }}
                                </span>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $course->judul }}</h2>
                        </div>
                        
                        @if($course->status === 'active')
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">Active</span>
                        @elseif($course->status === 'draft')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm rounded-full">Draft</span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full">Inactive</span>
                        @endif
                    </div>

                    <p class="text-gray-700 leading-relaxed">{{ $course->deskripsi }}</p>

                    {{-- PRICE --}}
                    <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
                        <div>
                            <p class="text-sm text-gray-600">Harga Normal</p>
                            <p class="text-2xl font-bold text-gray-900">
                                Rp {{ number_format($course->harga, 0, ',', '.') }}
                            </p>
                        </div>
                        @if($course->discount_price > 0)
                            <div>
                                <p class="text-sm text-gray-600">Harga Diskon</p>
                                <p class="text-2xl font-bold text-green-600">
                                    Rp {{ number_format($course->discount_price, 0, ',', '.') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- LEARNING OUTCOMES --}}
                    @if($course->learning)
                        <div class="pt-4 border-t border-gray-200">
                            <h3 class="font-semibold text-gray-900 mb-2">Yang Akan Dipelajari:</h3>
                            <p class="text-gray-700">{{ $course->learning }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- MATERIALS LIST --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-book text-blue-600 mr-2"></i>
                        Materi Kursus ({{ $course->materi->count() }})
                    </h3>
                    <a href="{{ route('admin.courses.detail', $course->id) }}"
                       class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Kelola Materi →
                    </a>
                </div>

                <div class="p-6">
                    @forelse($course->materi as $index => $materi)
                        <div class="flex items-start gap-4 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div class="w-8 h-8 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center flex-shrink-0 font-semibold text-sm">
                                {{ $materi->urutan }}
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $materi->judul }}</h4>
                                @if($materi->isi)
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($materi->isi, 100) }}</p>
                                @endif
                                @if($materi->url_konten)
                                    <div class="flex items-center gap-2 mt-2">
                                        @if(Str::contains($materi->url_konten, ['youtube.com', 'youtu.be']))
                                            <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded">
                                                <i class="fab fa-youtube mr-1"></i>Video
                                            </span>
                                        @elseif(Str::endsWith($materi->url_konten, '.pdf'))
                                            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">
                                                <i class="fas fa-file-pdf mr-1"></i>PDF
                                            </span>
                                        @else
                                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded">
                                                <i class="fas fa-link mr-1"></i>Link
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            @if($materi->status_terkunci)
                                <i class="fas fa-lock text-gray-400"></i>
                            @else
                                <i class="fas fa-lock-open text-green-500"></i>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-book-open text-4xl mb-3"></i>
                            <p>Belum ada materi</p>
                            <a href="{{ route('admin.courses.materials.create', $course) }}"
                               class="text-blue-600 hover:text-blue-700 text-sm mt-2 inline-block">
                                Tambah Materi Pertama →
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ASSIGNMENTS LIST --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-clipboard-list text-purple-600 mr-2"></i>
                        Quiz ({{ $assignments->count() }})
                    </h3>
                    <a href="{{ route('admin.courses.detail', $course->id) }}"
                       class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                        Tambah Quiz →
                    </a>
                </div>

                <div class="p-6">
                    @forelse($assignments as $assignment)
                        <div class="flex items-start gap-4 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                @if($assignment->type === 'quiz')
                                    <i class="fas fa-question-circle text-purple-600 text-xl"></i>
                                @elseif($assignment->type === 'exam')
                                    <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                                @else
                                    <i class="fas fa-clipboard-list text-purple-600 text-xl"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-semibold text-gray-900">{{ $assignment->title }}</h4>
                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded-full">
                                        {{ ucfirst($assignment->type) }}
                                    </span>
                                    @if($assignment->is_published)
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">
                                            Published
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded-full">
                                            Draft
                                        </span>
                                    @endif
                                </div>
                                @if($assignment->description)
                                    <p class="text-sm text-gray-600 mb-2">{{ Str::limit($assignment->description, 100) }}</p>
                                @endif
                                <div class="flex items-center gap-4 text-sm text-gray-600">
                                    <span>
                                        <i class="fas fa-question-circle mr-1"></i>
                                        {{ $assignment->questions_count }} Soal
                                    </span>
                                    <span>
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $assignment->duration }} menit
                                    </span>
                                    @if($assignment->passing_score)
                                        <span>
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Pass: {{ $assignment->passing_score }}%
                                        </span>
                                    @endif
                                    @if($assignment->deadline)
                                        <span>
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-clipboard-list text-4xl mb-3"></i>
                            <p>Belum ada quiz</p>
                            <a href="{{ route('admin.courses.detail', $course->id) }}"
                               class="text-purple-600 hover:text-purple-700 text-sm mt-2 inline-block">
                                Tambah Quiz Pertama →
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN - INSTRUCTOR & STUDENTS --}}
        <div class="space-y-6">
            
            {{-- INSTRUCTOR INFO --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-chalkboard-teacher text-emerald-600 mr-2"></i>
                    Instruktur
                </h3>
                
                @if($course->instructor)
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold">{{ strtoupper(substr($course->instructor->name, 0, 2)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900">{{ $course->instructor->name }}</p>
                            <p class="text-sm text-gray-600 truncate">{{ $course->instructor->email }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Belum ada instruktur</p>
                @endif
            </div>

            {{-- COURSE INFO --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Informasi Kursus
                </h3>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Mode</span>
                        <span class="font-medium text-gray-900">{{ $course->mode }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Status</span>
                        <span class="font-medium text-gray-900">
                            @if($course->status === 'active')
                                <span class="text-green-600">Active</span>
                            @elseif($course->status === 'draft')
                                <span class="text-yellow-600">Draft</span>
                            @else
                                <span class="text-gray-600">Inactive</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Dibuat</span>
                        <span class="font-medium text-gray-900">{{ $course->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-600">Diupdate</span>
                        <span class="font-medium text-gray-900">{{ $course->updated_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- ENROLLED STUDENTS --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-users text-blue-600 mr-2"></i>
                        Progress & Nilai Peserta ({{ $participantProgress->count() }})
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2 py-1 bg-blue-50 text-blue-700 rounded-full font-semibold">
                            {{ $stats['total_materials'] }} materi • {{ $stats['total_assignments'] }} tugas/quiz
                        </span>
                        <span class="text-xs px-2 py-1 bg-emerald-50 text-emerald-700 rounded-full font-semibold">
                            Lulus jika ≥ {{ $passingScore ?? 60 }}%
                        </span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mb-4">
                    <input id="participantSearch" type="text" placeholder="Cari peserta..." class="w-full sm:w-1/3 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <select id="participantFilter" class="w-full sm:w-40 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">Semua status</option>
                        <option value="passed">Lulus</option>
                        <option value="failed">Tidak lulus</option>
                        <option value="none">Belum ada nilai</option>
                    </select>
                    <select id="participantSort" class="w-full sm:w-48 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="progress_desc">Progress tertinggi</option>
                        <option value="progress_asc">Progress terendah</option>
                        <option value="score_desc">Nilai tertinggi</option>
                        <option value="score_asc">Nilai terendah</option>
                        <option value="last_submit_desc">Terakhir submit terbaru</option>
                        <option value="last_submit_asc">Terakhir submit terlama</option>
                    </select>
                </div>

                @if($participantProgress->isEmpty())
                    <div class="text-center py-6 text-gray-500">
                        <i class="fas fa-user-slash text-3xl mb-2"></i>
                        <p class="text-sm">Belum ada peserta</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full" id="participantTable">
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
                                    <tr class="hover:bg-gray-50 participant-row"
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

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const rows = Array.from(document.querySelectorAll('.participant-row'));
    const searchInput = document.getElementById('participantSearch');
    const filterSelect = document.getElementById('participantFilter');
    const sortSelect = document.getElementById('participantSort');

    const apply = () => {
        const term = (searchInput?.value || '').toLowerCase();
        const filter = filterSelect?.value || 'all';
        const sort = sortSelect?.value || 'progress_desc';

        let filtered = rows.filter(row => {
            const name = row.dataset.name || '';
            const email = row.dataset.email || '';
            const status = row.dataset.status || 'none';
            const matchTerm = name.includes(term) || email.includes(term);
            const matchStatus = filter === 'all'
                ? true
                : filter === status;
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

        const tbody = document.querySelector('#participantTable tbody');
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
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const rows = Array.from(document.querySelectorAll('.participant-row'));
    const searchInput = document.getElementById('participantSearch');
    const filterSelect = document.getElementById('participantFilter');
    const sortSelect = document.getElementById('participantSort');

    const apply = () => {
        const term = (searchInput?.value || '').toLowerCase();
        const filter = filterSelect?.value || 'all';
        const sort = sortSelect?.value || 'progress_desc';

        let filtered = rows.filter(row => {
            const name = row.dataset.name || '';
            const email = row.dataset.email || '';
            const status = row.dataset.status || 'none';
            const matchTerm = name.includes(term) || email.includes(term);
            const matchStatus = filter === 'all'
                ? true
                : filter === status;
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

        const tbody = document.querySelector('#participantTable tbody');
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
</script>
@endpush
