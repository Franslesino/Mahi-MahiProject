@extends('layouts.app')

@section('title', 'Semua Kursus - UpGrennius')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Auto Carousel Promo Banner -->
        <div class="mb-8">
            <div class="relative overflow-hidden rounded-2xl">
                <!-- Carousel Container -->
                <div id="promoCarousel" class="flex transition-transform duration-500 ease-in-out">

                    @forelse($promoBanners as $banner)
                        <!-- Slide {{ $loop->iteration }} -->
                        <div
                            class="min-w-full bg-gradient-to-r from-{{ $banner->gradient_from }} to-{{ $banner->gradient_to }} p-8 text-white relative overflow-hidden">
                            <div class="absolute inset-0 bg-black/10 md:bg-black/15"></div>
                            <div class="relative z-10 max-w-md">
                                @if($banner->badge)
                                    <div class="inline-block bg-white/20 px-3 py-1 rounded-full text-sm font-semibold mb-3">
                                        {{ $banner->badge }}
                                    </div>
                                @endif
                                <h2 class="text-3xl font-bold mb-2">{{ $banner->title }}</h2>
                                <p class="text-white/90 mb-4 leading-relaxed">
                                    {!! nl2br(e($banner->description)) !!}
                                </p>
                                @if($banner->button_text)
                                    @if($banner->button_link)
                                        <a href="{{ $banner->button_link }}"
                                            class="inline-flex items-center gap-2 bg-white text-gray-900 px-6 py-3 rounded-xl font-semibold hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200 shadow-sm text-sm md:text-base">
                                            {{ $banner->button_text }}
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @else
                                        <button
                                            class="inline-flex items-center gap-2 bg-white text-gray-900 px-6 py-3 rounded-xl font-semibold hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200 shadow-sm text-sm md:text-base">
                                            {{ $banner->button_text }}
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    @endif
                                @endif
                            </div>
                            <div class="absolute right-0 top-0 w-40 h-40 bg-white/20 rounded-full -mr-20 -mt-10"></div>
                            <div class="absolute right-20 bottom-0 w-32 h-32 bg-white/10 rounded-full -mb-10"></div>
                        </div>
                    @empty
                        <!-- Default Slide if no banners -->
                        <div
                            class="min-w-full bg-gradient-to-r from-teal-600 to-teal-700 p-8 text-white relative overflow-hidden">
                            <div class="absolute inset-0 bg-black/10 md:bg-black/15"></div>
                            <div class="relative z-10 max-w-md">
                                <div class="inline-block bg-white/20 px-3 py-1 rounded-full text-sm font-semibold mb-3">
                                    SELAMAT DATANG
                                </div>
                                <h2 class="text-3xl font-bold mb-2">UpGrennius</h2>
                                <p class="text-teal-50 mb-4 leading-relaxed">
                                    Platform Pembelajaran Online<br>Terbaik untuk Pengembangan Skill
                                </p>
                                <a href="#courses"
                                    class="inline-flex items-center gap-2 bg-white text-teal-700 px-6 py-3 rounded-xl font-semibold hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200 shadow-sm text-sm md:text-base">
                                    JELAJAHI KURSUS
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                            <div class="absolute right-0 top-0 w-40 h-40 bg-teal-500/30 rounded-full -mr-20 -mt-10"></div>
                            <div class="absolute right-20 bottom-0 w-32 h-32 bg-teal-400/20 rounded-full -mb-10"></div>
                        </div>
                    @endforelse

                </div>

                <!-- Navigation Dots -->
                <div class="carousel-dots absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-20"></div>

                <!-- Navigation Arrows -->
                @if($promoBanners->count() > 1 || $promoBanners->count() == 0)
                    <button onclick="prevSlide()"
                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-white text-gray-900 shadow-md hover:shadow-xl p-2 rounded-full transition z-10 border border-white/60">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button onclick="nextSlide()"
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-white text-gray-900 shadow-md hover:shadow-xl p-2 rounded-full transition z-10 border border-white/60">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>

        <!-- Categories -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Kategori</h3>
                <a href="{{ route('home') }}"
                    class="text-pnj-blue font-semibold text-sm hover:underline flex items-center gap-1">
                    LIHAT SEMUA
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="flex gap-3 overflow-x-auto pb-2">
                @php
                    $currentCategory = request('category', 'Semua');
                    $categoryIcons = [
                        '3D Design' => 'fa-cube',
                        'Data Analytic' => 'fa-chart-line',
                        'Graphic Design' => 'fa-pen-nib',
                        'Web Development' => 'fa-code',
                        'AI / Machine Learning' => 'fa-brain',
                        'Database' => 'fa-database',
                        'Programming' => 'fa-terminal',
                    ];
                    $categoriesList = collect($categoryCounts ?? [])->keys()->prepend('Semua');
                @endphp

                @foreach($categoriesList as $cat)
                    @php
                        $icon = $categoryIcons[$cat] ?? 'fa-folder';
                        $count = $cat === 'Semua' ? ($courses->total() ?? 0) : ($categoryCounts[$cat] ?? 0);
                    @endphp
                    <a href="{{ route('home', array_merge(request()->only('search'), ['category' => $cat])) }}"
                        class="px-6 py-2 rounded-lg font-medium whitespace-nowrap transition shadow-sm flex items-center gap-2
                           {{ $currentCategory === $cat ? 'bg-pnj-blue text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas {{ $icon }}"></i>
                        <span>{{ $cat }}</span>
                        <span
                            class="text-xs px-2 py-0.5 rounded-full {{ $currentCategory === $cat ? 'bg-white/20' : 'bg-gray-100 text-gray-700' }}">
                            {{ $count }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Courses Skeleton -->
        <div id="courseSkeleton" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @for($i = 0; $i < 8; $i++)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="h-40 bg-gray-200 animate-pulse"></div>
                    <div class="p-4 space-y-3">
                        <div class="h-4 bg-gray-200 rounded animate-pulse w-1/2"></div>
                        <div class="h-3 bg-gray-200 rounded animate-pulse w-1/3"></div>
                        <div class="flex gap-2">
                            <div class="h-3 bg-gray-200 rounded animate-pulse w-16"></div>
                            <div class="h-3 bg-gray-200 rounded animate-pulse w-12"></div>
                        </div>
                        <div class="h-6 bg-gray-200 rounded animate-pulse w-24"></div>
                    </div>
                </div>
            @endfor
        </div>

        <!-- Courses Grid -->
        <div id="courseGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 hidden">
            @forelse($courses as $course)
                <a href="{{ route('courses.show', $course) }}"
                    class="bg-white rounded-xl shadow-sm hover:shadow-xl border border-gray-100 hover:-translate-y-1 transition overflow-hidden group cursor-pointer">

                    <!-- Image -->
                    <div class="relative h-40 bg-gray-900 overflow-hidden">
                        @if($course->image_url)
                            <img src="{{ $course->image_url }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                alt="{{ $course->judul }}">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white text-5xl opacity-50"></i>
                            </div>
                        @endif

                        <!-- Category -->
                        <div class="absolute top-3 left-3">
                            @php
                                $cat = $course->kategori ?? 'General';
                                $catColors = [
                                    'AI / Machine Learning' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                    'Data Analytic' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'Database' => 'bg-slate-100 text-slate-800 border-slate-200',
                                    'Web Development' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'Graphic Design' => 'bg-pink-100 text-pink-800 border-pink-200',
                                    '3D Design' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                ];
                                $badgeClass = $catColors[$cat] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold border shadow-sm {{ $badgeClass }}">
                                {{ $cat }}
                            </span>
                        </div>

                        <!-- Badge -->
                        @if($course->badge)
                            <div class="absolute top-3 right-3">
                                <span class="bg-yellow-500 text-white text-xs px-2.5 py-1 rounded-full font-semibold shadow-sm">
                                    {{ $course->badge }}
                                </span>
                            </div>
                        @endif

                        <!-- Purchase Deadline Badge -->
                        @if($course->purchase_deadline_date && $course->purchase_deadline_date->isFuture())
                            @php
                                $hoursLeft = now()->diffInHours($course->purchase_deadline_date);
                                $daysLeft = now()->diffInDays($course->purchase_deadline_date);
                            @endphp
                            @if($hoursLeft <= 48)
                                <div class="absolute bottom-3 left-3 right-3">
                                    <div
                                        class="bg-red-600 text-white text-xs px-2.5 py-1.5 rounded-lg font-semibold shadow-lg flex items-center gap-1.5 animate-pulse">
                                        <i class="fas fa-clock"></i>
                                        <span>Berakhir {{ $hoursLeft }}j lagi!</span>
                                    </div>
                                </div>
                            @elseif($daysLeft <= 7)
                                <div class="absolute bottom-3 left-3 right-3">
                                    <div
                                        class="bg-orange-500 text-white text-xs px-2.5 py-1.5 rounded-lg font-semibold shadow-lg flex items-center gap-1.5">
                                        <i class="fas fa-hourglass-half"></i>
                                        <span>{{ $daysLeft }} hari lagi</span>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Info Card -->
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-pnj-blue transition-colors">
                            {{ $course->judul }}</h4>

                        <div class="flex items-center gap-2 text-sm text-gray-600 mb-3">
                            <i class="fas fa-user-tie"></i>
                            <span>{{ $course->instructor->name ?? $course->pembuat->name ?? 'Instruktur' }}</span>
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                            <span><i class="fas fa-user-graduate mr-1"></i>{{ $course->students_count ?? 0 }} Siswa</span>
                            <span class="text-gray-300">|</span>
                            <span><i class="fas fa-video mr-1"></i>{{ $course->videos_count ?? 0 }} Video</span>
                            <span class="text-gray-300">|</span>
                            <span>{{ $course->materi_count ?? 0 }} Materi</span>
                        </div>

                        {{-- Purchase Deadline Info --}}
                        @if($course->purchase_deadline_date && $course->purchase_deadline_date->isFuture())
                            @php
                                $hoursLeft = now()->diffInHours($course->purchase_deadline_date);
                                $daysLeft = now()->diffInDays($course->purchase_deadline_date);
                            @endphp
                            <div
                                class="mb-3 p-2 rounded-lg {{ $hoursLeft <= 48 ? 'bg-red-50 border border-red-200' : ($daysLeft <= 7 ? 'bg-orange-50 border border-orange-200' : 'bg-blue-50 border border-blue-200') }}">
                                <div
                                    class="flex items-center gap-2 text-xs {{ $hoursLeft <= 48 ? 'text-red-700' : ($daysLeft <= 7 ? 'text-orange-700' : 'text-blue-700') }}">
                                    <i class="fas fa-clock"></i>
                                    <span class="font-semibold">
                                        @if($hoursLeft <= 48)
                                            Berakhir dalam {{ $hoursLeft }} jam
                                        @elseif($daysLeft <= 7)
                                            Berakhir dalam {{ $daysLeft }} hari
                                        @else
                                            Tersedia hingga {{ $course->purchase_deadline_date->format('d M Y') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-between">
                            @php
                                // fallback harga
                                $basePrice = $course->harga ?? $course->price ?? 0;

                                // final price (gunakan diskon kalau ada)
                                $finalPrice = ($course->discount_price ?? 0) > 0
                                    ? $course->discount_price
                                    : $basePrice;
                            @endphp

                            <div class="flex flex-col gap-1">
                                <span class="text-2xl font-bold text-pnj-blue">
                                    Rp {{ number_format($finalPrice, 0, ',', '.') }}
                                </span>

                                {{-- kalau lagi diskon, tampilkan harga asli dicoret --}}
                                @if(($course->discount_price ?? 0) > 0)
                                    <span class="text-sm text-gray-500 line-through">
                                        Rp {{ number_format($basePrice, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>


                        </div>

                    </div>

                </a>

            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-600 space-y-3">
                        <i class="fas fa-search text-3xl text-gray-400"></i>
                        <p class="font-semibold text-gray-800">Kursus tidak ditemukan</p>
                        <p class="text-sm text-gray-500">Coba gunakan kata kunci lain atau lihat semua kategori.</p>
                        <a href="{{ route('home') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-pnj-blue text-white rounded-lg font-semibold shadow hover:-translate-y-0.5 transition">
                            Reset Filter
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-10 mb-8">
            {{ $courses->links() }}
        </div>

        <!-- Why Choose Us Section -->
        <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-3xl p-12 mb-12 shadow-sm border border-gray-100">
            <div class="text-center mb-12">
                <h3 class="text-3xl font-bold text-gray-900 mb-3">Mengapa Memilih UpGrennius?</h3>
                <p class="text-gray-600 text-lg">Platform pembelajaran terbaik untuk mahasiswa PNJ</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div
                    class="text-center group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-xl transition">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform shadow-xl">
                        <i class="fas fa-certificate text-white text-3xl"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-2 text-lg">Sertifikat Resmi</h4>
                    <p class="text-gray-600 text-sm">Dapatkan sertifikat yang diakui industri setelah menyelesaikan kursus
                    </p>
                </div>

                <div
                    class="text-center group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-xl transition">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform shadow-xl">
                        <i class="fas fa-users text-white text-3xl"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-2 text-lg">Instruktur Ahli</h4>
                    <p class="text-gray-600 text-sm">Belajar dari praktisi dan dosen berpengalaman di bidangnya</p>
                </div>

                <div
                    class="text-center group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-xl transition">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform shadow-xl">
                        <i class="fas fa-clock text-white text-3xl"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-2 text-lg">Fleksibel</h4>
                    <p class="text-gray-600 text-sm">Belajar kapan saja dan di mana saja sesuai dengan waktumu</p>
                </div>

                <div
                    class="text-center group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-xl transition">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform shadow-xl">
                        <i class="fas fa-infinity text-white text-3xl"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-2 text-lg">Akses Fleksibel</h4>
                    <p class="text-gray-600 text-sm">Akses materi sesuai durasi yang ditentukan atau selamanya</p>
                </div>
            </div>
        </div>

    </div>

    <script>
        (function () {
            const carousel = document.getElementById('promoCarousel');
            if (!carousel) return;

            const slides = Array.from(carousel.children);
            let totalSlides = slides.length;
            let currentSlide = 0;
            let autoSlideInterval;

            // Build dots dynamically
            const dotsContainer = document.querySelector('.carousel-dots');
            if (dotsContainer) {
                dotsContainer.innerHTML = '';
                slides.forEach((_, idx) => {
                    const btn = document.createElement('button');
                    btn.className = 'carousel-dot';
                    btn.textContent = idx + 1;
                    btn.addEventListener('click', () => goToSlide(idx));
                    dotsContainer.appendChild(btn);
                });
            }

            const updateCarousel = () => {
                if (totalSlides === 0) return;
                carousel.style.transform = `translateX(-${currentSlide * 100}%)`;
                document.querySelectorAll('.carousel-dot').forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentSlide);
                });
            };

            const nextSlide = () => {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateCarousel();
            };

            const prevSlide = () => {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                updateCarousel();
            };

            window.goToSlide = (index) => {
                currentSlide = index;
                updateCarousel();
                resetAuto();
            };
            window.nextSlide = () => { nextSlide(); resetAuto(); };
            window.prevSlide = () => { prevSlide(); resetAuto(); };

            const startAuto = () => {
                autoSlideInterval = setInterval(nextSlide, 5000);
            };
            const resetAuto = () => {
                clearInterval(autoSlideInterval);
                startAuto();
            };

            updateCarousel();
            startAuto();

            const container = carousel.parentElement;
            container.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));
            container.addEventListener('mouseleave', startAuto);
        })();

        // Debounce helper
        const debounce = (fn, delay = 350) => {
            let t;
            return (...args) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...args), delay);
            };
        };

        // Auto-submit search with debounce (desktop & mobile search inputs)
        document.addEventListener('DOMContentLoaded', () => {
            const searchInputs = document.querySelectorAll('form[action*="{{ route('home') }}"] input[name="search"]');
            searchInputs.forEach(input => {
                const form = input.closest('form');
                if (!form) return;
                const submitDebounced = debounce(() => form.submit(), 400);
                input.addEventListener('input', () => submitDebounced());
            });
        });

        // Carousel behavior (restore)
        (function () {
            const carousel = document.getElementById('promoCarousel');
            if (!carousel) return;

            const slides = Array.from(carousel.children);
            let totalSlides = slides.length;
            let currentSlide = 0;
            let autoSlideInterval;

            const dotsContainer = document.querySelector('.carousel-dots');
            if (dotsContainer) {
                dotsContainer.innerHTML = '';
                slides.forEach((_, idx) => {
                    const btn = document.createElement('button');
                    btn.className = 'carousel-dot';
                    btn.textContent = idx + 1;
                    btn.addEventListener('click', () => goToSlide(idx));
                    dotsContainer.appendChild(btn);
                });
            }

            const updateCarousel = () => {
                if (totalSlides === 0) return;
                carousel.style.transform = `translateX(-${currentSlide * 100}%)`;
                document.querySelectorAll('.carousel-dot').forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentSlide);
                });
            };

            const nextSlide = () => {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateCarousel();
            };

            const prevSlide = () => {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                updateCarousel();
            };

            window.goToSlide = (index) => {
                currentSlide = index;
                updateCarousel();
                resetAuto();
            };
            window.nextSlide = () => { nextSlide(); resetAuto(); };
            window.prevSlide = () => { prevSlide(); resetAuto(); };

            const startAuto = () => { autoSlideInterval = setInterval(nextSlide, 5000); };
            const resetAuto = () => { clearInterval(autoSlideInterval); startAuto(); };

            updateCarousel();
            startAuto();

            const container = carousel.parentElement;
            container.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));
            container.addEventListener('mouseleave', startAuto);
        })();

        // Sembunyikan skeleton kursus saat halaman siap
        document.addEventListener('DOMContentLoaded', () => {
            const skeleton = document.getElementById('courseSkeleton');
            const grid = document.getElementById('courseGrid');
            if (skeleton && grid) {
                skeleton.classList.add('hidden');
                grid.classList.remove('hidden');
            }
        });
    </script>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .carousel-dot {
            width: 2.25rem;
            height: 2.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            color: #1f2937;
            background: rgba(255, 255, 255, 0.65);
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
        }

        .carousel-dot:hover {
            background: #ffffff;
            transform: translateY(-2px);
        }

        .carousel-dot.active {
            background: #ffffff;
            border-color: #ffffff;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
            transform: translateY(-3px);
        }

        .text-pnj-blue {
            color: #003d82;
        }
    </style>
@endsection