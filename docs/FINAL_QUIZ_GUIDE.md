# Final Quiz Feature Documentation

## Overview
Fitur Final Quiz memungkinkan admin dan instruktur untuk mewajibkan peserta menyelesaikan quiz akhir sebelum mendapatkan sertifikat kursus.

## Fitur Utama

### 1. Untuk Admin & Instruktur

#### Mengatur Final Quiz
- **Route**: `/instructor/courses/{kursus}/final-quiz`
- **Fitur**:
  - Aktifkan/nonaktifkan final quiz untuk kursus
  - Pilih quiz mana yang dijadikan final quiz
  - Atur nilai minimum kelulusan (0-100%)
  - Atur jumlah maksimal percobaan (1-10 kali)

#### Melihat Statistik
- **Route**: `/instructor/courses/{kursus}/final-quiz/statistics`
- **Informasi yang ditampilkan**:
  - Total peserta yang mengerjakan
  - Jumlah peserta yang lulus
  - Jumlah peserta yang belum lulus
  - Rata-rata nilai
  - Detail per peserta (attempts, nilai terbaik, status)

### 2. Untuk Peserta

#### Mengerjakan Final Quiz
- **Route**: `/courses/{kursus}/final-quiz`
- **Fitur**:
  - Melihat informasi final quiz (passing score, max attempts)
  - Memulai attempt baru
  - Mengerjakan quiz dengan timer (jika ada)
  - Melihat hasil setelah selesai
  - Review jawaban benar/salah

#### Batasan
- Peserta hanya bisa mengerjakan sesuai jumlah maksimal percobaan
- Jika sudah lulus, tidak perlu mengerjakan lagi
- Jika belum lulus dan attempts habis, tidak bisa lanjut
- Sertifikat hanya diberikan jika sudah lulus final quiz

## Database Schema

### Tabel: `quiz_attempts`
```sql
- id: primary key
- user_id: foreign key ke users
- quiz_id: foreign key ke quiz
- kursus_id: foreign key ke kursus
- attempt_number: nomor percobaan (1, 2, 3, dst)
- score: nilai yang didapat (0-100)
- is_passed: apakah lulus (boolean)
- started_at: waktu mulai
- completed_at: waktu selesai
- created_at, updated_at
```

### Tabel: `kursus` (kolom tambahan)
```sql
- final_quiz_id: foreign key ke quiz (nullable)
- min_passing_score: nilai minimum untuk lulus (default 70)
- max_quiz_attempts: jumlah maksimal percobaan (default 3)
- require_final_quiz: apakah wajib final quiz (boolean, default false)
```

### Tabel: `jawaban_peserta` (kolom tambahan)
```sql
- quiz_attempt_id: foreign key ke quiz_attempts (nullable)
- attempt_number: nomor percobaan
```

## Models

### QuizAttempt
- **Relasi**:
  - `belongsTo(User)`
  - `belongsTo(Quiz)`
  - `belongsTo(Kursus)`
  - `hasMany(JawabanPeserta)`
- **Scopes**:
  - `latestAttempt($userId, $quizId)`: mendapatkan attempt terakhir
  - `userAttempts($userId, $quizId)`: semua attempts user

### Kursus (update)
- **Relasi baru**:
  - `belongsTo(Quiz, 'final_quiz_id')`: final quiz untuk kursus

### Quiz (update)
- **Relasi baru**:
  - `hasMany(QuizAttempt)`: tracking attempts
  - `hasMany(Kursus, 'final_quiz_id')`: kursus yang menggunakan quiz ini sebagai final
- **Methods**:
  - `isFinalQuiz()`: cek apakah quiz adalah final quiz

## Controllers

### Instructor\FinalQuizController
- `edit($kursusId)`: halaman setting final quiz
- `update(Request, $kursusId)`: simpan pengaturan
- `statistics($kursusId)`: halaman statistik

### Student\FinalQuizController
- `show($kursusId)`: halaman overview final quiz
- `start(Request, $kursusId)`: mulai attempt baru
- `take($kursusId, $attemptId)`: halaman mengerjakan quiz
- `submit(Request, $kursusId, $attemptId)`: submit jawaban
- `result($kursusId, $attemptId)`: halaman hasil

## Routes

### Instruktur
```php
Route::prefix('instructor')->group(function () {
    Route::get('/courses/{kursus}/final-quiz', 'edit');
    Route::put('/courses/{kursus}/final-quiz', 'update');
    Route::get('/courses/{kursus}/final-quiz/statistics', 'statistics');
});
```

### Student
```php
Route::get('/courses/{kursus}/final-quiz', 'show');
Route::post('/courses/{kursus}/final-quiz/start', 'start');
Route::get('/courses/{kursus}/final-quiz/{attempt}', 'take');
Route::post('/courses/{kursus}/final-quiz/{attempt}/submit', 'submit');
Route::get('/courses/{kursus}/final-quiz/{attempt}/result', 'result');
```

## Views

### Instruktur
- `resources/views/instructor/courses/final-quiz-settings.blade.php`
- `resources/views/instructor/courses/final-quiz-statistics.blade.php`

### Student
- `resources/views/student/courses/final-quiz.blade.php`
- `resources/views/student/courses/take-final-quiz.blade.php`
- `resources/views/student/courses/final-quiz-result.blade.php`

## Logic Sertifikat

Sertifikat hanya akan di-generate jika:
1. Semua materi sudah selesai
2. Jika kursus memiliki final quiz yang wajib (`require_final_quiz = true`):
   - User harus memiliki minimal 1 attempt yang `is_passed = true`

Update dilakukan di:
- `StudentController::generateCertificateIfNeeded()`

## Testing Checklist

### Admin/Instruktur
- [ ] Dapat mengakses halaman setting final quiz
- [ ] Dapat mengaktifkan/nonaktifkan final quiz
- [ ] Dapat memilih quiz sebagai final quiz
- [ ] Dapat mengatur passing score
- [ ] Dapat mengatur max attempts
- [ ] Dapat melihat statistik attempts peserta
- [ ] Validasi input bekerja dengan baik

### Peserta
- [ ] Dapat melihat informasi final quiz
- [ ] Dapat memulai attempt baru
- [ ] Timer berjalan dengan benar (jika ada)
- [ ] Dapat submit jawaban
- [ ] Dapat melihat hasil dengan detail
- [ ] Tidak bisa attempt lagi jika sudah lulus
- [ ] Tidak bisa attempt lagi jika sudah mencapai max attempts
- [ ] Sertifikat hanya muncul setelah lulus final quiz

### Integrasi
- [ ] Migration berjalan tanpa error
- [ ] Model relasi berfungsi
- [ ] Routes terdaftar dengan benar
- [ ] Authorization bekerja (role instructor/admin/student)

## Migration Command
```bash
php artisan migrate
```

## Rollback Command (jika diperlukan)
```bash
php artisan migrate:rollback --step=3
```

## Notes
- Final quiz menggunakan sistem yang sama dengan quiz biasa
- Tracking attempts dilakukan di tabel terpisah (`quiz_attempts`)
- Setiap attempt menyimpan semua jawaban di `jawaban_peserta` dengan `quiz_attempt_id`
- Auto-submit quiz ketika timer habis (jika quiz memiliki durasi)
- Prevent accidental page close saat mengerjakan quiz
