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
        <div class="flex items-center gap-2">
            @if($course->isOffline() || $course->isHybrid())
                <a href="{{ route('admin.courses.schedules.index', $course) }}"
                   class="flex items-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Kelola Jadwal</span>
                </a>
            @endif
            <a href="{{ route('admin.courses.index') }}"
               class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
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

                {{-- Kategori (dropdown) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    @php
                        $kategoriValue = old('category', $course->kategori);
                        $kategoriGroups = [
                            'Teknologi & IT' => [
                                'Web Development',
                                'Mobile Development',
                                'Data Science',
                                'Design UI/UX',
                                'Cyber Security',
                                'AI / Machine Learning',
                                'Database',
                            ],
                            'Bisnis' => [
                                'Marketing',
                                'Financial Literacy',
                                'Entrepreneurship',
                                'Business Management',
                            ],
                        ];
                    @endphp
                    <select
                        name="category"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white
                               focus:outline-none focus:ring-2 focus:ring-emerald-500
                               @error('category') border-red-500 @enderror">
                        <option value="" {{ $kategoriValue ? '' : 'selected' }}>Pilih kategori</option>
                        @foreach($kategoriGroups as $group => $options)
                            <optgroup label="{{ $group }}">
                                @foreach($options as $opt)
                                    <option value="{{ $opt }}" {{ $kategoriValue === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
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

                <!-- Konfigurasi Mode Offline/Hybrid -->
                <div id="mode-config" class="col-span-1 md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2 {{ in_array(old('mode', $course->mode), ['Offline', 'Hybrid']) ? '' : 'hidden' }}">
                    <h4 class="font-semibold text-gray-800 mb-3 text-sm"><i class="fas fa-cog mr-1"></i> Konfigurasi Offline/Hybrid</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Kapasitas -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kapasitas Maksimal (Siswa)</label>
                            <input type="number" name="max_participants" value="{{ old('max_participants', $course->max_participants) }}" min="1"
                                   placeholder="Contoh: 30"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <p class="text-xs text-gray-500 mt-1">Biarkan kosong untuk tanpa batas.</p>
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Default</label>
                            <input type="text" name="default_location" value="{{ old('default_location', $course->default_location) }}"
                                   placeholder="Contoh: Gedung A, Ruang 101"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <!-- Map Picker -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Lokasi di Peta (Opsional)</label>
                            <div id="course-map" class="w-full h-64 rounded-lg border border-gray-200"></div>
                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $course->latitude) }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $course->longitude) }}">
                            <p class="text-xs text-gray-500 mt-1">Klik atau drag marker di peta untuk menetapkan koordinat.</p>
                        </div>
                        
                        <!-- Mode Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Mode (Opsional)</label>
                            <textarea name="mode_description" rows="2"
                                      placeholder="Penjelasan singkat tentang pelaksanaan metode belajar..."
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('mode_description', $course->mode_description) }}</textarea>
                        </div>
                    </div>
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

                {{-- Lama Akses Kursus --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-calendar-check text-blue-600 mr-1"></i>
                        Lama Akses Kursus Setelah Pembelian (Hari)
                    </label>
                    <input
                        type="number"
                        name="access_duration_days"
                        value="{{ old('access_duration_days', $course->access_duration_days) }}"
                        min="1"
                        placeholder="Kosongkan untuk akses selamanya"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-emerald-500
                               @error('access_duration_days') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i>
                        Berapa hari siswa dapat mengakses materi kursus setelah pembelian. Kosongkan untuk akses unlimited.
                    </p>
                    @error('access_duration_days')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Batas Waktu Pembelian --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-calendar-times text-red-600 mr-1"></i>
                        Batas Waktu Pembelian Kursus
                    </label>
                    <input
                        type="datetime-local"
                        name="purchase_deadline_date"
                        value="{{ old('purchase_deadline_date', $course->purchase_deadline_date?->format('Y-m-d\TH:i')) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-emerald-500
                               @error('purchase_deadline_date') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i>
                        Tanggal dan waktu terakhir siswa dapat membeli kursus ini. Kosongkan jika tidak ada batas waktu.
                    </p>
                    @error('purchase_deadline_date')
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
                    @if($course->image_url)
                        <img src="{{ $course->image_url }}"
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modeSelect = document.querySelector('select[name="mode"]');
        const modeConfig = document.getElementById('mode-config');
        const mapContainer = document.getElementById('course-map');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        let leafletLoaded = false;
        let mapInstance = null;
        let marker = null;

        function toggleModeConfig() {
            if (modeSelect.value === 'Offline' || modeSelect.value === 'Hybrid') {
                modeConfig.classList.remove('hidden');
                initializeMap();
            } else {
                modeConfig.classList.add('hidden');
                destroyMap();
            }
        }

        modeSelect.addEventListener('change', toggleModeConfig);
        toggleModeConfig();

        function loadLeafletAssets(callback) {
            if (leafletLoaded) {
                callback();
                return;
            }

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
                existingScript.addEventListener('load', () => {
                    leafletLoaded = true;
                    callback();
                });
                return;
            }

            const script = document.createElement('script');
            script.id = scriptId;
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = () => {
                leafletLoaded = true;
                callback();
            };
            document.body.appendChild(script);
        }

        function initializeMap() {
            if (!mapContainer || mapInstance) {
                return;
            }

            loadLeafletAssets(() => {
                const defaultLat = parseFloat(latInput?.value) || -6.200000;
                const defaultLng = parseFloat(lngInput?.value) || 106.816666;

                mapInstance = L.map(mapContainer).setView([defaultLat, defaultLng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(mapInstance);

                marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(mapInstance);
                updateInputs(defaultLat, defaultLng);

                mapInstance.on('click', function(e) {
                    marker.setLatLng(e.latlng);
                    updateInputs(e.latlng.lat, e.latlng.lng);
                });

                marker.on('dragend', function(e) {
                    const pos = e.target.getLatLng();
                    updateInputs(pos.lat, pos.lng);
                });
            });
        }

        function destroyMap() {
            if (mapInstance) {
                mapInstance.remove();
                mapInstance = null;
                marker = null;
            }
        }

        function updateInputs(lat, lng) {
            if (latInput && lngInput) {
                latInput.value = lat.toFixed(6);
                lngInput.value = lng.toFixed(6);
            }
        }
    });
</script>
@endpush
