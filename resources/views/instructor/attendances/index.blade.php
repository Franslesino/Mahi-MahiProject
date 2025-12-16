@extends('layouts.instructor')

@section('title', 'Absensi - ' . ($session->judul ?? 'Sesi Kelas'))

@section('content')
    <div class="max-w-6xl mx-auto">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <a href="{{ route('instructor.courses.sessions.index', $course) }}"
                    class="text-gray-500 hover:text-gray-700 mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Sesi
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Absensi Peserta</h1>
                <p class="text-gray-600">{{ $session->judul ?? 'Sesi Kelas' }} - {{ $session->tanggal->format('d M Y') }}
                </p>
            </div>
            <div class="mt-4 md:mt-0 flex gap-2">
                <form action="{{ route('instructor.courses.sessions.attendances.mark-all-present', [$course, $session]) }}"
                    method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium"
                        onclick="return confirm('Tandai semua peserta hadir?')">
                        <i class="fas fa-check-double mr-1"></i> Semua Hadir
                    </button>
                </form>
                <form action="{{ route('instructor.courses.sessions.attendances.regenerate', [$course, $session]) }}"
                    method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
                        <i class="fas fa-sync mr-1"></i> Refresh Peserta
                    </button>
                </form>
            </div>
        </div>

        {{-- Session Info --}}
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="flex flex-wrap gap-6 text-sm">
                <div class="flex items-center gap-2">
                    <span
                        class="px-2 py-1 rounded text-xs font-medium {{ $session->tipe === 'online' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ ucfirst($session->tipe) }}
                    </span>
                </div>
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-clock"></i>
                    <span>{{ $session->waktu_mulai->format('H:i') }} - {{ $session->waktu_selesai->format('H:i') }}</span>
                </div>
                @if($session->lokasi)
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $session->lokasi }}</span>
                    </div>
                @endif
                <div class="flex items-center gap-2">
                    <span
                        class="text-[#005F56] font-semibold">{{ $attendances->where('status', 'hadir')->count() }}/{{ $attendances->count() }}</span>
                    <span class="text-gray-500">Hadir</span>
                </div>
            </div>
        </div>

        {{-- Attendance List --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            @if($attendances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Peserta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Catatan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($attendances as $attendance)
                                <tr class="hover:bg-gray-50" id="attendance-{{ $attendance->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 rounded-full bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white font-bold mr-3">
                                                {{ strtoupper(substr($attendance->user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $attendance->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $attendance->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form
                                            action="{{ route('instructor.courses.sessions.attendances.update', [$course, $session, $attendance]) }}"
                                            method="POST" class="attendance-form">
                                            @csrf
                                            @method('PUT')
                                            <select name="status"
                                                class="text-sm rounded-lg border-gray-300 focus:ring-[#005F56] focus:border-[#005F56]"
                                                onchange="this.form.submit()">
                                                @foreach($statusOptions as $value => $label)
                                                    <option value="{{ $value }}" {{ $attendance->status === $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="text" class="text-sm border-gray-300 rounded-lg w-full max-w-xs"
                                            placeholder="Tambah catatan..." value="{{ $attendance->catatan }}"
                                            onchange="updateNote({{ $attendance->id }}, this.value)">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($attendance->check_in_time)
                                            {{ $attendance->check_in_time->format('H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button type="button" class="text-green-600 hover:text-green-800 mr-2"
                                            onclick="quickStatus({{ $attendance->id }}, 'hadir')" title="Hadir">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                        <button type="button" class="text-red-600 hover:text-red-800"
                                            onclick="quickStatus({{ $attendance->id }}, 'tidak_hadir')" title="Tidak Hadir">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Belum Ada Peserta</h3>
                    <p class="text-gray-600">Peserta yang terdaftar di kursus ini akan muncul di sini.</p>
                </div>
            @endif
        </div>

        {{-- Summary --}}
        @if($attendances->count() > 0)
            <div class="mt-6 grid grid-cols-2 md:grid-cols-5 gap-4">
                @php
                    $summary = [
                        'hadir' => ['label' => 'Hadir', 'color' => 'green', 'icon' => 'check-circle'],
                        'tidak_hadir' => ['label' => 'Tidak Hadir', 'color' => 'red', 'icon' => 'times-circle'],
                        'izin' => ['label' => 'Izin', 'color' => 'yellow', 'icon' => 'file-alt'],
                        'sakit' => ['label' => 'Sakit', 'color' => 'orange', 'icon' => 'medkit'],
                        'terlambat' => ['label' => 'Terlambat', 'color' => 'blue', 'icon' => 'clock'],
                    ];
                @endphp
                @foreach($summary as $status => $info)
                    <div class="bg-white rounded-xl p-4 shadow-sm text-center">
                        <div class="text-2xl font-bold text-{{ $info['color'] }}-600">
                            {{ $attendances->where('status', $status)->count() }}
                        </div>
                        <div class="text-sm text-gray-600">{{ $info['label'] }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        function quickStatus(attendanceId, status) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/instructor/courses/{{ $course->id }}/sessions/{{ $session->id }}/attendances/' + attendanceId;

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
            fetch('/instructor/courses/{{ $course->id }}/sessions/{{ $session->id }}/attendances/' + attendanceId, {
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