# User Manual - Instructor
## Panduan Pengguna UpGreenius untuk Instruktur

---

## Daftar Isi
1. [Login ke Panel Instruktur](#1-login-ke-panel-instruktur)
2. [Dashboard Instruktur](#2-dashboard-instruktur)
3. [Mengelola Kursus](#3-mengelola-kursus)
4. [Mengelola Materi](#4-mengelola-materi)
5. [Bank Soal & Quiz](#5-bank-soal--quiz)
6. [Sesi Tatap Muka & Absensi](#6-sesi-tatap-muka--absensi)
7. [Final Quiz](#7-final-quiz)
8. [Profil Instruktur](#8-profil-instruktur)

---

## 1. Login ke Panel Instruktur

### Langkah-langkah:
1. Buka halaman login di `/login`
2. Masukkan **Email** dan **Password** akun instruktur
3. Klik tombol **Login**
4. Anda akan diarahkan ke Dashboard Instruktur

---

## 2. Dashboard Instruktur

Dashboard menampilkan ringkasan aktivitas Anda:
- **Kursus Saya** - Jumlah kursus yang Anda kelola
- **Total Peserta** - Jumlah siswa di semua kursus
- **Notifikasi** - Pemberitahuan terbaru

### Menu Navigasi:
- 🏠 **Dashboard** - Halaman utama
- 📚 **Kursus Saya** - Daftar kursus yang ditugaskan
- 📝 **Bank Soal** - Kelola soal-soal quiz
- 👤 **Edit Profil** - Pengaturan profil
- ❓ **Bantuan** - Panduan penggunaan

---

## 3. Mengelola Kursus

### Akses Menu
Navigasi: **Kursus Saya**

### 3.1 Melihat Daftar Kursus
- Lihat semua kursus yang ditugaskan kepada Anda
- Setiap kartu kursus menampilkan:
  - Kategori dan Mode (Online/Offline/Hybrid)
  - Status (Active/Draft)
  - Jumlah materi dan peserta
  - Harga kursus

### 3.2 Masuk ke Detail Kursus
1. Klik **Kelola Materi** pada kartu kursus
2. Halaman detail menampilkan:
   - Daftar modul/section
   - Materi di setiap modul
   - Progress peserta

---

## 4. Mengelola Materi

### 4.1 Menambah Modul Baru
1. Di halaman detail kursus, klik **Tambah Modul**
2. Isi:
   - **Judul Modul**
   - **Deskripsi** (opsional)
3. Klik **Simpan**

### 4.2 Menambah Materi
1. Klik **Tambah Materi** pada modul yang diinginkan
2. Pilih **Tipe Materi**:
   - **Video** - File video (MP4, MOV)
   - **PDF** - Dokumen PDF
   - **Text** - Konten teks/artikel
   - **Quiz** - Kuis dengan soal
   - **Class Session** - Sesi tatap muka
3. Isi detail:
   - **Judul Materi**
   - **Deskripsi**
   - Upload file (jika ada)
   - **Status** - Published/Draft
4. Klik **Simpan**

### 4.3 Edit Materi
1. Klik ikon **Edit** pada materi
2. Ubah data yang diperlukan
3. Klik **Update**

### 4.4 Hapus Materi
1. Klik ikon **Hapus** pada materi
2. Konfirmasi penghapusan

### 4.5 Preview Materi
- Klik ikon **Preview** untuk melihat tampilan materi seperti yang dilihat siswa

---

## 5. Bank Soal & Quiz

### Akses Menu
Navigasi: **Bank Soal**

### 5.1 Membuat Bank Soal
1. Klik **Buat Bank Soal**
2. Isi nama dan deskripsi
3. Klik **Simpan**

### 5.2 Menambah Soal ke Bank Soal
1. Masuk ke bank soal
2. Klik **Tambah Soal**
3. Isi:
   - **Pertanyaan**
   - **Opsi Jawaban** (A, B, C, D)
   - Tandai **Jawaban Benar**
   - **Poin**
4. Klik **Simpan**

### 5.3 Membuat Quiz di Kursus
1. Di halaman detail kursus, pilih modul
2. Klik **Tambah Quiz**
3. Isi:
   - Judul quiz
   - Deskripsi
   - Durasi (menit)
   - Passing score
   - Pilih bank soal (opsional)
4. Klik **Buat Quiz**

### 5.4 Mengelola Soal Quiz
1. Masuk ke quiz yang dibuat
2. Klik **Kelola Soal**
3. Tambah soal baru atau import dari bank soal
4. Klik **Publish** untuk mengaktifkan quiz

---

## 6. Sesi Tatap Muka & Absensi

> **Catatan:** Fitur ini hanya untuk kursus dengan mode **Offline** atau **Hybrid**

### 6.1 Membuat Sesi Tatap Muka
1. Di halaman detail kursus, klik **Tambah Materi**
2. Pilih tipe **Class Session**
3. Isi:
   - **Judul Sesi**
   - **Tanggal** sesi
   - **Waktu Mulai** dan **Waktu Selesai**
   - **Tipe** - Offline atau Online
   - **Lokasi** (untuk offline)
   - **Link Meeting** (untuk online)
4. Klik **Simpan**

### 6.2 Mengelola Absensi
1. Buka materi sesi tatap muka
2. Klik **Kelola Kehadiran**
3. Daftar peserta akan muncul
4. Untuk setiap peserta, pilih status:
   - ✅ **Hadir**
   - ❌ **Tidak Hadir**
   - 📝 **Izin**
   - 🏥 **Sakit**
   - ⏰ **Terlambat**
5. Klik **Simpan** untuk menyimpan perubahan

### 6.3 Generate Daftar Kehadiran
- Klik **Generate Peserta** untuk menambahkan semua siswa terdaftar ke daftar kehadiran
- Klik **Tandai Semua Hadir** untuk absensi massal

> 💡 **Tips:** Siswa yang ditandai "Hadir" otomatis dianggap menyelesaikan materi sesi tersebut

---

## 7. Final Quiz

Final Quiz adalah ujian akhir yang wajib dilalui siswa untuk menyelesaikan kursus.

### 7.1 Mengatur Final Quiz
1. Di halaman detail kursus, klik **Final Quiz**
2. Pilih quiz yang akan dijadikan final quiz
3. Atur:
   - **Passing Score** - Nilai minimum kelulusan
   - **Max Attempts** - Jumlah percobaan maksimal
4. Klik **Simpan**

### 7.2 Mengaktifkan Final Quiz
- Toggle status **Aktif** untuk mengaktifkan final quiz
- Siswa harus menyelesaikan semua materi sebelum bisa mengerjakan final quiz

---

## 8. Profil Instruktur

### Akses Menu
Navigasi: **Edit Profil**

### 8.1 Update Informasi Profil
1. Ubah data yang diperlukan:
   - Nama
   - Bio/Deskripsi
   - Foto profil
2. Klik **Simpan**

### 8.2 Ganti Password
1. Masukkan password lama
2. Masukkan password baru
3. Konfirmasi password baru
4. Klik **Update Password**

---

## Tips & Catatan Penting

> 📌 **Status Materi:** Pastikan mengubah status materi ke "Published" agar siswa dapat mengaksesnya

> 🎯 **Quiz:** Klik "Publish" pada quiz agar siswa dapat mengerjakan

> 📊 **Progress:** Pantau progress peserta di halaman detail kursus

> 🔔 **Notifikasi:** Cek notifikasi secara berkala untuk pemberitahuan penting

---

*Dokumen ini merupakan panduan penggunaan untuk Instruktur UpGreenius.*
*Versi: 1.0 | Tanggal: Desember 2024*
