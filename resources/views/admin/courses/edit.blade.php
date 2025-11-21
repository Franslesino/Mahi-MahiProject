{{-- resources/views/admin/courses/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Kursus')

@section('content')
<div class="px-8 pt-6 space-y-6">

    {{-- HEADER --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Kursus</h1>
            <p class="text-gray-600 mt-1">Perbarui informasi kursus</p>
        </div>

        <a href="{{ route('admin.courses.index') }}"
           class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        {{-- FORM EDIT KURSUS --}}
        <form action="{{ route('admin.courses.update', $course) }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Judul Kursus --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Judul Kursus <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="title"
                        value="{{ old('title', $course->judul) }}"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-emerald-500
                               @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Deskripsi <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        name="description"
                        rows="4"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-emerald-500
                               @error('description') border-red-500 @enderror">{{ old('description', $course->deskripsi) }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="category"
                        value="{{ old('category', $course->kategori) }}"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-emerald-500
                               @error('category') border-red-500 @enderror">
                    @error('category')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mode Pembelajaran --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Mode Pembelajaran <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="mode"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white
                               focus:outline-none focus:ring-2 focus:ring-emerald-500
                               @error('mode') border-red-500 @enderror">
                        <option value="Online"  {{ old('mode', $course->mode) === 'Online' ? 'selected' : '' }}>Online</option>
                        <option value="Offline" {{ old('mode', $course->mode) === 'Offline' ? 'selected' : '' }}>Offline</option>
                        <option value="Hybrid"  {{ old('mode', $course->mode) === 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                    </select>
                    @error('mode')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Harga --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Harga (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="price"
                        value="{{ old('price', $course->harga) }}"
                        required
                        min="0"
                        step="0.01"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-emerald-500
                               @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Harga Diskon --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Harga Diskon (Rp)
                    </label>
                    <input
                        type="number"
                        name="discount_price"
                        value="{{ old('discount_price', $course->discount_price) }}"
                        min="0"
                        step="0.01"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-emerald-500
                               @error('discount_price') border-red-500 @enderror">
                    @error('discount_price')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Instruktur --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Instruktur <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="instructor_id"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white
                               focus:outline-none focus:ring-2 focus:ring-emerald-500
                               @error('instructor_id') border-red-500 @enderror">
                        <option value="">Pilih Instruktur</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}"
                                {{ old('instructor_id', $course->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                {{ $instructor->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('instructor_id')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="status"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white
                               focus:outline-none focus:ring-2 focus:ring-emerald-500
                               @error('status') border-red-500 @enderror">
                        <option value="draft"    {{ old('status', $course->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active"   {{ old('status', $course->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $course->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Gambar --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Gambar Kursus
                    </label>
                    @if($course->image)
                        <img src="{{ asset('storage/' . $course->image) }}"
                             alt="Current Image"
                             class="w-32 h-32 object-cover rounded-lg mb-2">
                    @endif
                    <input
                        type="file"
                        name="image"
                        accept="image/jpeg,image/png,image/jpg,image/webp"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-emerald-500
                               @error('image') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 mt-1">
                        Format: JPG, PNG, WEBP. Maksimal 2MB. Kosongkan jika tidak ingin mengubah.
                    </p>
                    @error('image')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Badge --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Badge (Opsional)
                    </label>
                    <input
                        type="text"
                        name="badge"
                        value="{{ old('badge', $course->badge) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                {{-- Badge Color --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Warna Badge
                    </label>
                    <select
                        name="badge_color"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white
                               focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="blue"   {{ old('badge_color', $course->badge_color) === 'blue' ? 'selected' : '' }}>Biru</option>
                        <option value="green"  {{ old('badge_color', $course->badge_color) === 'green' ? 'selected' : '' }}>Hijau</option>
                        <option value="red"    {{ old('badge_color', $course->badge_color) === 'red' ? 'selected' : '' }}>Merah</option>
                        <option value="yellow" {{ old('badge_color', $course->badge_color) === 'yellow' ? 'selected' : '' }}>Kuning</option>
                        <option value="purple" {{ old('badge_color', $course->badge_color) === 'purple' ? 'selected' : '' }}>Ungu</option>
                    </select>
                </div>

                {{-- Yang Akan Dipelajari --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Yang Akan Dipelajari
                    </label>
                    <textarea
                        name="learning"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('learning', $course->learning) }}</textarea>
                </div>
            </div>

            {{-- BUTTONS --}}
            <div class="flex items-center gap-3 pt-2">
                <button
                    type="submit"
                    class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg text-sm font-medium
                           hover:bg-emerald-700 transition">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>

                <a href="{{ route('admin.courses.index') }}"
                   class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
