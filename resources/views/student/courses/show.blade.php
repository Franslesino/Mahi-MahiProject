{{-- resources/views/courses/show.blade.php --}}
@extends('layouts.app')

@php
    // Biar gampang baca di Blade
    $judul        = $course->judul ?? $course->title ?? '';
    $deskripsi    = $course->deskripsi ?? $course->description ?? '';
    $kategori     = $course->kategori ?? $course->category ?? 'General';
    $mode         = $course->mode ?? 'Online';
    $badge        = $course->badge ?? null;

    // Harga / diskon – ambil dari kolom yang ada
    $hargaAsli    = $course->harga ?? $course->price ?? 0;
    $hargaDiskon  = $course->discount_price && $course->discount_price > 0
                    ? $course->discount_price
                    : null;

    $hargaTampil  = $hargaDiskon ?? $hargaAsli;

    // Hindari bagi 0 saat hitung persen diskon
    $persenDiskon = ($hargaDiskon && $hargaAsli > 0)
        ? round((($hargaAsli - $hargaDiskon) / $hargaAsli) * 100)
        : 0;

    // Hitung count materi kalau belum ada
    $materialsCount = $course->materials_count
        ?? $course->materi_count
        ?? ($materials->count() ?? 0);
@endphp

@section('title', $judul . ' - Detail Kursus')

@section('content')

