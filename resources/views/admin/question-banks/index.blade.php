@extends('layouts.admin')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Bank Soal</h2>
            <p class="text-gray-600 mt-2">Kelola koleksi soal terorganisir dalam folder dengan fitur public/private sharing</p>
        </div>
        <a href="{{ route('admin.question-banks.create') }}" 
           class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            <i class="fas fa-plus"></i>
            Buat Bank Soal Baru
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.question-banks.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[250px]">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari bank soal..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <div class="w-64">
                <select name="category" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            @if(request('search') || request('category'))
            <a href="{{ route('admin.question-banks.index') }}" 
               class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                <i class="fas fa-times mr-2"></i>Reset
            </a>
            @endif
        </form>
    </div>

    @if($banks->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="fas fa-folder-open text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Bank Soal</h3>
            <p class="text-gray-600 mb-6">Buat bank soal pertama Anda untuk menyimpan koleksi soal</p>
            <a href="{{ route('admin.question-banks.create') }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                <i class="fas fa-plus"></i>
                Buat Bank Soal
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($banks as $bank)
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $bank->title }}</h3>
                            @if($bank->category)
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">
                                {{ $bank->category }}
                            </span>
                            @endif
                        </div>
                        @if($bank->is_public)
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">
                            <i class="fas fa-globe text-xs"></i> Public
                        </span>
                        @endif
                    </div>

                    @if($bank->description)
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $bank->description }}</p>
                    @endif

                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                        <span>
                            <i class="fas fa-question-circle mr-1"></i>
                            {{ $bank->questions_count }} soal
                        </span>
                        <span>
                            <i class="fas fa-user mr-1"></i>
                            {{ $bank->creator->name }}
                        </span>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('admin.question-banks.show', $bank) }}" 
                           class="flex-1 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-center font-medium text-sm">
                            <i class="fas fa-eye mr-1"></i> Lihat
                        </a>
                        @if($bank->created_by == auth()->id())
                        <a href="{{ route('admin.question-banks.edit', $bank) }}" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.question-banks.destroy', $bank) }}" 
                              method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus bank soal ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium text-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $banks->links() }}
        </div>
    @endif
</div>
@endsection
