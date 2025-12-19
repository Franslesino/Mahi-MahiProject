@extends('layouts.instructor')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <a href="{{ route('instructor.courses.show', $course->id) }}" class="text-blue-600 hover:text-blue-700 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Detail Kursus</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Tambah Materi</h2>
        <p class="text-gray-600 mb-6">Kursus: {{ $course->judul }}</p>

        <form id="materialCreateForm"
              data-redirect="{{ route('instructor.courses.show', $course->id) }}"
              action="{{ route('instructor.courses.materials.store', $course->id) }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Materi *</label>
                    <input type="text" name="judul" value="{{ old('judul') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('judul') border-red-500 @enderror">
                    @error('judul')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror   
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Materi *</label>
                    <select name="type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror">
                        <option value="video" {{ old('type') === 'video' ? 'selected' : '' }}>Video</option>
                        <option value="pdf" {{ old('type') === 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="text" {{ old('type') === 'text' ? 'selected' : '' }}>Text</option>
                        <option value="quiz" {{ old('type') === 'quiz' ? 'selected' : '' }}>Quiz</option>
                    </select>
                    @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">File (Video/PDF)</label>
                    <input id="fileInput" type="file" name="file" accept="video/*,application/pdf" class="hidden" />
                    <div id="dropzone" class="border-2 border-dashed border-gray-300 rounded-xl px-6 py-8 bg-gray-50 hover:border-blue-400 hover:bg-blue-50 transition cursor-pointer">
                        <div class="flex flex-col items-center justify-center gap-3 text-center">
                            <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                <i class="fas fa-cloud-upload-alt text-2xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Tarik & letakkan file di sini</p>
                                <p class="text-sm text-gray-500">Klik untuk pilih file Video (MP4, MOV, AVI) atau PDF. Maks 100MB.</p>
                            </div>
                            <div id="fileName" class="text-sm text-blue-600 font-medium hidden"></div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Gunakan drag & drop agar instruktur bisa melihat progres upload.</p>
                    @error('file')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <div id="uploadProgress" class="hidden mt-3">
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Uploading...</span>
                            <span id="uploadProgressText">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div id="uploadProgressBar" class="h-2 bg-blue-600 rounded-full transition-all duration-200" style="width: 0%;"></div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Durasi (menit)</label>
                    <input type="number" name="duration" value="{{ old('duration') }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                    <input type="number" name="order" value="{{ old('order') }}" min="0"
                           placeholder="Otomatis jika kosong"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_preview" value="1" {{ old('is_preview') ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Bisa diakses sebagai preview</span>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konten Text (jika tipe Text)</label>
                    <textarea name="content" rows="6"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('content') }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-4 mt-8">
                <button type="submit"
                        id="materialSubmitBtn"
                        class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Materi
                </button>
                <a href="{{ route('instructor.courses.show', $course->id) }}"
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Success Notification Modal -->
<div id="uploadSuccessModal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 relative">
        <div class="flex flex-col items-center text-center space-y-4">
            <!-- Icon Success -->
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                <i class="fas fa-check text-3xl"></i>
            </div>
            
            <!-- Title & Message -->
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Materi Berhasil Diupload!</h3>
                <p class="text-sm text-gray-600">Materi telah ditambahkan ke kursus.</p>
            </div>
            
            <!-- Button -->
            <button id="successOkBtn"
                    class="w-full px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                OK
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('materialCreateForm');
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const fileName = document.getElementById('fileName');
    const progressWrap = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('uploadProgressBar');
    const progressText = document.getElementById('uploadProgressText');
    const submitBtn = document.getElementById('materialSubmitBtn');
    const successModal = document.getElementById('uploadSuccessModal');
    const successOkBtn = document.getElementById('successOkBtn');
    const fallbackRedirect = form?.dataset?.redirect;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!form || !dropzone || !fileInput) return;

    const resetProgress = () => {
        if (progressWrap) progressWrap.classList.add('hidden');
        if (progressBar) progressBar.style.width = '0%';
        if (progressText) progressText.textContent = '0%';
    };

    const showFileName = (file) => {
        if (!fileName) return;
        fileName.textContent = file.name;
        fileName.classList.remove('hidden');
    };

    dropzone.addEventListener('click', () => fileInput.click());

    ['dragenter', 'dragover'].forEach(evt => {
        dropzone.addEventListener(evt, e => {
            e.preventDefault();
            dropzone.classList.add('border-blue-500', 'bg-blue-50');
        });
    });

    ['dragleave', 'drop'].forEach(evt => {
        dropzone.addEventListener(evt, e => {
            e.preventDefault();
            dropzone.classList.remove('border-blue-500', 'bg-blue-50');
        });
    });

    dropzone.addEventListener('drop', (e) => {
        const file = e.dataTransfer?.files?.[0];
        if (file) {
            fileInput.files = e.dataTransfer.files;
            showFileName(file);
        }
    });

    fileInput.addEventListener('change', (e) => {
        const file = e.target.files?.[0];
        if (file) {
            showFileName(file);
        } else {
            fileName?.classList.add('hidden');
        }
    });

    form.addEventListener('submit', (e) => {
        const hasFile = fileInput.files && fileInput.files.length > 0;
        if (!hasFile) {
            resetProgress();
            return;
        }

        e.preventDefault();
        resetProgress();
        progressWrap?.classList.remove('hidden');
        submitBtn?.setAttribute('disabled', 'disabled');
        submitBtn?.classList.add('opacity-70', 'cursor-not-allowed');

        const xhr = new XMLHttpRequest();
        xhr.open(form.method, form.action);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        if (csrf) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
        }

        xhr.upload.onprogress = (event) => {
            if (!event.lengthComputable) return;
            const percent = Math.round((event.loaded / event.total) * 100);
            if (progressBar) progressBar.style.width = percent + '%';
            if (progressText) progressText.textContent = percent + '%';
        };

        const handleSuccess = () => {
            submitBtn?.removeAttribute('disabled');
            submitBtn?.classList.remove('opacity-70', 'cursor-not-allowed');
            if (progressBar) progressBar.style.width = '100%';
            if (progressText) progressText.textContent = '100%';
            
            // Tampilkan modal
            if (successModal) {
                successModal.classList.remove('hidden');
                successModal.classList.add('flex');
            }
            
            // Simpan redirect URL
            const redirectUrl =
                xhr.getResponseHeader('Location') ||
                fallbackRedirect ||
                form.getAttribute('action');
            
            // Set redirect URL ke tombol OK
            if (redirectUrl && successOkBtn) {
                successOkBtn.setAttribute('data-redirect', redirectUrl);
            }
        };

        xhr.onload = () => {
            const status = xhr.status || 0;
            console.log('XHR Status:', status);
            console.log('XHR Response:', xhr.responseText);
            
            if (status >= 200 && status < 400) {
                console.log('Success - showing modal');
                handleSuccess();
            } else {
                alert('Upload gagal. Silakan coba lagi atau cek ukuran file.');
                submitBtn?.removeAttribute('disabled');
                submitBtn?.classList.remove('opacity-70', 'cursor-not-allowed');
                resetProgress();
            }
        };

        xhr.onerror = () => {
            alert('Terjadi kesalahan jaringan. Coba ulangi.');
            submitBtn?.removeAttribute('disabled');
            submitBtn?.classList.remove('opacity-70', 'cursor-not-allowed');
            resetProgress();
        };

        const formData = new FormData(form);
        xhr.send(formData);
    });

    // Event listener untuk tombol OK
    successOkBtn?.addEventListener('click', () => {
        const redirectUrl = successOkBtn.getAttribute('data-redirect');
        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    });
});
</script>
@endpush