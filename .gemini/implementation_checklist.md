# Checklist Implementasi Perbaikan Website

## Status Implementasi
- ✅ = Sudah selesai
- 🔄 = Sedang dikerjakan
- ⏳ = Belum dimulai

## Daftar Tugas

### 1. ✅ Instruktur wajib dipilih saat membuat kursus
- File: resources/views/admin/courses/create.blade.php (sudah required)
- File: resources/views/instructor/courses/create.blade.php (perlu dicek)

### 3. ⏳ Hapus tulisan "assignment &" di halaman admin
- File: resources/views/admin/courses/show.blade.php atau course-detail.blade.php
- Action: Cari text "assignment &" dan hapus, sisakan "Quiz"

### 4. ⏳ Fitur hapus semua modul dan hapus semua materi
- Controller: Admin\CourseController (sudah ada destroyAllModules dan destroyAllMaterials)
- Perlu tambahkan button di view

### 5. ⏳ Fix urutan modul dan materi
- Controller: perlu cek query untuk ordering
- Migration: perlu cek kolom order

### 6. ⏳ Hilangkan pilihan sesi kelas untuk tipe online
- File: resources/views/admin/courses/create.blade.php dan edit.blade.php
- Action: Tambah JavaScript untuk hide/show conditional

### 7. ⏳ Hapus fitur durasi di soal quiz biasa
- File: perlu cari form create quiz (bukan final quiz)

### 8. ⏳ Tambah fitur search di bank soal
- File: perlu cari modal/halaman import dari bank soal

### 9. ⏳ Tambah waktu pengerjaan di quiz biasa
- Controller: perlu update logic quiz biasa

### 10. ⏳ Modal informasi sebelum mulai quiz
- File: resources/views/student/materials/quiz.blade.php (atau sejenisnya)
- Action: Tambah modal dengan info minimum nilai dan waktu

### 11. ⏳ Modal saat "Coba Lagi" quiz
- File: resources/views/student/quiz/result.blade.php (atau sejenisnya)
- Action: Tambah modal sama seperti #10

### 12. ⏳ Quiz biasa: hapus lulus/tidak lulus, hanya show nilai dan review
- Controller: update logic quiz biasa submission
- View: update tampilan hasil quiz

### 13. ⏳ Hapus nilai minimum lulus di quiz biasa
- File: form create quiz biasa

### 14. ⏳ Hapus preview di verifikasi sertifikat
- File: resources/views/certificate/verify.blade.php
- Action: Hapus button/link preview, sisakan download saja

### 15. ⏳ Ganti "mahi2" menjadi "UpGreenius"
- Action: Search semua file untuk "mahi"

### 16. ⏳ Kurangi angka informasi berakhirnya kursus di homepage
- File: resources/views/home/index.blade.php

### 17. ⏳ Fix posisi nama instruktur di daftar instruktur
- File: perlu cari halaman daftar instruktur

### 18. ⏳ Foto instruktur ikuti avatar_path bukan avatar
- File: perlu cari blade yang menampilkan foto instruktur
- Action: Ganti dari $instructor->avatar ke $instructor->avatar_path

### 19. ⏳ Fitur timer di form final quiz  
- File: resources/views/admin/courses/final-quiz-settings.blade.php atau create-final-quiz.blade.php

### 20. ⏳ Form nilai minimum final quiz: gunakan desimal
- File: resources/views/admin/courses/final-quiz-settings.blade.php
- Action: Ganti input type="number" step="0.01" atau step="any"

### 23. ⏳ Alert saat final quiz dinonaktifkan saat peserta mengerjakan
- Controller: Student\FinalQuizController
- Action: Tambah check status final quiz

### 24. ⏳ Lanjutkan percobaan final quiz yang sama setelah back
- Controller: Student\FinalQuizController
- Action: Simpan state ke session/local storage

### 25. ⏳ Button "Lanjut Belajar" di homepage jika sudah beli
- File: resources/views/home/index.blade.php
- Action: Cek enrollment, ganti tampilan harga jadi button

### 26. ⏳ Hilangkan harga di detail produk jika sudah beli
- File: resources/views/courses/show.blade.php (detail kursus peserta)

### 27. ⏳ Import soal: tampilkan nama bank soal dan bisa pilih multiple
- File: perlu cari modal import dari bank soal
- Action: Tambah checkbox untuk multiple select

### 28. ⏳ Bug materi teks terduplikat
- Controller: perlu cari function storeMaterial untuk tipe text
- Action: Fix bug duplikasi

### 29. ⏳ Form konten teks tidak full
- File: perlu cari form create/edit material text
- Action: Fix CSS width/height

### 30. ⏳ Ubah button "Lanjut ke materi berikutnya" jadi "Kembali ke materi kursus"
- File: resources/views/student/final-quiz/result.blade.php (atau sejenisnya)
- Action: Ganti text dan redirect URL
