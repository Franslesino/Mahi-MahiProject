@extends('layouts.instructor')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kursus Saya</h2>
        <p class="text-gray-600 mt-1">Kelola materi dan konten kursus Anda</p>
    </div>

    @if($courses->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <i class="fas fa-book text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Kursus</h3>
            <p class="text-gray-500 mb-2">Anda belum memiliki kursus yang terdaftar.</p>
            <p class="text-sm text-gray-400">Hubungi admin untuk menambahkan kursus ke akun Anda.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                    <div class="h-48 bg-gradient-to-br from-blue-500 to-blue-600 relative">
                        @if($course->image)
                            <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white text-6xl opacity-20"></i>
                            </div>
                        @endif
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $course->status === 'active' ? 'bg-green-100 text-green-700' : ($course->status === 'draft' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ ucfirst($course->status) }}
                            </span>
                        </div>
                        @if($course->badge)
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-{{ $course->badge_color }}-100 text-{{ $course->badge_color }}-700">
                                    {{ $course->badge }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="p-6">
                        <div class="mb-2">
                            <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded">
                                {{ $course->category }}
                            </span>
                            <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-1 rounded ml-2">
                                {{ $course->mode }}
                            </span>
                        </div>

                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                            {{ $course->title }}
                        </h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {{ $course->description }}
                        </p>

                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <div class="flex items-center gap-1">
                                <i class="fas fa-file-alt"></i>
                                <span>{{ $course->materials_count }} Materi</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fas fa-video"></i>
                                <span>{{ $course->videos }} Video</span>
                            </div>
                        </div>

                        @if($course->price)
                            <div class="flex items-center gap-2 mb-4">
                                @if($course->discount_price)
                                    <span class="text-lg font-bold text-green-600">Rp {{ number_format($course->discount_price, 0, ',', '.') }}</span>
                                    <span class="text-sm text-gray-500 line-through">Rp {{ number_format($course->price, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-lg font-bold text-gray-900">Rp {{ number_format($course->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        @endif

                        <a href="{{ route('instructor.courses.show', $course) }}" 
                           class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Kelola Materi
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection