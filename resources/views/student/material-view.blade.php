@extends('layouts.app')

@section('title', $material->judul ?? $material->title)

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap');

        :root {
            --primary: #0d9488;
            --primary-dark: #0f766e;
            --primary-light: #14b8a6;
            --success: #10b981;
            --success-dark: #059669;
            --info: #06b6d4;
            --neutral-50: #fafafa;
            --neutral-100: #f5f5f5;
            --neutral-200: #e5e5e5;
            --neutral-600: #525252;
            --neutral-700: #404040;
            --neutral-800: #262626;
            --neutral-900: #171717;
            --teal-50: #f0fdfa;
            --teal-100: #ccfbf1;
        }

        .material-view-page {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #f0fdfa 0%, #e0f2fe 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .material-container {
            max-width: 80rem;
            margin: 0 auto;
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: white;
            color: var(--neutral-700);
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(13, 148, 136, 0.1);
            text-decoration: none;
            border: 2px solid var(--teal-100);
        }

        .back-link:hover {
            color: var(--primary);
            border-color: var(--primary);
            transform: translateX(-3px);
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.2);
        }

        .completed-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #d1fae5;
            color: var(--success-dark);
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            border: 2px solid var(--success);
        }

        .content-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 4px 16px rgba(13, 148, 136, 0.1);
            padding: 2.5rem;
            border: 2px solid var(--teal-100);
        }

        .material-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .material-info {
            flex: 1;
            min-width: 0;
        }

        .material-type-badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            background: var(--teal-100);
            color: var(--primary-dark);
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
        }

        .material-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--neutral-900);
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .material-description {
            font-size: 0.9375rem;
            color: var(--neutral-600);
            line-height: 1.6;
        }

        .action-buttons {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-secondary {
            background: var(--neutral-100);
            color: var(--neutral-700);
            border: 2px solid var(--neutral-200);
        }

        .btn-secondary:hover {
            background: var(--neutral-200);
            transform: translateY(-1px);
        }

        .btn-info {
            background: linear-gradient(135deg, var(--info) 0%, #0891b2 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
        }

        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(6, 182, 212, 0.4);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(13, 148, 136, 0.4);
        }

        .btn-success {
            background: #d1fae5;
            color: var(--success-dark);
            border: 2px solid var(--success);
        }

        .viewer-container {
            border-radius: 16px;
            border: 2px solid var(--teal-100);
            background: #000;
            overflow: hidden;
            min-height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .viewer-container video,
        .viewer-container iframe {
            width: 100%;
            height: 100%;
            min-height: 500px;
        }

        .viewer-placeholder {
            color: rgba(255, 255, 255, 0.6);
            font-size: 1rem;
        }

        .content-viewer {
            padding: 2rem;
            background: white;
            color: var(--neutral-800);
            line-height: 1.8;
            min-height: 400px;
        }

        .quiz-prompt {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, var(--teal-50) 0%, white 100%);
        }

        .quiz-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            box-shadow: 0 8px 20px rgba(13, 148, 136, 0.3);
        }

        .quiz-text {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--neutral-800);
        }

        @media (max-width: 768px) {
            .material-view-page {
                padding: 1rem 0.5rem;
            }

            .content-card {
                padding: 1.5rem;
            }

            .material-title {
                font-size: 1.5rem;
            }

            .material-header {
                flex-direction: column;
            }

            .action-buttons {
                width: 100%;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .content-card {
            animation: fadeIn 0.5s ease;
        }
    </style>

    <div class="material-view-page">
        <div class="material-container">
            <!-- Header Bar -->
            <div class="header-bar">
                <a href="{{ route('student.course.learn', $course) }}" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke daftar materi</span>
                </a>

                @if($completed)
                    <div class="completed-indicator">
                        <i class="fas fa-check-circle"></i>
                        <span>Selesai</span>
                    </div>
                @endif
            </div>

            <!-- Main Content Card -->
            <div class="content-card">
                <!-- Material Header -->
                <div class="material-header">
                    <div class="material-info">
                        <span class="material-type-badge">{{ ucfirst($material->type) }}</span>
                        <h1 class="material-title">{{ $material->judul ?? $material->title }}</h1>
                        @if($material->description)
                            <p class="material-description">{{ $material->description }}</p>
                        @endif
                    </div>

                    <div class="action-buttons">
                        @php
                            $downloadUrl = route('courses.materials.download', [$course, $material->id]);
                            $hasFile = in_array($material->type, ['video', 'pdf']) && ($material->file_url_full ?? $material->url_konten);
                        @endphp

                        @if($hasFile)
                            <button type="button" class="btn btn-secondary" onclick="toggleFullscreen()">
                                <i class="fas fa-expand"></i>
                                Fullscreen
                            </button>

                            <a href="{{ $downloadUrl }}" class="btn btn-info">
                                <i class="fas fa-download"></i>
                                Download
                            </a>
                        @endif

                        @if(!$completed)
                            <form action="{{ route('courses.materials.complete', [$course, $material->id]) }}" method="POST"
                                onsubmit="return handleMarkComplete(event)">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check"></i>
                                    Tandai Selesai
                                </button>
                            </form>
                        @else
                            <span class="btn btn-success">
                                <i class="fas fa-check-circle"></i>
                                Sudah Selesai
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Material Viewer -->
                <div id="materialViewer" class="viewer-container">
                    @if($material->type === 'video')
                        @php
                            $videoSrc = $material->video_url ?? $material->file_url_full ?? $material->url_konten;
                            $mimeType = 'video/mp4';
                            if ($videoSrc && \Illuminate\Support\Str::endsWith(strtolower($videoSrc), ['.mov'])) {
                                $mimeType = 'video/quicktime';
                            }
                        @endphp

                        @if($videoSrc)
                            <video controls playsinline style="width: 100%; height: auto; min-height: 500px;">
                                <source src="{{ $videoSrc }}" type="{{ $mimeType }}">
                                Browser Anda tidak mendukung pemutaran video.
                            </video>
                        @else
                            <p class="viewer-placeholder">Video belum tersedia</p>
                        @endif

                    @elseif($material->type === 'pdf')
                        @php $pdfSrc = $material->file_url_full ?? $material->url_konten; @endphp

                        @if($pdfSrc)
                            <iframe src="{{ $pdfSrc }}" frameborder="0" style="width: 100%; height: 100%; min-height: 600px;">
                            </iframe>
                        @else
                            <p class="viewer-placeholder">PDF belum tersedia</p>
                        @endif

                    @elseif($material->type === 'quiz')
                        <div class="quiz-prompt">
                            <div class="quiz-icon">
                                <i class="fas fa-clipboard-question"></i>
                            </div>
                            <p class="quiz-text">Siap untuk mengerjakan quiz?</p>
                            <a href="{{ route('courses.materials.quiz', [$course, $material->id]) }}" class="btn btn-primary"
                                style="padding: 1rem 2rem; font-size: 1rem;">
                                <i class="fas fa-play"></i>
                                Mulai Quiz
                            </a>
                        </div>

                    @elseif($material->type === 'class_session')
                        {{-- Class Session Display --}}
                        @php
                            $attendance = $material->attendances()->where('user_id', auth()->id())->first();
                            $attendanceStatus = $attendance ? $attendance->status : null;
                            $isHadir = $attendanceStatus === 'hadir';
                        @endphp
                        <div class="p-8 bg-gradient-to-br from-orange-50 to-amber-50" style="border-radius: 16px;">
                            {{-- Session Type Badge --}}
                            <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold {{ ($material->session_type ?? 'offline') === 'online' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                    <i class="fas {{ ($material->session_type ?? 'offline') === 'online' ? 'fa-video' : 'fa-building' }}"></i>
                                    Sesi {{ ucfirst($material->session_type ?? 'offline') }}
                                </span>
                                
                                {{-- Attendance Status Badge --}}
                                @if($attendanceStatus)
                                    <span class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-full
                                        {{ $attendanceStatus === 'hadir' ? 'bg-green-100 text-green-700 border-2 border-green-300' : '' }}
                                        {{ $attendanceStatus === 'tidak_hadir' ? 'bg-red-100 text-red-700 border-2 border-red-300' : '' }}
                                        {{ in_array($attendanceStatus, ['izin', 'sakit']) ? 'bg-yellow-100 text-yellow-700 border-2 border-yellow-300' : '' }}
                                        {{ $attendanceStatus === 'terlambat' ? 'bg-blue-100 text-blue-700 border-2 border-blue-300' : '' }}">
                                        @if($attendanceStatus === 'hadir')
                                            <i class="fas fa-check-circle"></i> Hadir
                                        @elseif($attendanceStatus === 'tidak_hadir')
                                            <i class="fas fa-times-circle"></i> Tidak Hadir
                                        @elseif($attendanceStatus === 'izin')
                                            <i class="fas fa-envelope"></i> Izin
                                        @elseif($attendanceStatus === 'sakit')
                                            <i class="fas fa-medkit"></i> Sakit
                                        @elseif($attendanceStatus === 'terlambat')
                                            <i class="fas fa-clock"></i> Terlambat
                                        @endif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-full">
                                        <i class="fas fa-hourglass-half"></i> Absensi Belum Tercatat
                                    </span>
                                @endif
                            </div>

                            {{-- Session Details Grid --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                {{-- Date & Time --}}
                                <div class="bg-white rounded-xl p-5 shadow-sm border border-orange-100">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                                            <i class="fas fa-calendar-alt text-orange-600 text-xl"></i>
                                        </div>
                                        <h3 class="font-semibold text-gray-800">Jadwal Kelas</h3>
                                    </div>
                                    @if($material->session_date)
                                        <p class="text-lg font-bold text-gray-900 mb-1">
                                            {{ $material->session_date->translatedFormat('l, d F Y') }}
                                        </p>
                                    @else
                                        <p class="text-gray-500 italic">Tanggal belum ditentukan</p>
                                    @endif
                                    @if($material->session_start_time && $material->session_end_time)
                                        <p class="text-gray-600 flex items-center gap-2">
                                            <i class="fas fa-clock text-orange-500"></i>
                                            {{ \Carbon\Carbon::parse($material->session_start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($material->session_end_time)->format('H:i') }} WIB
                                        </p>
                                    @endif
                                </div>

                                {{-- Location --}}
                                <div class="bg-white rounded-xl p-5 shadow-sm border border-orange-100">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                                            <i class="fas fa-map-marker-alt text-orange-600 text-xl"></i>
                                        </div>
                                        <h3 class="font-semibold text-gray-800">Lokasi</h3>
                                    </div>
                                    @if($material->session_location)
                                        <p class="text-lg font-bold text-gray-900">{{ $material->session_location }}</p>
                                    @else
                                        <p class="text-gray-500 italic">Lokasi belum ditentukan</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Meeting Link (if online) --}}
                            @if($material->session_meeting_link && ($material->session_type ?? 'offline') === 'online')
                                <div class="bg-white rounded-xl p-5 shadow-sm border border-blue-100 mb-6">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-video text-blue-600 text-xl"></i>
                                        </div>
                                        <h3 class="font-semibold text-gray-800">Link Meeting</h3>
                                    </div>
                                    <a href="{{ $material->session_meeting_link }}" target="_blank" 
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        <i class="fas fa-external-link-alt"></i>
                                        Buka Link Meeting
                                    </a>
                                    <p class="text-xs text-gray-500 mt-2">{{ $material->session_meeting_link }}</p>
                                </div>
                            @endif

                            {{-- Progress Info --}}
                            @if($isHadir)
                                <div class="bg-green-50 border-2 border-green-200 rounded-xl p-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-green-800">Materi Selesai</h3>
                                            <p class="text-sm text-green-600">Anda telah hadir pada sesi ini. Progress kursus bertambah.</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="fas fa-info-circle text-gray-500 text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-700">Menunggu Absensi</h3>
                                            <p class="text-sm text-gray-500">Instruktur akan mencatat kehadiran Anda setelah sesi berlangsung.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Session Description --}}
                            @if($material->isi || $material->content)
                                <div class="mt-6 bg-white rounded-xl p-5 shadow-sm border border-orange-100">
                                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                        <i class="fas fa-info-circle text-orange-500"></i>
                                        Deskripsi Sesi
                                    </h3>
                                    <div class="prose prose-sm max-w-none text-gray-600">
                                        {!! nl2br(e($material->isi ?? $material->content)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>

                    @else
                        <div class="content-viewer">
                            @php
                                $textContent = $material->content ?? $material->description ?? 'Konten belum tersedia';
                                $escaped = e($textContent);
                                $pattern = '/(https?:\/\/[^\s<]+[^\s<.,:;"\'\)\]\s])/i';
                                $linkedContent = preg_replace_callback($pattern, function ($matches) {
                                    $url = $matches[0];
                                    return '<a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="inline-link">' . $url . ' <i class="fas fa-external-link-alt" style="font-size: 0.75rem;"></i></a>';
                                }, $escaped);
                                $finalContent = nl2br($linkedContent);
                                preg_match_all($pattern, $escaped, $urlMatches);
                                $uniqueUrls = array_unique($urlMatches[0] ?? []);
                            @endphp

                            <div class="text-content-display">{!! $finalContent !!}</div>

                            @if(count($uniqueUrls) > 0)
                                <div style="margin-top: 1.5rem;">
                                    <p style="font-weight: 600; color: var(--neutral-700); margin-bottom: 0.75rem;">
                                        <i class="fas fa-link" style="margin-right: 0.5rem;"></i>Link dalam materi ini:
                                    </p>
                                    @foreach($uniqueUrls as $url)
                                        @php
                                            $parsedUrl = parse_url($url);
                                            $domain = $parsedUrl['host'] ?? $url;
                                            $iconClass = 'fas fa-globe';
                                            if (str_contains($domain, 'youtube.com') || str_contains($domain, 'youtu.be'))
                                                $iconClass = 'fab fa-youtube';
                                            elseif (str_contains($domain, 'github.com'))
                                                $iconClass = 'fab fa-github';
                                            elseif (str_contains($domain, 'drive.google.com'))
                                                $iconClass = 'fab fa-google-drive';
                                            elseif (str_contains($domain, 'google.com'))
                                                $iconClass = 'fab fa-google';
                                            elseif (str_contains($domain, 'facebook.com'))
                                                $iconClass = 'fab fa-facebook';
                                            elseif (str_contains($domain, 'instagram.com'))
                                                $iconClass = 'fab fa-instagram';
                                            elseif (str_contains($domain, 'twitter.com') || str_contains($domain, 'x.com'))
                                                $iconClass = 'fab fa-twitter';
                                            elseif (str_contains($domain, 'linkedin.com'))
                                                $iconClass = 'fab fa-linkedin';
                                        @endphp
                                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="link-preview-card">
                                            <div class="link-icon"><i class="{{ $iconClass }}"></i></div>
                                            <div class="link-info">
                                                <div class="link-title">{{ $domain }}</div>
                                                <div class="link-url">{{ Str::limit($url, 60) }}</div>
                                            </div>
                                            <i class="fas fa-external-link-alt link-external-icon"></i>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <style>
                            .link-preview-card {
                                display: flex;
                                align-items: center;
                                gap: 1rem;
                                padding: 1rem;
                                margin: 0.5rem 0; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #e2e8f0; border-left: 4px solid var(--primary); border-radius: 12px; transition: all 0.2s ease; text-decoration: none; }
                                    .link-preview-card:hover { background: linear-gradient(135deg, #f0fdfa 0%, #e0f7fa 100%); border-color: var(--primary); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(13, 148, 136, 0.15); }
                                    .link-icon { width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
                                    .link-icon i { color: white; font-size: 1.25rem; }
                                    .link-info { flex: 1; min-width: 0; }
                                    .link-title { font-weight: 600; color: var(--neutral-800); font-size: 0.9375rem; margin-bottom: 0.25rem; }
                                    .link-url { font-size: 0.8125rem; color: var(--primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                                    .link-external-icon { color: var(--neutral-600); font-size: 1rem; }
                                    .inline-link { color: var(--primary); text-decoration: underline; font-weight: 500; }
                                    .inline-link:hover { color: var(--primary-dark); }
                                </style>
                    @endif
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Fullscreen toggle
            function toggleFullscreen() {
                const viewer = document.getElementById('materialViewer');
                if (!viewer) return;

                if (!document.fullscreenElement) {
                    if (viewer.requestFullscreen) {
                        viewer.requestFullscreen();
                    } else if (viewer.webkitRequestFullscreen) {
                        viewer.webkitRequestFullscreen();
                    } else if (viewer.msRequestFullscreen) {
                        viewer.msRequestFullscreen();
                    }
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    } else if (document.msExitFullscreen) {
                        document.msExitFullscreen();
                    }
                }
            }

            // Handle mark complete with feedback
            function handleMarkComplete(event) {
                const button = event.target.querySelector('button[type="submit"]');
                if (button) {
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                }
                return true;
            }

            // Auto-mark video as complete when watched (optional feature)
            document.addEventListener('DOMContentLoaded', () => {
                const video = document.querySelector('video');
                if (video) {
                    let watched = false;
                    video.addEventListener('timeupdate', () => {
                        const percentage = (video.currentTime / video.duration) * 100;
                        if (percentage > 90 && !watched) {
                            watched = true;
                            // Optional: Auto-suggest marking as complete
                            console.log('Video almost complete - suggest marking as done');
                        }
                    });
                }
            });
        </script>
@endsection