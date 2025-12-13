@extends('layouts.admin')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <a href="{{ route('admin.promo-banners.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2 mb-4">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Daftar Banner</span>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Edit Banner Promo</h2>
    </div>

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <p class="font-semibold">Terdapat kesalahan:</p>
        <ul class="list-disc list-inside mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.promo-banners.update', $promoBanner->id) }}" method="POST" class="bg-white rounded-lg shadow-sm p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Judul -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Judul Banner <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title', $promoBanner->title) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Weekend Special"
                       required>
            </div>

            <!-- Badge -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Badge Label
                </label>
                <input type="text" 
                       name="badge" 
                       value="{{ old('badge', $promoBanner->badge) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="FLASH SALE">
            </div>

            <!-- Urutan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Urutan Tampil <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="order" 
                       value="{{ old('order', $promoBanner->order) }}"
                       min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       required>
            </div>

            <!-- Deskripsi -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi <span class="text-red-500">*</span>
                </label>
                <textarea name="description" 
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                          placeholder="Diskon hingga 60% untuk Kursus Pilihan Akhir Pekan"
                          required>{{ old('description', $promoBanner->description) }}</textarea>
            </div>

            <!-- Button Text -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Teks Tombol <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="button_text" 
                       value="{{ old('button_text', $promoBanner->button_text) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       required>
            </div>

            <!-- Button Link -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Link Tombol
                </label>
                <input type="url" 
                       name="button_link" 
                       value="{{ old('button_link', $promoBanner->button_link) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="https://...">
            </div>

            <!-- Gradient From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Warna Gradient Awal <span class="text-red-500">*</span>
                </label>
                <select name="gradient_from" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        required>
                    <option value="orange-500" {{ old('gradient_from', $promoBanner->gradient_from) === 'orange-500' ? 'selected' : '' }}>Orange</option>
                    <option value="red-500" {{ old('gradient_from', $promoBanner->gradient_from) === 'red-500' ? 'selected' : '' }}>Red</option>
                    <option value="pink-500" {{ old('gradient_from', $promoBanner->gradient_from) === 'pink-500' ? 'selected' : '' }}>Pink</option>
                    <option value="purple-500" {{ old('gradient_from', $promoBanner->gradient_from) === 'purple-500' ? 'selected' : '' }}>Purple</option>
                    <option value="indigo-500" {{ old('gradient_from', $promoBanner->gradient_from) === 'indigo-500' ? 'selected' : '' }}>Indigo</option>
                    <option value="blue-500" {{ old('gradient_from', $promoBanner->gradient_from) === 'blue-500' ? 'selected' : '' }}>Blue</option>
                    <option value="cyan-500" {{ old('gradient_from', $promoBanner->gradient_from) === 'cyan-500' ? 'selected' : '' }}>Cyan</option>
                    <option value="teal-500" {{ old('gradient_from', $promoBanner->gradient_from) === 'teal-500' ? 'selected' : '' }}>Teal</option>
                    <option value="green-500" {{ old('gradient_from', $promoBanner->gradient_from) === 'green-500' ? 'selected' : '' }}>Green</option>
                    <option value="emerald-500" {{ old('gradient_from', $promoBanner->gradient_from) === 'emerald-500' ? 'selected' : '' }}>Emerald</option>
                </select>
            </div>

            <!-- Gradient To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Warna Gradient Akhir <span class="text-red-500">*</span>
                </label>
                <select name="gradient_to" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        required>
                    <option value="red-600" {{ old('gradient_to', $promoBanner->gradient_to) === 'red-600' ? 'selected' : '' }}>Red</option>
                    <option value="orange-600" {{ old('gradient_to', $promoBanner->gradient_to) === 'orange-600' ? 'selected' : '' }}>Orange</option>
                    <option value="pink-600" {{ old('gradient_to', $promoBanner->gradient_to) === 'pink-600' ? 'selected' : '' }}>Pink</option>
                    <option value="purple-600" {{ old('gradient_to', $promoBanner->gradient_to) === 'purple-600' ? 'selected' : '' }}>Purple</option>
                    <option value="indigo-600" {{ old('gradient_to', $promoBanner->gradient_to) === 'indigo-600' ? 'selected' : '' }}>Indigo</option>
                    <option value="blue-600" {{ old('gradient_to', $promoBanner->gradient_to) === 'blue-600' ? 'selected' : '' }}>Blue</option>
                    <option value="cyan-600" {{ old('gradient_to', $promoBanner->gradient_to) === 'cyan-600' ? 'selected' : '' }}>Cyan</option>
                    <option value="teal-600" {{ old('gradient_to', $promoBanner->gradient_to) === 'teal-600' ? 'selected' : '' }}>Teal</option>
                    <option value="green-600" {{ old('gradient_to', $promoBanner->gradient_to) === 'green-600' ? 'selected' : '' }}>Green</option>
                    <option value="emerald-600" {{ old('gradient_to', $promoBanner->gradient_to) === 'emerald-600' ? 'selected' : '' }}>Emerald</option>
                </select>
            </div>

            <!-- Tanggal Mulai -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Mulai
                </label>
                <input type="date" 
                       name="start_date" 
                       value="{{ old('start_date', $promoBanner->start_date ? $promoBanner->start_date->format('Y-m-d') : '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Tanggal Berakhir -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Berakhir
                </label>
                <input type="date" 
                       name="end_date" 
                       value="{{ old('end_date', $promoBanner->end_date ? $promoBanner->end_date->format('Y-m-d') : '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Status Aktif -->
            <div class="md:col-span-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', $promoBanner->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                    <span class="text-sm font-medium text-gray-700">Aktifkan banner</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" 
                    class="px-6 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 font-semibold">
                <i class="fas fa-save mr-2"></i>
                Update Banner
            </button>
            <a href="{{ route('admin.promo-banners.index') }}" 
               class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
