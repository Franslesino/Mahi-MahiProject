@extends('layouts.admin')

@section('content')
@php
    $categories = collect($categories ?? []);
    $oldCategory = old('category');
    $isCustomCategory = $oldCategory && !$categories->contains($oldCategory);
@endphp

<div class="p-8 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Buat Bank Soal Baru</h2>
        <p class="text-gray-600 mt-2">Buat koleksi soal untuk digunakan dalam quiz dan assignment</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.question-banks.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Bank Soal <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           value="{{ old('title') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('title') border-red-500 @enderror"
                           placeholder="Contoh: Soal Pemrograman Web"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select id="category-select"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('category') border-red-500 @enderror">
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" @selected($oldCategory === $category)>{{ $category }}</option>
                        @endforeach
                        <option value="__new__" @selected($isCustomCategory || $categories->isEmpty())>Kategori baru...</option>
                    </select>
                    <div id="category-input-wrapper" class="mt-2 @if(!($isCustomCategory || $categories->isEmpty())) hidden @endif">
                        <input type="text"
                               id="category-input"
                               name="category"
                               value="{{ $oldCategory }}"
                               data-initial="{{ $oldCategory }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                               placeholder="Tulis kategori baru">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Pilih kategori yang tersedia atau pilih "Kategori baru..." untuk mengetik manual.</p>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" 
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                              placeholder="Jelaskan tentang bank soal ini...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Public Checkbox -->
                <div class="flex items-start">
                    <input type="checkbox" 
                           name="is_public" 
                           id="is_public"
                           value="1"
                           {{ old('is_public') ? 'checked' : '' }}
                           class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="is_public" class="ml-3">
                        <span class="block text-sm font-medium text-gray-700">
                            Bank Soal Public
                        </span>
                        <span class="block text-sm text-gray-500">
                            Centang jika ingin bank soal ini dapat diakses oleh instruktur lain
                        </span>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-between pt-4 border-t">
                    <a href="{{ route('admin.question-banks.index') }}" 
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        <i class="fas fa-save mr-2"></i>Simpan Bank Soal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectEl = document.getElementById('category-select');
        const inputWrapper = document.getElementById('category-input-wrapper');
        const inputEl = document.getElementById('category-input');

        if (!selectEl || !inputEl || !inputWrapper) return;

        const syncInput = () => {
            if (selectEl.value === '__new__') {
                inputWrapper.classList.remove('hidden');
                if (!inputEl.value) {
                    inputEl.value = inputEl.dataset.initial || '';
                }
                inputEl.focus();
            } else {
                inputWrapper.classList.add('hidden');
                inputEl.value = selectEl.value || '';
            }
        };

        syncInput();
        selectEl.addEventListener('change', syncInput);
    });
</script>
@endsection
