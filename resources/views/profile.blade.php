@extends('layouts.app')

@section('title', 'Edit Profile - Pelayanan TIK PNJ')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Edit Profile</h1>
    
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form>
            <div class="space-y-6">
                <!-- Avatar -->
                <div class="flex items-center gap-6">
                    <div class="w-24 h-24 bg-gradient-to-br from-teal-500 to-teal-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-4xl font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <button type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                            Ubah Foto
                        </button>
                    </div>
                </div>

                <!-- Nama -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" value="{{ Auth::user()->name }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none">
                </div>

                <!-- NIM -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">NIM</label>
                    <input type="text" value="{{ Auth::user()->nim ?? '-' }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" value="{{ Auth::user()->email }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                    <input type="tel" value="{{ Auth::user()->phone ?? '-' }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none">
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition font-medium">
                        Simpan Perubahan
                    </button>
                    <button type="button" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                        Batal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection