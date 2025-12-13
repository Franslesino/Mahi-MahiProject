# Cara Menggunakan Fitur Final Quiz

## Untuk Instruktur/Admin

### 1. Aktifkan Final Quiz untuk Kursus

1. Masuk ke dashboard instruktur
2. Pilih kursus yang ingin diatur final quiznya
3. Klik menu "Pengaturan Final Quiz" atau akses: `/instructor/courses/{id_kursus}/final-quiz`
4. Centang "Aktifkan Final Quiz untuk Kursus Ini"
5. Pilih quiz yang akan dijadikan final quiz (harus sudah membuat quiz terlebih dahulu)
6. Atur **Nilai Minimum Kelulusan** (contoh: 70%)
7. Atur **Maksimal Percobaan** (contoh: 3 kali)
8. Klik "Simpan Pengaturan"

### 2. Membuat Quiz untuk Final Quiz

Jika belum ada quiz:
1. Buat quiz baru melalui menu quiz/assignment
2. Tambahkan soal-soal ke quiz tersebut
3. Aktifkan quiz
4. Kembali ke pengaturan final quiz dan pilih quiz yang sudah dibuat

### 3. Melihat Statistik Final Quiz

1. Dari halaman pengaturan final quiz, klik "Lihat Statistik"
2. Atau akses: `/instructor/courses/{id_kursus}/final-quiz/statistics`
3. Anda akan melihat:
   - Total peserta yang mengerjakan
   - Jumlah yang lulus/belum lulus
   - Rata-rata nilai
   - Detail per peserta (attempts, nilai, status)

## Untuk Peserta

### 1. Mengerjakan Final Quiz

1. Masuk ke halaman kursus yang Anda ikuti
2. Jika kursus memiliki final quiz, akan ada tombol/link "Final Quiz"
3. Klik untuk melihat informasi final quiz
4. Baca informasi:
   - Nilai minimum untuk lulus
   - Berapa kali Anda bisa mengulang
5. Klik "Mulai Quiz" atau "Mulai Percobaan ke-X"
6. Jawab semua soal dengan teliti
7. Klik "Submit Jawaban" setelah selesai

### 2. Melihat Hasil

1. Setelah submit, Anda akan melihat:
   - Nilai yang Anda dapatkan
   - Apakah lulus atau tidak
   - Jawaban benar/salah
   - Penjelasan (jika ada)

### 3. Mengulang Jika Belum Lulus

1. Jika nilai Anda belum mencapai minimum dan masih ada kesempatan mengulang:
2. Kembali ke halaman final quiz
3. Klik "Coba Lagi"
4. Anda akan memulai percobaan baru

### 4. Mendapatkan Sertifikat

Sertifikat akan otomatis tersedia setelah:
1. Semua materi selesai dipelajari
2. **Lulus final quiz** (jika kursus memiliki final quiz)

## Tips untuk Peserta

- Pastikan koneksi internet stabil saat mengerjakan
- Jika quiz memiliki timer, perhatikan waktu yang tersisa
- Jangan tutup browser saat mengerjakan quiz
- Baca soal dengan teliti sebelum menjawab
- Gunakan kesempatan mengulang dengan bijak

## Tips untuk Instruktur

- Buat soal final quiz yang komprehensif mencakup semua materi kursus
- Tentukan passing score yang adil (biasanya 60-80%)
- Berikan 2-3 kesempatan mengulang untuk memberi peluang kepada peserta
- Review statistik secara berkala untuk melihat tingkat kesulitan quiz
- Jika banyak peserta tidak lulus, pertimbangkan untuk:
  - Menurunkan passing score
  - Menambah jumlah attempts
  - Mereview tingkat kesulitan soal

## Troubleshooting

### Quiz tidak muncul di pilihan final quiz
- Pastikan quiz sudah dibuat untuk kursus yang sama
- Pastikan quiz sudah diaktifkan

### Peserta tidak bisa mengerjakan final quiz
- Cek apakah peserta sudah terdaftar di kursus
- Cek apakah peserta sudah kehabisan attempts
- Cek apakah peserta sudah lulus (tidak perlu mengulang)

### Sertifikat tidak muncul padahal sudah lulus
- Pastikan semua materi sudah selesai
- Pastikan status final quiz adalah "Lulus"
- Refresh halaman atau logout-login kembali

## FAQ

**Q: Apakah setiap kursus harus memiliki final quiz?**
A: Tidak, final quiz bersifat opsional. Hanya aktifkan jika diperlukan.

**Q: Bisakah mengubah pengaturan final quiz setelah peserta mulai mengerjakan?**
A: Bisa, tapi sebaiknya hindari mengubah passing score atau max attempts setelah peserta sudah mulai.

**Q: Apakah peserta bisa melihat jawaban yang benar setelah mengerjakan?**
A: Ya, di halaman hasil peserta bisa melihat review lengkap jawaban benar/salah.

**Q: Bagaimana jika peserta gagal di semua attempts?**
A: Peserta tidak akan mendapatkan sertifikat. Instruktur bisa mempertimbangkan untuk menambah attempts jika diperlukan.

**Q: Bisakah menggunakan quiz yang sama untuk final quiz di banyak kursus?**
A: Ya, satu quiz bisa digunakan sebagai final quiz di kursus yang berbeda.
