{{-- resources/views/admin/courses/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <a href="{{ route('admin.courses.index') }}" 
   class="text-emerald-600 hover:text-emerald-700 flex items-center gap-2">
   <i class="fas fa-arrow-left"></i>
   <span>Kembali ke Daftar Kursus</span>
</a>

    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Kursus</h2>

        <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Judul Kursus -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Kursus *</label>
                    <input type="text" name="title" value="{{ old('title') }}">
 required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('title') border-red-500 @enderror">
                    @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi *</label>
                    <textarea name="description" rows="4" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('description') border-red-500 @enderror">{{ old('description', $course->description) }}</textarea>
                    @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                    <input type="text" name="category" value="{{ old('category', $course->category) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('category') border-red-500 @enderror">
                    @error('category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mode -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mode Pembelajaran *</label>
                    <select name="mode" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('mode') border-red-500 @enderror">
                        <option value="Online" {{ old('mode', $course->mode) === 'Online' ? 'selected' : '' }}>Online</option>
                        <option value="Offline" {{ old('mode', $course->mode) === 'Offline' ? 'selected' : '' }}>Offline</option>
                        <option value="Hybrid" {{ old('mode', $course->mode) === 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                    </select>
                    @error('mode')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga (Rp) *</label>
                    <input type="number" name="price" value="{{ old('price', $course->price) }}" required min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('price') border-red-500 @enderror">
                    @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga Diskon -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga Diskon (Rp)</label>
                    <input type="number" name="discount_price" value="{{ old('discount_price', $course->discount_price) }}" min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('discount_price') border-red-500 @enderror">
                    @error('discount_price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Instruktur -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Instruktur *</label>
                    <select name="instructor_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('instructor_id') border-red-500 @enderror">
                        <option value="">Pilih Instruktur</option>
                        @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" {{ old('instructor_id', $course->instructor_id) == $instructor->id ? 'selected' : '' }}>
                            {{ $instructor->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('instructor_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('status') border-red-500 @enderror">
                        <option value="draft" {{ old('status', $course->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $course->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $course->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gambar -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Kursus</label>
                    @if($course->image)
                    <img src="{{ asset('storage/' . $course->image) }}" alt="Current Image" class="w-32 h-32 object-cover rounded-lg mb-2">
                    @endif
                    <input type="file" name="image" accept="image/jpeg,image/png,image/jpg"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('image') border-red-500 @enderror">
                    <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB. Kosongkan jika tidak ingin mengubah.</p>
                    @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Badge -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Badge (Opsional)</label>
                    <input type="text" name="badge" value="{{ old('badge', $course->badge) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Badge Color -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warna Badge</label>
                    <select name="badge_color"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="blue" {{ old('badge_color', $course->badge_color) === 'blue' ? 'selected' : '' }}>Biru</option>
                        <option value="green" {{ old('badge_color', $course->badge_color) === 'green' ? 'selected' : '' }}>Hijau</option>
                        <option value="red" {{ old('badge_color', $course->badge_color) === 'red' ? 'selected' : '' }}>Merah</option>
                        <option value="yellow" {{ old('badge_color', $course->badge_color) === 'yellow' ? 'selected' : '' }}>Kuning</option>
                        <option value="purple" {{ old('badge_color', $course->badge_color) === 'purple' ? 'selected' : '' }}>Ungu</option>
                    </select>
                </div>

                <!-- Yang Dipelajari -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Yang Akan Dipelajari</label>
                    <textarea name="learning" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('learning', $course->learning) }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-4 mt-8">
                <button type="submit"
                        class="px-6 py-3 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Update Kursus
                </button>
                <a href="{{ route('admin.courses.index') }}"
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection