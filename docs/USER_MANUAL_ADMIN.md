# User Manual - Admin
## Panduan Pengguna UpGreenius untuk Administrator

---

## Daftar Isi
1. [Login ke Dashboard Admin](#1-login-ke-dashboard-admin)
2. [Dashboard Admin](#2-dashboard-admin)
3. [Manajemen Pengguna](#3-manajemen-pengguna)
4. [Manajemen Kursus](#4-manajemen-kursus)
5. [Manajemen Transaksi](#5-manajemen-transaksi)
6. [Manajemen Voucher](#6-manajemen-voucher)
7. [Manajemen Promo Banner](#7-manajemen-promo-banner)
8. [Bank Soal](#8-bank-soal)

---

## 1. Login ke Dashboard Admin

### Langkah-langkah:
1. Buka halaman login di `/login`
2. Masukkan **Email** dan **Password** akun admin
3. Klik tombol **Login**
4. Anda akan diarahkan ke Dashboard Admin

> **Catatan:** Pastikan akun Anda memiliki role "admin" untuk mengakses fitur admin.

---

## 2. Dashboard Admin

Dashboard menampilkan ringkasan statistik platform:
- **Total Pengguna** - Jumlah semua user terdaftar
- **Total Kursus** - Jumlah kursus yang tersedia
- **Total Transaksi** - Jumlah transaksi pembayaran
- **Pendapatan** - Total pendapatan dari transaksi berhasil

---

## 3. Manajemen Pengguna

### Akses Menu
Navigasi: **Admin Panel → Users**

### Fitur yang Tersedia:

#### 3.1 Melihat Daftar Pengguna
- Lihat semua pengguna (Admin, Instructor, Student)
- Gunakan fitur **Search** untuk mencari berdasarkan nama/email
- Filter berdasarkan **Role**

#### 3.2 Menambah Pengguna Baru
1. Klik tombol **Tambah User**
2. Isi form:
   - Nama lengkap
   - Email
   - Password
   - Role (Admin/Instructor/Student)
3. Klik **Simpan**

#### 3.3 Edit Pengguna
1. Klik ikon **Edit** pada baris pengguna
2. Ubah data yang diperlukan
3. Klik **Update**

#### 3.4 Hapus Pengguna
1. Klik ikon **Hapus** pada baris pengguna
2. Konfirmasi penghapusan

---

## 4. Manajemen Kursus

### Akses Menu
Navigasi: **Admin Panel → Courses**

### Fitur yang Tersedia:

#### 4.1 Melihat Daftar Kursus
- Lihat semua kursus dengan status (Active, Draft, Inactive)
- Cari kursus berdasarkan judul/kategori

#### 4.2 Menambah Kursus Baru
1. Klik tombol **Tambah Kursus**
2. Isi form:
   - **Judul Kursus** - Nama kursus
   - **Deskripsi** - Penjelasan detail kursus
   - **Kategori** - Pilih kategori (Web Development, Data Science, dll)
   - **Mode** - Online, Offline, atau Hybrid
   - **Harga** - Harga kursus (0 untuk gratis)
   - **Instruktur** - Pilih instruktur yang bertanggung jawab
   - **Status** - Draft/Active/Inactive
   - **Gambar** - Upload gambar thumbnail
   - **Badge** (opsional) - Label seperti "Populer", "Terbaru"
3. Klik **Simpan Kursus**

#### 4.3 Kelola Detail Kursus
1. Klik nama kursus untuk masuk ke detail
2. Di halaman detail, Anda dapat:
   - Menambah **Modul/Section**
   - Menambah **Materi** (Video, PDF, Text, Quiz, Sesi Tatap Muka)
   - Mengatur **Final Quiz**
   - Melihat **Peserta** dan progress mereka

#### 4.4 Menambah Modul
1. Klik **Tambah Modul**
2. Isi judul dan deskripsi modul
3. Klik **Simpan**

#### 4.5 Menambah Materi
1. Pilih modul tujuan
2. Klik **Tambah Materi**
3. Pilih tipe materi:
   - **Video** - Upload file video
   - **PDF** - Upload dokumen PDF
   - **Text** - Konten teks/artikel
   - **Quiz** - Kuis dengan soal pilihan ganda
   - **Class Session** - Untuk kursus offline/hybrid
4. Isi detail materi dan simpan

---

## 5. Manajemen Transaksi

### Akses Menu
Navigasi: **Admin Panel → Transactions**

### Fitur yang Tersedia:

#### 5.1 Melihat Daftar Transaksi
- Lihat semua transaksi pembayaran
- Filter berdasarkan status: Pending, Success, Failed, Expired

#### 5.2 Detail Transaksi
1. Klik kode transaksi untuk melihat detail
2. Informasi yang ditampilkan:
   - Data pembeli
   - Kursus yang dibeli
   - Metode pembayaran
   - Status pembayaran
   - Bukti pembayaran (jika ada)

#### 5.3 Update Status Transaksi
1. Pada halaman detail transaksi
2. Ubah status sesuai kebutuhan
3. Klik **Update Status**

---

## 6. Manajemen Voucher

### Akses Menu
Navigasi: **Admin Panel → Vouchers**

### Fitur yang Tersedia:

#### 6.1 Membuat Voucher Baru
1. Klik **Tambah Voucher**
2. Isi form:
   - **Kode Voucher** - Kode unik (contoh: DISKON20)
   - **Tipe Diskon** - Persentase atau Nominal
   - **Nilai Diskon** - Berapa diskon yang diberikan
   - **Minimal Pembelian** (opsional)
   - **Kuota Penggunaan** - Berapa kali voucher bisa dipakai
   - **Tanggal Berlaku** - Periode aktif voucher
3. Klik **Simpan**

#### 6.2 Aktifkan/Nonaktifkan Voucher
- Klik toggle status untuk mengaktifkan atau menonaktifkan voucher

---

## 7. Manajemen Promo Banner

### Akses Menu
Navigasi: **Admin Panel → Promo Banners**

### Fitur yang Tersedia:

#### 7.1 Menambah Banner
1. Klik **Tambah Banner**
2. Upload gambar banner
3. Isi judul dan link tujuan
4. Atur periode tayang
5. Klik **Simpan**

#### 7.2 Mengatur Urutan Banner
- Drag and drop untuk mengubah urutan tampilan

---

## 8. Bank Soal

### Akses Menu
Navigasi: **Admin Panel → Bank Soal**

### Fitur yang Tersedia:

#### 8.1 Membuat Bank Soal
1. Klik **Buat Bank Soal**
2. Isi nama dan deskripsi
3. Klik **Simpan**

#### 8.2 Menambah Soal
1. Masuk ke bank soal yang diinginkan
2. Klik **Tambah Soal**
3. Isi:
   - Pertanyaan
   - Opsi jawaban (A, B, C, D)
   - Tandai jawaban yang benar
   - Poin nilai
4. Klik **Simpan Soal**

---

## Tips & Catatan Penting

> ⚠️ **Backup Data:** Lakukan backup database secara berkala

> 💡 **Kursus Offline/Hybrid:** Pastikan mengatur jadwal sesi tatap muka dengan benar

> 📧 **Notifikasi:** Sistem akan mengirim notifikasi otomatis ke instruktur saat ditugaskan ke kursus baru

---

*Dokumen ini merupakan panduan penggunaan untuk Administrator UpGreenius.*
*Versi: 1.0 | Tanggal: Desember 2024*
