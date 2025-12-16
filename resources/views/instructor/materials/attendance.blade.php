@extends('layouts.instructor')

@section('title', 'Kelola Absensi - ' . $material->judul)

@section('content')
    <div class="p-6 max-w-5xl mx-auto">
        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('instructor.courses.show', $course) }}"
                class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-3">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Kursus
            </a>
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Kelola Absensi</h1>
                    <p class="text-gray-600">{{ $material->judul }}</p>
                </div>
                <div class="flex gap-2">
                    <form action="{{ route('instructor.materials.attendance.mark-all-present', [$course, $material]) }}"
                        method="POST">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium flex items-center gap-2">
                            <i class="fas fa-check-double"></i>
                            <span>Semua Hadir</span>
                        </button>
                    </form>
                    <form action="{{ route('instructor.materials.attendance.generate', [$course, $material]) }}"
                        method="POST">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2">
                            <i class="fas fa-sync"></i>
                            <span>Refresh Peserta</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Session Info --}}
        <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border border-orange-100 p-4 mb-6">
            <div class="flex flex-wrap gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <span
                        class="px-2 py-1 rounded text-xs font-medium {{ $material->session_type === 'online' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                        {{ ucfirst($material->session_type ?? 'offline') }}
                    </span>
                </div>
                @if($material->session_date)
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ $material->formatted_session_date }}</span>
                    </div>
                @endif
                @if($material->session_start_time && $material->session_end_time)
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-clock"></i>
                        <span>{{ \Carbon\Carbon::parse($material->session_start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($material->session_end_time)->format('H:i') }}</span>
                    </div>
                @endif
                @if($material->session_location)
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $material->session_location }}</span>
                    </div>
                @endif
            </div>
            @if($material->session_meeting_link)
                <div class="mt-2">
                    <a href="{{ $material->session_meeting_link }}" target="_blank"
                        class="text-blue-600 hover:underline text-sm">
                        <i class="fas fa-video mr-1"></i> {{ $material->session_meeting_link }}
                    </a>
                </div>
            @endif
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- Attendance Stats --}}
        @php
            $hadirCount = $attendances->where('status', 'hadir')->count();
            $tidakHadirCount = $attendances->where('status', 'tidak_hadir')->count();
            $izinCount = $attendances->whereIn('status', ['izin', 'sakit'])->count();
            $totalCount = $attendances->count();
        @endphp
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-green-50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-green-600">{{ $hadirCount }}</div>
                <div class="text-sm text-green-800">Hadir</div>
            </div>
            <div class="bg-red-50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-red-600">{{ $tidakHadirCount }}</div>
                <div class="text-sm text-red-800">Tidak Hadir</div>
            </div>
            <div class="bg-yellow-50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $izinCount }}</div>
                <div class="text-sm text-yellow-800">Izin/Sakit</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-gray-600">{{ $totalCount }}</div>
                <div class="text-sm text-gray-800">Total</div>
            </div>
        </div>

        {{-- Attendance List --}}
        @if($attendances->isEmpty())
            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Peserta</h3>
                <p class="text-gray-600 mb-4">Klik "Refresh Peserta" untuk menambahkan peserta dari enrollment kursus.</p>
                <form action="{{ route('instructor.materials.attendance.generate', [$course, $material]) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-sync mr-2"></i> Refresh Peserta
                    </button>
                </form>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($attendances as $attendance)
                            <tr id="attendance-{{ $attendance->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                            @if($attendance->user && $attendance->user->avatar)
                                                <img src="{{ $attendance->user->avatar }}" class="w-10 h-10 rounded-full object-cover">
                                            @else
                                                <i class="fas fa-user text-gray-400"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $attendance->user->name ?? 'Unknown' }}</div>
                                            <div class="text-sm text-gray-500">{{ $attendance->user->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form
                                        action="{{ route('instructor.materials.attendance.update', [$course, $material, $attendance]) }}"
                                        method="POST" class="inline-form">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()" class="text-sm rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500
                                                    {{ $attendance->status === 'hadir' ? 'bg-green-50 text-green-700' : '' }}
                                                    {{ $attendance->status === 'tidak_hadir' ? 'bg-red-50 text-red-700' : '' }}
                                                    {{ in_array($attendance->status, ['izin', 'sakit']) ? 'bg-yellow-50 text-yellow-700' : '' }}
                                                    {{ $attendance->status === 'terlambat' ? 'bg-blue-50 text-blue-700' : '' }}">
                                            @foreach($statusOptions as $value => $label)
                                                <option value="{{ $value }}" {{ $attendance->status === $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" value="{{ $attendance->catatan }}" placeholder="Tambah catatan..."
                                        class="text-sm w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                                        onblur="updateNote({{ $attendance->id }}, this.value)">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex gap-2">
                                        <button onclick="quickStatus({{ $attendance->id }}, 'hadir')"
                                            class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="Hadir">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="quickStatus({{ $attendance->id }}, 'tidak_hadir')"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Tidak Hadir">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script>
        function quickStatus(attendanceId, status) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/instructor/courses/{{ $course->id }}/materials/{{ $material->id }}/attendance/' + attendanceId;

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            form.appendChild(statusInput);

            document.body.appendChild(form);
            form.submit();
        }

        function updateNote(attendanceId, note) {
            fetch('/instructor/courses/{{ $course->id }}/materials/{{ $material->id }}/attendance/' + attendanceId, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: document.querySelector('#attendance-' + attendanceId + ' select[name="status"]').value,
                    catatan: note
                })
            }).then(response => {
                if (response.ok) {
                    // Show brief success indicator
                }
            });
        }
    </script>
@endsection