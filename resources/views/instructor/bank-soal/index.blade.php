@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.instructor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        Bank Soal Sederhana
                        <span class="px-3 py-1 bg-green-600 text-white text-sm rounded-full ml-2">New</span>
                    </h1>
                    <p class="text-gray-600 mt-2">Kelola soal secara cepat dan langsung import ke Final Quiz</p>
                </div>
                <a href="{{ route('instructor.bank-soal.create') }}" 
                   class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Tambah Soal
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Soal</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $bankSoal->total() }}</p>
                    </div>
                    <i class="fas fa-question-circle text-blue-500 text-3xl"></i>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Multiple Choice</p>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ \App\Models\BankSoal::where('tipe_soal', 'multiple_choice')->count() }}
                        </p>
                    </div>
                    <i class="fas fa-list-ul text-blue-500 text-3xl"></i>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Essay</p>
                        <p class="text-2xl font-bold text-purple-600">
                            {{ \App\Models\BankSoal::where('tipe_soal', 'essay')->count() }}
                        </p>
                    </div>
                    <i class="fas fa-pencil-alt text-purple-500 text-3xl"></i>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">True/False</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ \App\Models\BankSoal::where('tipe_soal', 'true_false')->count() }}
                        </p>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Questions List -->
        <div class="space-y-4">
            @forelse($bankSoal as $soal)
                <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <!-- Content -->
                        <div class="flex-1">
                            <!-- Question Header -->
                            <div class="flex items-center gap-3 mb-3">
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
                                
                                @if($soal->kategori)
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">
                                        <i class="fas fa-tag mr-1"></i>{{ $soal->kategori }}
                                    </span>
                                @endif

                                <span class="text-sm text-gray-500">
                                    Dibuat {{ $soal->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <!-- Question Text -->
                            <p class="text-gray-800 mb-3 font-medium">{{ $soal->pertanyaan }}</p>

                            <!-- Options Preview -->
                            @if(in_array($soal->tipe_soal, ['multiple_choice', 'true_false']) && $soal->opsiJawaban->count() > 0)
                                <div class="space-y-2 pl-4 border-l-2 border-gray-200">
                                    @foreach($soal->opsiJawaban as $opsi)
                                        <div class="flex items-center gap-2">
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
                            @endif

                            <!-- Usage Info -->
                            @if($soal->relasiQuiz && $soal->relasiQuiz->count() > 0)
                                <div class="mt-3 text-sm text-gray-600">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Digunakan dalam {{ $soal->relasiQuiz->count() }} quiz
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 ml-4">
                            <a href="{{ route('instructor.bank-soal.edit', $soal->id) }}" 
                               class="px-3 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('instructor.bank-soal.destroy', $soal->id) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Yakin ingin menghapus soal ini? Soal yang sedang digunakan tidak dapat dihapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                    <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
                    <p class="text-gray-600 font-semibold">Belum ada soal di bank soal</p>
                    <p class="text-gray-500 mt-2">Mulai buat soal untuk koleksi yang dapat digunakan di berbagai quiz.</p>
                    <a href="{{ route('instructor.bank-soal.create') }}" 
                       class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>Tambah Soal Pertama
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($bankSoal->hasPages())
            <div class="mt-6">
                {{ $bankSoal->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
