@extends('layouts.admin')

@section('title', 'Kelola Jadwal - ' . $course->judul)

@section('content')
<div class="px-8 pt-6 space-y-6 pb-20">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Jadwal Pertemuan</h1>
            <p class="text-gray-600 mt-1">{{ $course->judul }}</p>
            <span class="inline-flex items-center mt-2 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $course->isOnline() ? 'bg-blue-100 text-blue-800' : ($course->isOffline() ? 'bg-orange-100 text-orange-800' : 'bg-purple-100 text-purple-800') }}">
                Mode {{ ucfirst($course->mode) }}
            </span>
        </div>
        <a href="{{ route('admin.courses.detail', $course) }}"
           class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Detail</span>
        </a>
    </div>

    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Form Tambah Jadwal -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sticky top-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-plus text-emerald-600"></i> Tambah Jadwal Baru
                </h3>
                
                <form action="{{ route('admin.courses.schedules.store', $course) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Pertemuan *</label>
                        <input type="text" name="title" required placeholder="Contoh: Sesi 1 - Pengenalan"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                        <input type="date" name="date" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mulai *</label>
                            <input type="time" name="start_time" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Selesai *</label>
                            <input type="time" name="end_time" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi (Opsional)</label>
                        <input type="text" name="location" value="{{ $course->default_location }}" placeholder="Nama Gedung / Ruangan"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan untuk menggunakan lokasi default kursus.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link Meeting (Opsional)</label>
                        <input type="url" name="meeting_url" placeholder="https://zoom.us/..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                        <p class="text-xs text-gray-500 mt-1">Isi jika pertemuan dilakukan secara hybrid/online.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                        <textarea name="description" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Jadwal
                    </button>
                </form>
            </div>
        </div>

        <!-- Daftar Jadwal -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Daftar Jadwal ({{ $schedules->count() }})</h3>
                </div>

                @if($schedules->isEmpty())
                    <div class="p-12 text-center text-gray-500">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                            <i class="far fa-calendar text-2xl text-gray-400"></i>
                        </div>
                        <p class="font-medium text-gray-900">Belum ada jadwal pertemuan</p>
                        <p class="text-sm mt-1">Tambahkan jadwal pertemuan pertama Anda melalui form di sebelah kiri.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-200">
                        @foreach($schedules as $schedule)
                            <div class="p-6 hover:bg-gray-50 transition group">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h4 class="font-bold text-gray-900 text-lg">{{ $schedule->title }}</h4>
                                            
                                            <!-- Status Badge -->
                                            @php
                                                $statusColors = [
                                                    'scheduled' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    'completed' => 'bg-green-50 text-green-700 border-green-200',
                                                    'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                                                ];
                                            @endphp
                                            <span class="px-2.5 py-0.5 rounded text-xs font-semibold border {{ $statusColors[$schedule->status] ?? 'bg-gray-50' }}">
                                                {{ ucfirst($schedule->status) }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-600 mb-3">
                                            <div class="flex items-center gap-2">
                                                <i class="far fa-calendar-alt text-gray-400"></i>
                                                <span class="font-medium text-gray-900">{{ $schedule->date->format('l, d M Y') }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i class="far fa-clock text-gray-400"></i>
                                                <span>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                            </div>
                                        </div>

                                        @if($schedule->location)
                                            <div class="flex items-center gap-2 text-sm text-gray-600 mb-1">
                                                <i class="fas fa-map-marker-alt text-gray-400 w-5 text-center"></i>
                                                <span>{{ $schedule->location }}</span>
                                            </div>
                                        @endif

                                        @if($schedule->meeting_url)
                                            <div class="flex items-center gap-2 text-sm text-blue-600 mb-1">
                                                <i class="fas fa-video w-5 text-center"></i>
                                                <a href="{{ $schedule->meeting_url }}" target="_blank" class="hover:underline flex items-center gap-1">
                                                    Link Meeting <i class="fas fa-external-link-alt text-xs"></i>
                                                </a>
                                            </div>
                                        @endif

                                        @if($schedule->description)
                                            <div class="mt-3 text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                                {{ $schedule->description }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center gap-2 ml-4 self-start">
                                        <!-- Edit Button (Trigger Modal) -->
                                        <button onclick="openEditModal({{ $schedule->id }})" 
                                                class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                                title="Edit Jadwal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <!-- Delete Form -->
                                        <form action="{{ route('admin.courses.schedules.destroy', [$course, $schedule]) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                    title="Hapus Jadwal">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Edit Modal (Native HTML Dialog) -->
                            <dialog id="edit_modal_{{ $schedule->id }}" class="modal p-0 rounded-xl shadow-2xl backdrop:bg-black/50 bg-transparent" style="max-width: 90vw;">
                                <div class="w-[500px] max-w-full bg-white rounded-xl shadow-xl overflow-hidden">
                                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                        <h3 class="text-lg font-bold text-gray-800">Edit Jadwal</h3>
                                        <button onclick="document.getElementById('edit_modal_{{ $schedule->id }}').close()" class="text-gray-400 hover:text-gray-600 transition">
                                            <i class="fas fa-times text-xl"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="p-6">
                                        <form action="{{ route('admin.courses.schedules.update', [$course, $schedule]) }}" method="POST" class="space-y-4">
                                            @csrf
                                            @method('PUT')
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Pertemuan *</label>
                                                <input type="text" name="title" value="{{ $schedule->title }}" required
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                                                <input type="date" name="date" value="{{ $schedule->date->format('Y-m-d') }}" required
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mulai *</label>
                                                    <input type="time" name="start_time" value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}" required
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Selesai *</label>
                                                    <input type="time" name="end_time" value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}" required
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                                                <input type="text" name="location" value="{{ $schedule->location }}"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Link Meeting</label>
                                                <input type="url" name="meeting_url" value="{{ $schedule->meeting_url }}"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                                <textarea name="description" rows="2"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">{{ $schedule->description }}</textarea>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                                                    <option value="scheduled" {{ $schedule->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                                    <option value="completed" {{ $schedule->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="cancelled" {{ $schedule->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                            </div>

                                            <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                                                <button type="button" onclick="document.getElementById('edit_modal_{{ $schedule->id }}').close()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition">Batal</button>
                                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </dialog>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function openEditModal(id) {
        document.getElementById('edit_modal_' + id).showModal();
    }
</script>
@endsection
