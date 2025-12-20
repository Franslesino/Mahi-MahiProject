@extends('layouts.admin')

@section('content')
    <div class="p-8">
        <div class="mb-6">
            <a href="{{ route('admin.users.index') }}"
                class="text-emerald-600 hover:text-emerald-700 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Daftar Pengguna</span>
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Pengguna Baru</h2>

            <form id="adminUserCreateForm" action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                        <div class="relative">
                            <input type="password" name="password" id="adminPassword" required
                                class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('password') border-red-500 @enderror"
                                placeholder="Min. 8 karakter, huruf besar, kecil, angka, simbol">
                            <button type="button" id="togglePassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Min. 8 karakter, huruf besar, kecil, angka, simbol</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password *</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                        <select name="role" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('role') border-red-500 @enderror">
                            <option value="">Pilih Role</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="instructor" {{ old('role') === 'instructor' ? 'selected' : '' }}>Instruktur
                            </option>
                            <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                        @error('role')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email Verification Options --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Opsi Verifikasi Email</label>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="auto_verify" value="1"
                                    class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                    id="autoVerifyCheckbox">
                                <span class="text-sm text-gray-700">
                                    <strong>Verifikasi Otomatis</strong> - Email langsung terverifikasi (user bisa login
                                    tanpa verifikasi)
                                </span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="send_verification" value="1" checked
                                    class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                    id="sendVerificationCheckbox">
                                <span class="text-sm text-gray-700">
                                    <strong>Kirim Email Verifikasi</strong> - User harus verifikasi email sebelum login
                                </span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Jika kedua opsi tidak dipilih, user tidak akan bisa login sampai diverifikasi manual.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4 mt-8">
                    <button type="submit" id="adminUserCreateSubmit"
                        class="px-6 py-3 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 font-medium flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                        <i class="fas fa-save"></i>
                        <span id="adminUserCreateText">Simpan Pengguna</span>
                        <svg id="adminUserCreateSpinner" class="hidden w-4 h-4 animate-spin" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('adminUserCreateForm');
            const submitBtn = document.getElementById('adminUserCreateSubmit');
            const spinner = document.getElementById('adminUserCreateSpinner');
            const text = document.getElementById('adminUserCreateText');
            if (form && submitBtn && spinner && text) {
                form.addEventListener('submit', () => {
                    submitBtn.disabled = true;
                    spinner.classList.remove('hidden');
                    text.textContent = 'Menyimpan...';
                });
            }

            // Password toggle visibility
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('adminPassword');
            const eyeIcon = document.getElementById('eyeIcon');
            if (togglePassword && passwordInput && eyeIcon) {
                togglePassword.addEventListener('click', () => {
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        eyeIcon.classList.remove('fa-eye');
                        eyeIcon.classList.add('fa-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        eyeIcon.classList.remove('fa-eye-slash');
                        eyeIcon.classList.add('fa-eye');
                    }
                });
            }
        });
    </script>
@endpush