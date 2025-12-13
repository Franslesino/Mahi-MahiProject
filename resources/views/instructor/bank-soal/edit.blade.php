@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.instructor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Edit Soal Bank Soal</h1>
                    <p class="text-gray-600 mt-2">Update informasi soal</p>
                </div>
                <a href="{{ route('instructor.bank-soal.index') }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <p class="font-semibold">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Warning if used in quiz -->
        @if($soal->relasiQuiz && $soal->relasiQuiz->count() > 0)
            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
                    <div class="text-sm text-yellow-800">
                        <p class="font-semibold mb-1">Perhatian:</p>
                        <p>Soal ini sedang digunakan dalam {{ $soal->relasiQuiz->count() }} quiz. Perubahan hanya untuk kategori dan pertanyaan, tidak dapat mengubah tipe soal atau opsi jawaban.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('instructor.bank-soal.update', $soal->id) }}" method="POST" class="bg-white p-6 rounded-lg shadow">
            @csrf
            @method('PUT')

            <!-- Kategori -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-tag mr-2"></i>Kategori
                </label>
                <input type="text" 
                       name="kategori" 
                       value="{{ old('kategori', $soal->kategori) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Contoh: matematika, pemrograman, final_quiz">
            </div>

            <!-- Tipe Soal (Read-only) -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-list mr-2"></i>Tipe Soal
                </label>
                <div class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg">
                    @if($soal->tipe_soal === 'multiple_choice')
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded-full">
                            <i class="fas fa-list-ul mr-1"></i>Multiple Choice
                        </span>
                    @elseif($soal->tipe_soal === 'essay')
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-sm rounded-full">
                            <i class="fas fa-pencil-alt mr-1"></i>Essay
                        </span>
                    @else
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full">
                            <i class="fas fa-check-circle mr-1"></i>True/False
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1">Tipe soal tidak dapat diubah setelah dibuat</p>
            </div>

            <!-- Pertanyaan -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-question-circle mr-2"></i>Pertanyaan <span class="text-red-500">*</span>
                </label>
                <textarea name="pertanyaan" 
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Masukkan pertanyaan..."
                          required>{{ old('pertanyaan', $soal->pertanyaan) }}</textarea>
            </div>

            <!-- Display existing options (Read-only) -->
            @if(in_array($soal->tipe_soal, ['multiple_choice', 'true_false']) && $soal->opsiJawaban->count() > 0)
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-list-ul mr-2"></i>Opsi Jawaban
                    </label>
                    <div class="space-y-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        @foreach($soal->opsiJawaban as $opsi)
                            <div class="flex items-center gap-2 p-2 bg-white rounded border {{ $opsi->is_benar ? 'border-green-300' : 'border-gray-200' }}">
                                @if($opsi->is_benar)
                                    <i class="fas fa-check-circle text-green-600"></i>
                                    <span class="text-green-700 font-medium">{{ $opsi->teks_opsi }}</span>
                                @else
                                    <i class="far fa-circle text-gray-400"></i>
                                    <span class="text-gray-600">{{ $opsi->teks_opsi }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Opsi jawaban tidak dapat diubah. Jika perlu mengubah opsi, buat soal baru.</p>
                </div>
            @endif

            <!-- Submit -->
            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Update Soal
                </button>
                <a href="{{ route('instructor.bank-soal.index') }}" 
                   class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