<div class="bg-gray-50 min-h-screen transition-all duration-300" id="main-content">
    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Purchase Deadline Alert -->
        @if($course->purchase_deadline_date && $course->purchase_deadline_date->isFuture() && !$isEnrolled)
            @php
                $hoursLeft = now()->diffInHours($course->purchase_deadline_date);
                $daysLeft = now()->diffInDays($course->purchase_deadline_date);
                $minutesLeft = now()->diffInMinutes($course->purchase_deadline_date);
            @endphp
            @if($hoursLeft <= 48)
                <div class="bg-gradient-to-r from-red-600 to-red-700 text-white rounded-2xl shadow-lg p-6 mb-6 animate-pulse">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-2xl"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold mb-2">⚠️ Segera Berakhir!</h3>
                            <p class="text-sm leading-relaxed mb-3">
                                Kursus ini hanya dapat dibeli hingga <strong>{{ $course->purchase_deadline_date->format('d M Y H:i') }}</strong>.
                                Waktu tersisa: <strong id="countdown-display">{{ $hoursLeft }}j lagi</strong>
                            </p>
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fas fa-info-circle"></i>
                                <span>Jangan sampai terlewat! Daftar sekarang sebelum terlambat.</span>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($daysLeft <= 7)
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-2xl shadow-lg p-6 mb-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-hourglass-half text-2xl"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold mb-2">Waktu Terbatas!</h3>
                            <p class="text-sm leading-relaxed">
                                Kursus ini dapat dibeli hingga <strong>{{ $course->purchase_deadline_date->format('d M Y H:i') }}</strong>.
                                Tersisa <strong>{{ $daysLeft }} hari lagi</strong> untuk mendaftar.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($daysLeft <= 30)
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-2xl shadow-lg p-5 mb-6">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-calendar-alt text-xl"></i>
                        <p class="text-sm">
                            Pendaftaran kursus ini akan ditutup pada <strong>{{ $course->purchase_deadline_date->format('d M Y') }}</strong>
                        </p>
                    </div>
                </div>
            @endif

            @push('scripts')
            <script>
                // Countdown timer untuk deadline < 48 jam
                @if($hoursLeft <= 48)
                const deadlineTime = {{ $minutesLeft * 60 * 1000 }}; // ms
                const deadlineDate = new Date(Date.now() + deadlineTime);
                
                function updateCountdown() {
                    const now = new Date();
                    const diff = deadlineDate - now;
                    
                    if (diff <= 0) {
                        document.getElementById('countdown-display').textContent = 'Pendaftaran telah ditutup';
                        return;
                    }
                    
                    const hours = Math.floor(diff / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                    
                    const display = hours > 0 
                        ? `${hours}j ${minutes}m ${seconds}s` 
                        : `${minutes}m ${seconds}s`;
                    
                    document.getElementById('countdown-display').textContent = display;
                }
                
                // Update setiap detik
                updateCountdown();
                setInterval(updateCountdown, 1000);
                @endif
            </script>
            @endpush
        @endif

        <!-- Course Card -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
            <!-- Course Image -->
            @if($course->image_url)
                <img src="{{ $course->image_url }}" alt="{{ $judul }}" 
                     class="w-full h-80 object-cover">
            @else
                <div class="w-full h-80 bg-gradient-to-br from-teal-400 via-blue-500 to-purple-600 flex items-center justify-center">
                    <i class="fas fa-code text-white text-8xl opacity-30"></i>
                </div>
            @endif

            <!-- Course Info -->
            <div class="p-8">
                <!-- Category & Badge -->
                <div class="flex items-center gap-2 mb-4">
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-md font-medium">
                        {{ $kategori }}
                    </span>
                    @if($badge)
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm rounded-md font-medium">
                            {{ $badge }}
                        </span>
                    @endif
                    <span class="px-3 py-1 bg-teal-100 text-teal-700 text-sm rounded-md font-medium">
                        {{ $mode }}
                    </span>
                </div>

                <!-- Title -->
                <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $judul }}</h1>
                
                <!-- Description -->
                <p class="text-gray-600 text-base leading-relaxed mb-6">{{ $deskripsi }}</p>

                <!-- Instructor -->
                @if($course->instructor)
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm font-bold">{{ substr($course->instructor->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $course->instructor->name }}</p>
                            <p class="text-xs text-gray-500">Instructur</p>
                        </div>
                    </div>
                @endif

                <!-- Skills Tags -->
                @if($course->learning)
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Keterampilan yang didapat</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode("\n", $course->learning) as $item)
                                @if(trim($item))
                                    <span class="px-4 py-2 bg-teal-600 text-white text-sm rounded-md font-medium">
                                        {{ trim($item) }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Stats -->
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-6 pb-6 border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-book"></i>
                        <span>{{ $materialsCount }} Module</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-users"></i>
                        <span>{{ $course->students_count ?? 0 }} Siswa</span>
                    </div>
                </div>

                <!-- What You'll Learn -->
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Apa yang akan kamu pelajari?</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Spesialisasi ini dibangun berdasarkan kesuksesan kursus {{ $judul }} untuk Semua Orang 
                        dan akan memperkenalkan konsep-konsep pemrograman fundamental termasuk struktur data, 
                        antarmuka program aplikasi jaringan, dan basis data, menggunakan bahasa pemrograman yang dipelajari.
                    </p>
                </div>

                <!-- Price & Action -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 p-6 bg-gray-50 rounded-xl">
                    <div>
                        @if($hargaDiskon)
                            <div class="flex items-center gap-3 mb-1">
                                <span class="text-3xl font-bold text-gray-900">
                                    Rp {{ number_format($hargaDiskon, 0, ',', '.') }}
                                </span>
                                <span class="text-lg text-gray-500 line-through">
                                    Rp {{ number_format($hargaAsli, 0, ',', '.') }}
                                </span>
                            </div>
                            @if($persenDiskon > 0)
                                <span class="inline-block px-2 py-1 bg-red-100 text-red-600 text-xs rounded font-semibold">
                                    Hemat {{ $persenDiskon }}%
                                </span>
                            @endif
                        @else
                            <span class="text-3xl font-bold text-gray-900">
                                Rp {{ number_format($hargaAsli, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>

                    <div class="flex gap-3">
                        @auth
                            @if($isEnrolled)
                                <a href="{{ route('student.course.learn', $course) }}" 
                                   class="px-8 py-3 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition shadow-sm">
                                    <i class="fas fa-play-circle mr-2"></i>
                                    Lanjutkan Belajar
                                </a>
                            @else
                                @if($course->isFull())
                                    <button disabled 
                                            class="px-8 py-3 bg-gray-400 text-white rounded-lg font-semibold cursor-not-allowed shadow-sm">
                                        Kuota Penuh
                                    </button>
                                @else
                                    <form action="{{ route('courses.enroll', $course) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="px-8 py-3 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition shadow-sm">
                                            Bayar
                                        </button>
                                    </form>
                                @endif
                            @endif
                        @else
                            <a href="{{ route('login') }}" 
                               class="px-8 py-3 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition shadow-sm">
                                Login untuk Membeli
                            </a>
                        @endauth

                       
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Content -->
        @if(!$materials->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm p-8 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Konten Kursus</h2>
                    <span class="text-sm text-gray-600">
                        {{ $materialsCount }} Module
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach($materials as $index => $material)
                        <div class="border border-gray-200 rounded-xl hover:border-teal-300 transition overflow-hidden">
                            <div class="p-5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4 flex-1">
                                        <!-- Icon -->
                                        <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 {{ 
                                            $material->type === 'video' ? 'bg-red-50' : 
                                            ($material->type === 'pdf' ? 'bg-blue-50' : 
                                            ($material->type === 'quiz' ? 'bg-purple-50' : 'bg-gray-50'))
                                        }}">
                                            <i class="fas fa-{{ 
                                                $material->type === 'video' ? 'play-circle' : 
                                                ($material->type === 'pdf' ? 'file-pdf' : 
                                                ($material->type === 'quiz' ? 'question-circle' : 'align-left'))
                                            }} text-{{ 
                                                $material->type === 'video' ? 'red' : 
                                                ($material->type === 'pdf' ? 'blue' : 
                                                ($material->type === 'quiz' ? 'purple' : 'gray'))
                                            }}-500 text-xl"></i>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 class="font-semibold text-gray-900">{{ $material->title }}</h4>
                                                @if($material->is_preview)
                                                    <span class="px-2 py-1 bg-teal-50 text-teal-700 text-xs rounded font-medium">
                                                        Preview Gratis
                                                    </span>
                                                @endif
                                            </div>
                                            @if($material->description)
                                                <p class="text-sm text-gray-600 mb-2">{{ $material->description }}</p>
                                            @endif
                                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                                <span class="capitalize">
                                                    <i class="fas fa-tag mr-1"></i>{{ $material->type }}
                                                </span>
                                                @if($material->duration)
                                                    <span>
                                                        <i class="fas fa-clock mr-1"></i>{{ $material->duration }} menit
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action -->
                                    @if($material->is_preview || $isEnrolled)
                                        <button class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition text-sm font-medium">
                                            <i class="fas fa-play mr-2"></i>
                                            Lihat
                                        </button>
                                    @else
                                        <div class="flex items-center gap-2 text-gray-300">
                                            <i class="fas fa-lock text-lg"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Upcoming Schedules --}}
        @if($course->requiresSchedule())
            <div class="bg-white rounded-2xl shadow-sm p-8 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Jadwal Pertemuan</h3>
                        <p class="text-sm text-gray-600">Pertemuan mendatang untuk mode {{ ucfirst($course->mode ?? 'offline') }}</p>
                    </div>
                    @if($course->upcomingSchedules->count() === 0)
                        <span class="text-xs text-gray-500">Belum ada jadwal</span>
                    @else
                        <span class="text-xs text-gray-500">{{ $course->upcomingSchedules->count() }} jadwal</span>
                    @endif
                </div>

                @if($course->upcomingSchedules->count() === 0)
                    <div class="p-6 bg-gray-50 rounded-xl text-sm text-gray-600 flex items-center gap-3">
                        <i class="far fa-calendar text-gray-400"></i>
                        Jadwal pertemuan akan segera diumumkan.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($course->upcomingSchedules as $schedule)
                            <div class="p-4 rounded-xl border border-gray-100 hover:border-emerald-200 transition bg-gray-50/70">
                                <div class="flex flex-wrap gap-3 items-start justify-between">
                                    <div class="flex items-start gap-3">
                                        <div class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-700 flex flex-col items-center justify-center text-xs font-bold">
                                            <span>{{ $schedule->date->format('d') }}</span>
                                            <span class="text-[10px] uppercase tracking-wide">{{ $schedule->date->format('M') }}</span>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-semibold text-gray-900">{{ $schedule->title }}</h4>
                                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold border
                                                    @class([
                                                        'bg-blue-50 text-blue-700 border-blue-200' => $schedule->status === 'scheduled',
                                                        'bg-green-50 text-green-700 border-green-200' => $schedule->status === 'completed',
                                                        'bg-red-50 text-red-700 border-red-200' => $schedule->status === 'cancelled',
                                                    ])">
                                                    {{ ucfirst($schedule->status) }}
                                                </span>
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1 flex flex-wrap items-center gap-2">
                                                <span><i class="far fa-clock mr-1"></i>{{ $schedule->time_range }}</span>
                                                <span class="text-gray-400">•</span>
                                                <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $schedule->location ?? $course->default_location ?? 'Lokasi belum ditentukan' }}</span>
                                            </div>
                                            @if($schedule->description)
                                                <p class="text-sm text-gray-600 mt-2">{{ $schedule->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($schedule->meeting_url)
                                            <a href="{{ $schedule->meeting_url }}" target="_blank"
                                               class="px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                                <i class="fas fa-video mr-1"></i> Join Online
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <!-- Additional Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Course Features -->
            <div class="bg-white rounded-2xl shadow-sm p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Fitur Kursus</h3>
                <div class="space-y-4">
                    <!-- Mode Description -->
                    <div class="p-4 rounded-lg {{ $course->isOnline() ? 'bg-blue-50 text-blue-800' : ($course->isOffline() ? 'bg-orange-50 text-orange-800' : 'bg-purple-50 text-purple-800') }} mb-4">
                        <div class="font-semibold mb-1 flex items-center gap-2">
                            <i class="fas fa-{{ $course->isOnline() ? 'laptop' : ($course->isOffline() ? 'users' : 'random') }}"></i>
                            Mode {{ ucfirst($course->mode ?? 'Online') }}
                        </div>
                        <p class="text-sm opacity-90 leading-relaxed">{{ $course->getModeDescriptionText() }}</p>

                        <!-- Capacity for Offline/Hybrid -->
                        @if($course->hasCapacityLimit())
                            <div class="mt-3 pt-3 border-t border-black/10 flex justify-between items-center text-sm">
                                <span><i class="fas fa-chair mr-1"></i> Sisa Kuota:</span>
                                @php $slots = $course->available_slots; @endphp
                                <span class="font-bold {{ $slots > 0 ? 'text-emerald-700' : 'text-red-600' }}">
                                    {{ $slots > 0 ? $slots . ' Kursi' : 'PENUH' }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 text-gray-700">
                        <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg flex items-center justify-center shadow-sm">
                            <i class="fas fa-{{ $course->access_duration_days ? 'calendar-check' : 'infinity' }} text-white"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-semibold">{{ $course->access_duration_days ? 'Akses '.$course->access_duration_days.' Hari' : 'Akses Selamanya' }}</span>
                            @if($course->access_duration_days)
                                <span class="text-xs text-gray-500">Setelah pembelian</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-gray-700">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                            <i class="fas fa-mobile-alt text-white"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-semibold">Akses via Mobile</span>
                            <span class="text-xs text-gray-500">Belajar di mana saja</span>
                        </div>
                    </div>
                    @if($course->hasFinalQuiz())
                    <div class="flex items-center gap-3 text-gray-700">
                        <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center shadow-sm">
                            <i class="fas fa-certificate text-white"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-semibold">Sertifikat</span>
                            <span class="text-xs text-gray-500">Wajib Lulus Final Quiz</span>
                        </div>
                    </div>
                    @else
                    <div class="flex items-center gap-3 text-gray-700">
                        <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center shadow-sm">
                            <i class="fas fa-certificate text-white"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-semibold">Sertifikat</span>
                            <span class="text-xs text-gray-500">Setelah menyelesaikan materi</span>
                        </div>
                    </div>
                    @endif
                    <div class="flex items-center gap-3 text-gray-700">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center shadow-sm">
                            <i class="fas fa-download text-white"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-semibold">Materi Download</span>
                            <span class="text-xs text-gray-500">PDF, dokumen, dan lainnya</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructor Details -->
            @if($course->instructor)
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Tentang Instruktur</h3>
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-2xl font-bold">{{ substr($course->instructor->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 mb-1">{{ $course->instructor->name }}</h4>
                            <p class="text-sm text-gray-600 mb-3">{{ $course->instructor->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6 text-sm text-gray-600">
                        <span><i class="fas fa-book mr-2 text-teal-600"></i>{{ $instructorCourses }} Kursus</span>
                        <span><i class="fas fa-users mr-2 text-teal-600"></i>{{ $instructorStudents }} Siswa</span>
                    </div>
                </div>
            @endif

            {{-- Location Map (Offline/Hybrid) --}}
            @if($course->latitude && $course->longitude)
                <div class="bg-white rounded-2xl shadow-sm p-8 mb-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-1">Lokasi Pertemuan</h3>
                            <p class="text-sm text-gray-600">
                                {{ $course->default_location ?? 'Lokasi kursus' }}
                                @if($course->isOffline() || $course->isHybrid())
                                    • Mode {{ ucfirst($course->mode ?? 'offline') }}
                                @endif
                            </p>
                        </div>
                        <span class="text-xs text-gray-500">Klik pin untuk detail</span>
                    </div>
                    <div
                        id="course-location-map"
                        class="w-full h-64 rounded-xl border border-gray-200 mt-4"
                        data-lat="{{ $course->latitude }}"
                        data-lng="{{ $course->longitude }}"
                        data-label="{{ $course->default_location ?? 'Lokasi kursus' }}">
                    </div>
                </div>
            @elseif($course->default_location)
                <div class="bg-white rounded-2xl shadow-sm p-8 mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Lokasi Pertemuan</h3>
                    <p class="text-sm text-gray-700">{{ $course->default_location }}</p>
                </div>
            @endif

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

@push('scripts')
<script>
    // Detect sidebar state and shift content
    document.addEventListener('DOMContentLoaded', function() {
        const mainContent = document.getElementById('main-content');
        const sidebarToggle = document.querySelector('[data-drawer-toggle]');
        const sidebar = document.getElementById('drawer-navigation');
        
        if (sidebar && mainContent) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        const isOpen = !sidebar.classList.contains('-translate-x-full');
                        if (isOpen) {
                            mainContent.style.marginLeft = '256px'; // sidebar width
                        } else {
                            mainContent.style.marginLeft = '0';
                        }
                    }
                });
            });
            
            observer.observe(sidebar, {
                attributes: true,
                attributeFilter: ['class']
            });
            
            // Check initial state
            const isOpen = !sidebar.classList.contains('-translate-x-full');
            if (isOpen) {
                mainContent.style.marginLeft = '256px';
            }
        }
    });
</script>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mapEl = document.getElementById('course-location-map');
        if (!mapEl) return;

        const lat = parseFloat(mapEl.dataset.lat);
        const lng = parseFloat(mapEl.dataset.lng);
        if (Number.isNaN(lat) || Number.isNaN(lng)) return;

        function loadLeafletAssets(callback) {
            const cssId = 'leaflet-css';
            if (!document.getElementById(cssId)) {
                const link = document.createElement('link');
                link.id = cssId;
                link.rel = 'stylesheet';
                link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(link);
            }

            const scriptId = 'leaflet-js';
            const existingScript = document.getElementById(scriptId);
            if (existingScript) {
                if (typeof L !== 'undefined') {
                    callback();
                } else {
                    existingScript.addEventListener('load', callback, { once: true });
                }
                return;
            }

            const script = document.createElement('script');
            script.id = scriptId;
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = callback;
            document.body.appendChild(script);
        }

        loadLeafletAssets(() => {
            const map = L.map(mapEl).setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            L.marker([lat, lng]).addTo(map).bindPopup(mapEl.dataset.label || 'Lokasi kursus');

            // Fix map rendering when inside flex layouts
            setTimeout(() => map.invalidateSize(), 200);
        });
    });
</script>
@endpush

@endsection
