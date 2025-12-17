@extends('layouts.admin')

@section('content')
    <div class="p-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Kelola Pengguna</h2>
            <div class="flex items-center gap-3">
                {{-- Export Button --}}
                <a href="{{ route('admin.users.export', request()->only('search', 'role')) }}"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 flex items-center gap-2">
                    <i class="fas fa-file-csv"></i>
                    <span>Export CSV</span>
                </a>
                {{-- Add User Button --}}
                <a href="{{ route('admin.users.create') }}"
                    class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 flex items-center gap-2">
                    <i class="fas fa-user-plus"></i>
                    <span>Tambah Pengguna</span>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center space-x-3">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
                        <input type="text" name="search" placeholder="Cari pengguna..." value="{{ request('search') }}"
                            class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <select name="role"
                            class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Semua Role</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="instructor" {{ request('role') === 'instructor' ? 'selected' : '' }}>Instruktur
                            </option>
                            <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                {{-- Bulk Delete Button (hidden by default) --}}
                <button type="button" id="bulkDeleteBtn"
                    class="hidden px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center gap-2">
                    <i class="fas fa-trash"></i>
                    <span>Hapus Terpilih (<span id="selectedCount">0</span>)</span>
                </button>
            </div>

            @if($users->count() > 0)
                <form id="bulkDeleteForm" action="{{ route('admin.users.bulk-destroy') }}" method="POST">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input type="checkbox" id="selectAll"
                                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    </th>
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
                                        <td class="px-6 py-4">
                                            @if($user->id !== auth()->id())
                                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                                    class="user-checkbox rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                            @else
                                                <span class="text-gray-400" title="Tidak dapat menghapus akun sendiri">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">#{{ $user->id }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                @if($user->avatar_url)
                                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                                        class="w-10 h-10 rounded-full object-cover">
                                                @else
                                                    <div
                                                        class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center">
                                                        <span
                                                            class="text-white font-bold text-sm">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                                    </div>
                                                @endif
                                                <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                                        <td class="px-6 py-4">
                                            @if($user->role === 'admin')
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">Admin</span>
                                            @elseif($user->role === 'instructor')
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Instruktur</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                                    {{ $user->role === 'student' ? 'Student' : ucfirst($user->role) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->format('d M Y') }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('admin.users.show', $user->id) }}"
                                                    class="text-emerald-600 hover:text-emerald-800" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                                    class="text-blue-600 hover:text-blue-800" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                        class="inline" data-confirm-title="Hapus pengguna?"
                                                        data-confirm="Yakin ingin menghapus pengguna ini?">
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
                </form>

                <div class="px-6 py-4">
                    {{ $users->links() }}
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-16 px-6">
                    <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-semibold">Belum ada data pengguna</p>
                    <p class="text-gray-400 text-sm mt-2 text-center">Daftar pengguna akan ditampilkan di sini</p>
                    <a href="{{ route('admin.users.create') }}"
                        class="mt-4 px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600">
                        Tambah Pengguna
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Bulk Delete Confirmation Modal --}}
    <div id="bulkDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Konfirmasi Hapus</h3>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus <span id="modalCount"
                    class="font-bold text-red-600">0</span> pengguna yang dipilih?</p>
            <div class="flex justify-end gap-3">
                <button type="button" id="cancelBulkDelete"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
                <button type="button" id="confirmBulkDelete"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const selectAll = document.getElementById('selectAll');
                const checkboxes = document.querySelectorAll('.user-checkbox');
                const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
                const selectedCount = document.getElementById('selectedCount');
                const bulkDeleteForm = document.getElementById('bulkDeleteForm');
                const modal = document.getElementById('bulkDeleteModal');
                const cancelBtn = document.getElementById('cancelBulkDelete');
                const confirmBtn = document.getElementById('confirmBulkDelete');
                const modalCount = document.getElementById('modalCount');

                function updateSelectedCount() {
                    const checked = document.querySelectorAll('.user-checkbox:checked').length;
                    selectedCount.textContent = checked;
                    modalCount.textContent = checked;

                    if (checked > 0) {
                        bulkDeleteBtn.classList.remove('hidden');
                        bulkDeleteBtn.classList.add('flex');
                    } else {
                        bulkDeleteBtn.classList.add('hidden');
                        bulkDeleteBtn.classList.remove('flex');
                    }
                }

                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        checkboxes.forEach(cb => cb.checked = this.checked);
                        updateSelectedCount();
                    });
                }

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', updateSelectedCount);
                });

                if (bulkDeleteBtn) {
                    bulkDeleteBtn.addEventListener('click', function () {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    });
                }

                if (cancelBtn) {
                    cancelBtn.addEventListener('click', function () {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    });
                }

                if (confirmBtn) {
                    confirmBtn.addEventListener('click', function () {
                        bulkDeleteForm.submit();
                    });
                }

                // Close modal on background click
                if (modal) {
                    modal.addEventListener('click', function (e) {
                        if (e.target === modal) {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection