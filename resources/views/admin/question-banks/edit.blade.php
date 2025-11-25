@extends('layouts.admin')

@section('content')
<div class="p-8 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Edit Bank Soal</h2>
        <p class="text-gray-600 mt-2">Perbarui informasi bank soal</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.question-banks.update', $questionBank) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Bank Soal <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           value="{{ old('title', $questionBank->title) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('title') border-red-500 @enderror"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <input type="text" 
                           name="category" 
                           value="{{ old('category', $questionBank->category) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                           placeholder="Contoh: Programming, Mathematics, Language">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" 
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">{{ old('description', $questionBank->description) }}</textarea>
                </div>

                <!-- Public Checkbox -->
                <div class="flex items-start">
                    <input type="checkbox" 
                           name="is_public" 
                           id="is_public"
                           value="1"
                           {{ old('is_public', $questionBank->is_public) ? 'checked' : '' }}
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
                    <a href="{{ route('admin.question-banks.show', $questionBank) }}" 
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
