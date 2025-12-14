@extends('layouts.admin')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.courses.index') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-3xl font-bold text-gray-800">Kelola Materi</h2>
            </div>
            <p class="text-gray-600">{{ $course->judul }}</p>
        </div>
        <a href="{{ route('admin.courses.materials.create', $course) }}" 
           class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            <i class="fas fa-plus mr-2"></i>Tambah Materi
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    <!-- Materials List -->
    @if($materials->isEmpty())
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-book-open text-gray-400 text-3xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Materi</h3>
        <p class="text-gray-500 mb-4">Mulai tambahkan materi untuk kursus ini.</p>
        <a href="{{ route('admin.courses.materials.create', $course) }}" 
           class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            <i class="fas fa-plus mr-2"></i>Tambah Materi Pertama
        </a>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Order
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Judul Materi
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipe
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Durasi
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($materials as $material)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">{{ $material->urutan }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $material->judul }}</div>
                                @if($material->isi)
                                <div class="text-sm text-gray-500 line-clamp-1">{{ Str::limit($material->isi, 60) }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($material->url_konten)
                                @if(Str::contains($material->url_konten, ['youtube', 'vimeo', 'http']))
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs rounded-full font-medium">
                                    <i class="fas fa-video mr-1"></i>Video
                                </span>
                                @elseif(Str::endsWith($material->url_konten, ['.pdf', '.doc', '.docx', '.ppt', '.pptx']))
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">
                                    <i class="fas fa-file-pdf mr-1"></i>Dokumen
                                </span>
                                @else
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">
                                    <i class="fas fa-file mr-1"></i>File
                                </span>
                                @endif
                            @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full font-medium">
                                <i class="fas fa-align-left mr-1"></i>Teks
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                            -
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.courses.materials.edit', [$course, $material]) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.courses.materials.destroy', [$course, $material]) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Yakin ingin menghapus materi ini?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
