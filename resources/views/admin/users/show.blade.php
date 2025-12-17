@extends('layouts.admin')

@section('content')
<div class="p-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Detail Pengguna</h2>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 flex items-center gap-2">
                <i class="fas fa-edit"></i>
                <span>Edit</span>
            </a>
            @if($user->id !== auth()->id())
            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline"
                  data-confirm-title="Hapus pengguna?"
                  data-confirm="Yakin ingin menghapus pengguna ini?">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center gap-2">
                    <i class="fas fa-trash"></i>
                    <span>Hapus</span>
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profile Card --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="text-center mb-6">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                    @else
                        <div class="w-32 h-32 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-white font-bold text-4xl">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                    @endif
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-1">
                        @if($user->first_name && $user->last_name)
                            {{ $user->first_name }} {{ $user->last_name }}
                        @else
                            {{ $user->name }}
                        @endif
                    </h3>
                    
                        @if($user->role === 'admin')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-700">Admin</span>
                        @elseif($user->role === 'instructor')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-700">Instruktur</span>
                        @else
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-700">
                                {{ $user->role === 'student' ? 'Student' : ucfirst($user->role) }}
                            </span>
                        @endif
                </div>

                <div class="space-y-4 border-t pt-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-envelope text-gray-400 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 uppercase">Email</p>
                            <p class="text-sm text-gray-800">{{ $user->email }}</p>
                        </div>
                    </div>

                    @if($user->phone)
                    <div class="flex items-start gap-3">
                        <i class="fas fa-phone text-gray-400 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 uppercase">Telepon</p>
                            <p class="text-sm text-gray-800">{{ $user->phone }}</p>
                        </div>
                    </div>
                    @endif

                    @if($user->dob)
                    <div class="flex items-start gap-3">
                        <i class="fas fa-calendar text-gray-400 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 uppercase">Tanggal Lahir</p>
                            <p class="text-sm text-gray-800">{{ \Carbon\Carbon::parse($user->dob)->format('d M Y') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($user->gender)
                    <div class="flex items-start gap-3">
                        <i class="fas fa-venus-mars text-gray-400 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 uppercase">Gender</p>
                            <p class="text-sm text-gray-800">{{ ucfirst($user->gender) }}</p>
                        </div>
                    </div>
                    @endif

                    @if($user->nim)
                    <div class="flex items-start gap-3">
                        <i class="fas fa-id-card text-gray-400 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 uppercase">NIM</p>
                            <p class="text-sm text-gray-800">{{ $user->nim }}</p>
                        </div>
                    </div>
                    @endif

                    @if($user->profesi)
                    <div class="flex items-start gap-3">
                        <i class="fas fa-briefcase text-gray-400 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 uppercase">Profesi</p>
                            <p class="text-sm text-gray-800">{{ $user->profesi }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-start gap-3">
                        <i class="fas fa-clock text-gray-400 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 uppercase">Bergabung</p>
                            <p class="text-sm text-gray-800">{{ $user->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Area --}}
        <div class="lg:col-span-2">
            @if($user->role === 'instructor')
                {{-- Instructor Statistics --}}
                @if($stats)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Total Kursus</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_courses'] }}</p>
                            </div>
                            <i class="fas fa-book text-blue-500 text-2xl"></i>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Total Peserta</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_students'] }}</p>
                            </div>
                            <i class="fas fa-users text-emerald-500 text-2xl"></i>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Kursus Aktif</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['active_courses'] }}</p>
                            </div>
                            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Total Pendapatan</p>
                                <p class="text-lg font-bold text-gray-800">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                            </div>
                            <i class="fas fa-money-bill-wave text-yellow-500 text-2xl"></i>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Courses List --}}
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800">Kursus yang Diampu</h3>
                    </div>
                    
                    @if($user->instructorCourses->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($user->instructorCourses as $course)
                        <div class="p-6 hover:bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="text-lg font-semibold text-gray-800">{{ $course->judul }}</h4>
                                        @if($course->status_diterbitkan)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Diterbitkan</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Draft</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($course->deskripsi, 150) }}</p>
                                    
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <span><i class="fas fa-tag mr-1"></i>{{ $course->kategori }}</span>
                                        <span><i class="fas fa-video mr-1"></i>{{ $course->videos ?? 0 }} video</span>
                                        <span><i class="fas fa-users mr-1"></i>{{ $course->enrollments_count }} peserta</span>
                                        <span><i class="fas fa-money-bill-wave mr-1"></i>Rp {{ number_format($course->harga, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('admin.courses.show', $course->id) }}" class="ml-4 text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-12">
                        <i class="fas fa-book text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Belum ada kursus yang diampu</p>
                    </div>
                    @endif
                </div>

            @elseif($user->role === 'student' || $user->role === 'user')
                {{-- Student Statistics --}}
                @if($stats)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Total Kursus</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_enrolled'] }}</p>
                            </div>
                            <i class="fas fa-book text-blue-500 text-2xl"></i>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Selesai</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['completed_courses'] }}</p>
                            </div>
                            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Sedang Belajar</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['in_progress'] }}</p>
                            </div>
                            <i class="fas fa-play-circle text-emerald-500 text-2xl"></i>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Total Pengeluaran</p>
                                <p class="text-lg font-bold text-gray-800">Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}</p>
                            </div>
                            <i class="fas fa-money-bill-wave text-yellow-500 text-2xl"></i>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Enrolled Courses --}}
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800">Kursus yang Diikuti</h3>
                    </div>
                    
                    @if($user->enrollments->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($user->enrollments as $enrollment)
                        <div class="p-6 hover:bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-2">{{ $enrollment->kursus->judul }}</h4>
                                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-2">
                                        <span><i class="fas fa-user mr-1"></i>{{ $enrollment->kursus->instructor->name ?? 'Tidak ada instruktur' }}</span>
                                        <span><i class="fas fa-calendar mr-1"></i>Terdaftar: {{ $enrollment->created_at->format('d M Y') }}</span>
                                    </div>
                                    @php
                                        $status = $enrollment->status_pendaftaran ?? $enrollment->status ?? '-';
                                    @endphp
                                    @if(in_array($status, ['completed', 'selesai']))
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Selesai</span>
                                    @elseif(in_array($status, ['active', 'paid', 'approved', 'enrolled']))
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Sedang Belajar</span>
                                    @elseif($status === '-' || $status === null)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Tidak diketahui</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">{{ ucfirst($status) }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('admin.courses.show', $enrollment->kursus->id) }}" class="ml-4 text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-12">
                        <i class="fas fa-book text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Belum mengikuti kursus apapun</p>
                    </div>
                    @endif
                </div>

                {{-- Recent Transactions --}}
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800">Transaksi Terakhir</h3>
                    </div>
                    
                    @if($user->transactions->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($user->transactions as $transaction)
                        <div class="p-6 hover:bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="text-base font-semibold text-gray-800">#{{ $transaction->transaction_code ?? $transaction->id }}</h4>
                                        @if($transaction->status === 'paid')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Berhasil</span>
                                        @elseif($transaction->status === 'pending')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                                        @elseif($transaction->status === 'expired')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Expired</span>
                                        @elseif($transaction->status === 'cancelled')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Dibatalkan</span>
                                        @elseif($transaction->status === 'refunded')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">Refund</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">{{ ucfirst($transaction->status) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-2">
                                        <span><i class="fas fa-calendar mr-1"></i>{{ $transaction->created_at->format('d M Y H:i') }}</span>
                                        <span><i class="fas fa-money-bill-wave mr-1"></i>Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</span>
                                        @if($transaction->payment_method)
                                            <span><i class="fas fa-credit-card mr-1"></i>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('admin.transactions.show', $transaction->id) }}" class="ml-4 text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-12">
                        <i class="fas fa-receipt text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Belum ada transaksi</p>
                    </div>
                    @endif
                </div>

            @else
                {{-- Admin or other roles --}}
                <div class="bg-white rounded-lg shadow-sm p-12">
                    <div class="text-center">
                        <i class="fas fa-user-shield text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Administrator</h3>
                        <p class="text-gray-500">Akun ini memiliki akses penuh ke sistem</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
