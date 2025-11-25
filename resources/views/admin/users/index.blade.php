@extends('layouts.admin')

@section('content')
<div class="p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kelola Pengguna</h2>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 flex items-center gap-2">
            <i class="fas fa-user-plus"></i>
            <span>Tambah Pengguna</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center space-x-3">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
                    <input type="text" name="search" placeholder="Cari pengguna..." 
                           value="{{ request('search') }}"
                           class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <select name="role" class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="instructor" {{ request('role') === 'instructor' ? 'selected' : '' }}>Instruktur</option>
                        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        @if($users->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bergabung</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600">#{{ $user->id }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($user->avatar_path)
                                    <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                    </div>
                                @endif
                                <p class="font-medium text-gray-800">{{ $user->name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if($user->role === 'admin')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">Admin</span>
                            @elseif($user->role === 'instructor')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Instruktur</span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">User</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                {{-- TOMBOL SHOW/VIEW (BARU) --}}
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-emerald-600 hover:text-emerald-800" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                {{-- TOMBOL EDIT --}}
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- TOMBOL DELETE --}}
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4">
            {{ $users->links() }}
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-16 px-6">
            <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg font-semibold">Belum ada data pengguna</p>
            <p class="text-gray-400 text-sm mt-2 text-center">Daftar pengguna akan ditampilkan di sini</p>
        </div>
        @endif
    </div>
</div>
@endsection