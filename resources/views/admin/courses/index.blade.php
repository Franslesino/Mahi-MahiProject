@extends('layouts.admin')

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Kelola Kursus')

@section('content')

<div class="px-8 pt-6 space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Kursus</h1>
            <p class="text-gray-600 mt-1">Manage semua kursus dan assign instruktur</p>
        </div>

        <a href="{{ route('admin.courses.create') }}"
           class="flex items-center gap-2 px-5 py-3 bg-emerald-600 !text-white font-semibold rounded-lg hover:bg-emerald-700 transition shadow">
            <i class="fas fa-plus text-white"></i>
            <span class="!text-white">Tambah Kursus</span>
        </a>
    </div>

    {{-- SEARCH + FILTER --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.courses.index') }}"
              class="flex flex-col md:flex-row items-stretch md:items-center gap-3">

            {{-- SEARCH BAR (client debounce filter + server submit) --}}
            <div class="flex-1">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text"
                           name="search"
                           id="coursesQuickSearch"
                           placeholder="Cari kursus..."
                           value="{{ request('search') }}"
                           class="w-full pl-9 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            {{-- STATUS FILTER --}}
            <div class="w-full md:w-48">
                <select name="status"
                        class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="" {{ request('status') === null ? 'selected' : '' }}>Semua Status</option>
                    <option value="active"   {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="draft"    {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            {{-- BUTTON SEARCH --}}
            <button type="submit"
                    class="px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>


    {{-- TABLE KURSUS --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Image</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Kursus</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Instruktur</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Materi</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($courses as $course)
                        <tr class="hover:bg-gray-50">

                            {{-- IMAGE --}}
                            <td class="px-6 py-4">
                                @if($course->image && Storage::disk('public')->exists($course->image))
                                    <img src="{{ Storage::url($course->image) }}"
                                         class="w-16 h-16 rounded-lg object-cover">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </td>

                            {{-- NAMA KURSUS --}}
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">{{ $course->judul }}</p>
                                <p class="text-sm text-gray-500 truncate max-w-xs">
                                    {{ Str::limit($course->deskripsi, 60) }}
                                </p>
                            </td>

                            {{-- INSTRUKTUR --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($course->instructor)
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center">
                                            {{ strtoupper(substr($course->instructor->name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $course->instructor->name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>

                            {{-- KATEGORI --}}
                            <td class="px-6 py-4 text-sm text-gray-800">
                                {{ $course->kategori }}
                            </td>

                            {{-- HARGA --}}
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">
                                    Rp {{ number_format($course->harga, 0, ',', '.') }}
                                </p>

                                @if($course->discount_price > 0)
                                    <p class="text-xs text-green-600">
                                        Diskon: Rp {{ number_format($course->discount_price, 0, ',', '.') }}
                                    </p>
                                @endif
                            </td>

                            {{-- MATERI --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                    {{ $course->materi_count }} Materi
                                </span>
                            </td>

                            {{-- STATUS --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($course->status === 'active')
                                    <span class="px-2.5 py-0.5 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                @elseif($course->status === 'draft')
                                    <span class="px-2.5 py-0.5 bg-yellow-100 text-yellow-800 text-xs rounded-full">Draft</span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-gray-100 text-gray-800 text-xs rounded-full">Inactive</span>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">

                                    {{-- KELOLA MATERI --}}
                                    <a href="{{ route('admin.courses.materials.index', $course) }}"
                                       class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-xs font-medium"
                                       title="Kelola Materi">
                                        <i class="fas fa-book-open mr-1"></i>
                                        Materi
                                    </a>

                                    {{-- LIHAT DETAIL --}}
                                    <a href="{{ route('admin.courses.show', $course) }}"
                                       class="text-gray-600 hover:text-gray-800"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- EDIT --}}
                                    <a href="{{ route('admin.courses.edit', $course) }}"
                                       class="text-emerald-600 hover:text-emerald-800"
                                       title="Edit kursus">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- DELETE --}}
                                    <form method="POST"
                                          action="{{ route('admin.courses.destroy', $course) }}"
                                          data-confirm="Yakin ingin menghapus kursus ini? Tindakan tidak bisa dibatalkan.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus kursus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                Tidak ada kursus ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($courses->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $courses->links() }}
            </div>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
const debounce = (fn, delay = 250) => {
    let t;
    return (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn(...args), delay);
    };
};

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('coursesQuickSearch');
    const rows = document.querySelectorAll('table tbody tr');
    if (searchInput && rows.length) {
        const filterRows = debounce(() => {
            const q = searchInput.value.trim().toLowerCase();
            rows.forEach(row => {
                const haystack = row.innerText.toLowerCase();
                row.style.display = haystack.includes(q) ? '' : 'none';
            });
        }, 200);
        searchInput.addEventListener('input', filterRows);
    }
});
</script>
@endpush
