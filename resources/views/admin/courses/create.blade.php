{{-- resources/views/admin/courses/create.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="p-8">
    <div class="mb-6">
      <a href="{{ route('admin.courses.index') }}" class="text-emerald-600 hover:text-emerald-700 flex items-center gap-2">

            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Daftar Kursus</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Kursus Baru</h2>

        <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Judul Kursus -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Kursus *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('title') border-red-500 @enderror">
                    @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi *</label>
                    <textarea name="description" rows="4" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                    <input type="text" name="category" value="{{ old('category') }}" required
                           placeholder="Contoh: Programming, Design, Business"
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
                        <option value="Online" {{ old('mode') === 'Online' ? 'selected' : '' }}>Online</option>
                        <option value="Offline" {{ old('mode') === 'Offline' ? 'selected' : '' }}>Offline</option>
                        <option value="Hybrid" {{ old('mode') === 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                    </select>
                    @error('mode')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga (Rp) *</label>
                    <input type="number" name="price" value="{{ old('price') }}" required min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('price') border-red-500 @enderror">
                    @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga Diskon -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga Diskon (Rp)</label>
                    <input type="number" name="discount_price" value="{{ old('discount_price') }}" min="0" step="0.01"
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
                        <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
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
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gambar -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Kursus</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/jpg"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('image') border-red-500 @enderror">
                    <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB</p>
                    @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Badge -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Badge (Opsional)</label>
                    <input type="text" name="badge" value="{{ old('badge') }}"
                           placeholder="Contoh: Populer, Terbaru"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Badge Color -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warna Badge</label>
                    <select name="badge_color"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="blue">Biru</option>
                        <option value="green">Hijau</option>
                        <option value="red">Merah</option>
                        <option value="yellow">Kuning</option>
                        <option value="purple">Ungu</option>
                    </select>
                </div>

                <!-- Yang Dipelajari -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Yang Akan Dipelajari</label>
                    <textarea name="learning" rows="3"
                              placeholder="Masukkan poin-poin pembelajaran, pisahkan dengan enter"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('learning') }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-4 mt-8">
                <button type="submit"
                        class="px-6 py-3 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Kursus
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

