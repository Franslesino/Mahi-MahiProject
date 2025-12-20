# BLACK BOX TESTING v2.0
## UpGreenius E-Learning Platform
### Last Updated: 21 Desember 2024

---

## 1. PENDAHULUAN

### 1.1 Tujuan
Dokumen ini berisi test case untuk pengujian Black Box pada aplikasi UpGreenius yang telah diperbarui.

### 1.2 Teknik yang Digunakan
- **Equivalence Partitioning (EP)**
- **Boundary Value Analysis (BVA)**
- **Decision Table Testing**
- **State Transition Testing**

### 1.3 Perubahan dari Versi Sebelumnya
- Ditambahkan TC untuk Final Quiz (Timer dari database, Exit/Resume, Incomplete attempt blocking)
- Ditambahkan TC untuk Material Reordering (Drag & Drop)
- Ditambahkan TC untuk Quiz Deactivation Handler
- Diperbarui TC untuk UI Final Quiz Settings

---

## 2. TEST CASES - MODUL AUTENTIKASI (25 TC)

### 2.1 Login & Register

| TC ID | Test Case | Input | Expected | Actual | Status |
|-------|-----------|-------|----------|--------|--------|
| BB-AUTH-001 | Login valid admin | email: admin@test.com, pass: valid | Redirect /admin/dashboard | Redirect /admin/dashboard | ✅ PASS |
| BB-AUTH-002 | Login valid instructor | email: instructor@test.com | Redirect /instructor/dashboard | Redirect /instructor/dashboard | ✅ PASS |
| BB-AUTH-003 | Login valid student | email: student@test.com | Redirect / | Redirect / | ✅ PASS |
| BB-AUTH-004 | Login email kosong | email: (kosong) | Error "Email wajib diisi" | Error "Email wajib diisi" | ✅ PASS |
| BB-AUTH-005 | Login password kosong | pass: (kosong) | Error "Password wajib diisi" | Error "Password wajib diisi" | ✅ PASS |
| BB-AUTH-006 | Login email tidak terdaftar | email: notexist@test.com | Error "tidak ditemukan" | Error "tidak ditemukan" | ✅ PASS |
| BB-AUTH-007 | Login password salah | pass: wrongpassword | Error "password salah" | Error "password salah" | ✅ PASS |
| BB-AUTH-008 | Login format email invalid | email: invalid | Validation error | Validation error | ✅ PASS |
| BB-AUTH-009 | Login Google OAuth | Klik Google login | Redirect Google → Login | Redirect Google → Login | ✅ PASS |
| BB-AUTH-010 | Logout | Klik logout | Session ended, redirect login | Session ended, redirect login | ✅ PASS |
| BB-AUTH-011 | Register valid | Data lengkap valid | User created, email sent | User created, email sent | ✅ PASS |
| BB-AUTH-012 | Register email duplikat | email: existing | Error "sudah terdaftar" | Error "sudah terdaftar" | ✅ PASS |
| BB-AUTH-013 | Register password pendek | pass: abc | Error "minimal 8 karakter" | Error "minimal 8 karakter" | ✅ PASS |
| BB-AUTH-014 | Register password tidak cocok | confirm != pass | Error "tidak cocok" | Error "tidak cocok" | ✅ PASS |
| BB-AUTH-015 | Register nama kosong | name: (kosong) | Error "Nama wajib" | Error "Nama wajib" | ✅ PASS |
| BB-AUTH-016 | Verifikasi email valid | Link valid | Email verified | Email verified | ✅ PASS |
| BB-AUTH-017 | Verifikasi email expired | Link expired | Error "kadaluarsa" | Error "kadaluarsa" | ✅ PASS |
| BB-AUTH-018 | Forgot password valid | email: valid | Email reset sent | Email reset sent | ✅ PASS |
| BB-AUTH-019 | Forgot password email tidak ada | email: notexist | Error "tidak ditemukan" | Error "tidak ditemukan" | ✅ PASS |
| BB-AUTH-020 | Reset password valid | Token valid, pass valid | Password changed | Password changed | ✅ PASS |
| BB-AUTH-021 | Reset password token expired | Token expired | Error "token expired" | Error "token expired" | ✅ PASS |
| BB-AUTH-022 | Reset password pendek | pass: abc | Error "minimal 8" | Error "minimal 8" | ✅ PASS |
| BB-AUTH-023 | Akses login saat sudah login | User logged in | Redirect dashboard | Redirect dashboard | ✅ PASS |
| BB-AUTH-024 | Akses protected tanpa login | Guest access /my-courses | Redirect /login | Redirect /login | ✅ PASS |
| BB-AUTH-025 | Remember me | Centang remember me | Session persistent | Session persistent | ✅ PASS |

---

## 3. TEST CASES - MODUL STUDENT (70 TC)

### 3.1 Browse & Search Kursus (15 TC)

| TC ID | Test Case | Input | Expected | Actual | Status |
|-------|-----------|-------|----------|--------|--------|
| BB-STD-001 | View daftar kursus | Buka home | List kursus tampil | List kursus tampil | ✅ PASS |
| BB-STD-002 | Search kursus valid | Search: "Laravel" | Hasil matching | Hasil matching | ✅ PASS |
| BB-STD-003 | Search tidak ditemukan | Search: "xyz123" | "Tidak ditemukan" | "Tidak ditemukan" | ✅ PASS |
| BB-STD-004 | Filter kategori | Kategori: Programming | Kursus programming | Kursus programming | ✅ PASS |
| BB-STD-005 | Clear filter | Klik clear | Semua kursus tampil | Semua kursus tampil | ✅ PASS |
| BB-STD-006 | View detail kursus | Klik kursus | Detail tampil | Detail tampil | ✅ PASS |
| BB-STD-007 | Pagination | Klik page 2 | Halaman 2 tampil | Halaman 2 tampil | ✅ PASS |
| BB-STD-008 | View kursus unpublished | Akses URL unpublished | 404 atau redirect | 404 atau redirect | ✅ PASS |
| BB-STD-009 | View daftar instruktur | Buka /instructors | List instruktur | List instruktur | ✅ PASS |
| BB-STD-010 | View detail instruktur | Klik instruktur | Profile & kursus | Profile & kursus | ✅ PASS |
| BB-STD-011 | Search empty | Search: "" | Semua kursus | Semua kursus | ✅ PASS |
| BB-STD-012 | Filter + Search | Search + kategori | Filtered results | Filtered results | ✅ PASS |
| BB-STD-013 | View all courses | Buka /all-courses | Semua kursus published | Semua kursus published | ✅ PASS |
| BB-STD-014 | Sort by newest | Sort: newest | Urutan terbaru | Urutan terbaru | ✅ PASS |
| BB-STD-015 | View promo banner | Home dengan banner | Banner tampil | Banner tampil | ✅ PASS |

### 3.2 Checkout & Payment (20 TC)

| TC ID | Test Case | Input | Expected | Actual | Status |
|-------|-----------|-------|----------|--------|--------|
| BB-STD-016 | Checkout kursus | Klik Beli | Checkout page | Checkout page | ✅ PASS |
| BB-STD-017 | Apply voucher valid (%) | DISKON50 | Diskon 50% | Diskon 50% | ✅ PASS |
| BB-STD-018 | Apply voucher valid (fixed) | FIXED25K | Diskon Rp 25.000 | Diskon Rp 25.000 | ✅ PASS |
| BB-STD-019 | Apply voucher invalid | INVALID123 | Error "tidak valid" | Error "tidak valid" | ✅ PASS |
| BB-STD-020 | Apply voucher expired | EXPIRED | Error "kadaluarsa" | Error "kadaluarsa" | ✅ PASS |
| BB-STD-021 | Apply voucher limit | LIMIT (used up) | Error "batas tercapai" | Error "batas tercapai" | ✅ PASS |
| BB-STD-022 | Apply voucher min purchase | Min 500k, kursus 100k | Error "minimal pembelian" | Error "minimal pembelian" | ✅ PASS |
| BB-STD-023 | Remove voucher | Klik hapus voucher | Harga normal | Harga normal | ✅ PASS |
| BB-STD-024 | Payment Midtrans success | Complete payment | Status Paid, enrolled | Status Paid, enrolled | ✅ PASS |
| BB-STD-025 | Payment Midtrans pending | Belum bayar | Status Pending | Status Pending | ✅ PASS |
| BB-STD-026 | Payment Midtrans cancel | Cancel di popup | Status di-cancel | Status di-cancel | ✅ PASS |
| BB-STD-027 | Cancel transaksi | Klik Batalkan | Status Cancelled | Status Cancelled | ✅ PASS |
| BB-STD-028 | Regenerate snap token | Token expired, regenerate | Token baru dibuat | Token baru dibuat | ✅ PASS |
| BB-STD-029 | View riwayat transaksi | Buka my-transactions | List transaksi | List transaksi | ✅ PASS |
| BB-STD-030 | View detail transaksi | Klik transaksi | Detail tampil | Detail tampil | ✅ PASS |
| BB-STD-031 | Beli kursus gratis | Kursus Rp 0 | Langsung enrolled | Langsung enrolled | ✅ PASS |
| BB-STD-032 | Beli kursus sudah dimiliki | Sudah enrolled | Error "sudah dimiliki" | Error "sudah dimiliki" | ✅ PASS |
| BB-STD-033 | Voucher 100% diskon | GRATIS100 | Total Rp 0, langsung enrolled | Total Rp 0, enrolled | ✅ PASS |
| BB-STD-034 | Double voucher | Apply 2x voucher | Hanya 1 voucher | Hanya 1 voucher | ✅ PASS |
| BB-STD-035 | Checkout tanpa login | Guest checkout | Redirect login | Redirect login | ✅ PASS |

### 3.3 Belajar Kursus (20 TC)

| TC ID | Test Case | Input | Expected | Actual | Status |
|-------|-----------|-------|----------|--------|--------|
| BB-STD-036 | View My Courses | Buka my-courses | Kursus yang dimiliki | Kursus yang dimiliki | ✅ PASS |
| BB-STD-037 | Akses learn page | Klik Lanjut Belajar | Learn page tampil | Learn page tampil | ✅ PASS |
| BB-STD-038 | View materi text | Klik materi text | Konten teks tampil | Konten teks tampil | ✅ PASS |
| BB-STD-039 | View materi video | Klik materi video | Video player aktif | Video player aktif | ✅ PASS |
| BB-STD-040 | View materi file | Klik materi file | File preview/download | File preview/download | ✅ PASS |
| BB-STD-041 | Download file | Klik download | File terdownload | File terdownload | ✅ PASS |
| BB-STD-042 | View materi link | Klik materi link | Link preview | Link preview | ✅ PASS |
| BB-STD-043 | View class session | Klik class session | Jadwal, lokasi, status kehadiran tampil | Jadwal, lokasi, status tampil | ✅ PASS |
| BB-STD-044 | Mark complete | Klik Tandai Selesai | Materi complete | Materi complete | ✅ PASS |
| BB-STD-045 | Progress tracking | Complete 5/10 materi | Progress 50% | Progress 50% | ✅ PASS |
| BB-STD-046 | Akses kursus tidak enrolled | URL kursus lain | Error 403 | Error 403 | ✅ PASS |
| BB-STD-047 | Quiz materi | Klik quiz | Form quiz tampil dengan timer | Form quiz tampil dengan timer | ✅ PASS |
| BB-STD-048 | Submit quiz | Submit jawaban | Score tampil | Score tampil | ✅ PASS |
| BB-STD-049 | Quiz retry | Klik Coba Lagi | Reset quiz | Reset quiz | ✅ PASS |
| BB-STD-050 | Final quiz access | Progress 100% | Final quiz available | Final quiz available | ✅ PASS |
| BB-STD-051 | Final quiz not ready | Progress < 100% | Error "selesaikan materi" | Error "selesaikan materi" | ✅ PASS |
| BB-STD-052 | Final quiz pass | Score >= passing | Status PASS | Status PASS | ✅ PASS |
| BB-STD-053 | Final quiz fail | Score < passing | Status FAIL | Status FAIL | ✅ PASS |
| BB-STD-054 | Certificate generated | Pass final quiz | Sertifikat dibuat | Sertifikat dibuat | ✅ PASS |
| BB-STD-055 | Download certificate | Klik Download | PDF terdownload | PDF terdownload | ✅ PASS |

### 3.4 Final Quiz - Fitur Baru (15 TC) ⭐ NEW

| TC ID | Test Case | Input | Expected | Actual | Status |
|-------|-----------|-------|----------|--------|--------|
| BB-STD-056 | Timer dari database | Mulai quiz | Timer berdasarkan started_at | Timer berdasarkan started_at | ✅ PASS |
| BB-STD-057 | Timer tetap berjalan saat keluar | Keluar lalu masuk lagi | Timer melanjutkan dari waktu tersisa | Timer melanjutkan dari waktu tersisa | ✅ PASS |
| BB-STD-058 | Progress tersimpan saat keluar | Jawab 3 soal, keluar | Jawaban tetap ada saat kembali | Jawaban tetap ada saat kembali | ✅ PASS |
| BB-STD-059 | Incomplete attempt blocking | Mulai quiz, keluar, coba mulai baru | Hanya tampil "Lanjutkan", tidak bisa mulai baru | Hanya tampil "Lanjutkan" | ✅ PASS |
| BB-STD-060 | Lanjutkan percobaan | Klik "Lanjutkan Percobaan" | Resume quiz dengan jawaban sebelumnya | Resume quiz dengan jawaban | ✅ PASS |
| BB-STD-061 | Modal exit menampilkan info timer | Klik "Kembali ke Kursus" | Modal menampilkan "waktu tetap berjalan" | Modal menampilkan info timer | ✅ PASS |
| BB-STD-062 | Auto-submit saat waktu habis | Tunggu timer = 0 | Quiz otomatis disubmit | Quiz otomatis disubmit | ✅ PASS |
| BB-STD-063 | Warning 5 menit tersisa | Timer = 5 menit | Alert "5 menit lagi" | Alert "5 menit lagi" | ✅ PASS |
| BB-STD-064 | Quiz deactivated saat mengerjakan | Admin nonaktifkan quiz | Redirect + alert "dalam proses maintenance" | Redirect + alert maintenance | ✅ PASS |
| BB-STD-065 | Max attempts tercapai | 3/3 percobaan | Tidak bisa mulai lagi | Tidak bisa mulai lagi | ✅ PASS |
| BB-STD-066 | Sudah lulus, tidak bisa mulai | Sudah pass | Tampilan "Selamat Lulus" | Tampilan "Selamat Lulus" | ✅ PASS |
| BB-STD-067 | View hasil quiz | Klik "Lihat Detail" | Halaman result tampil | Halaman result tampil | ✅ PASS |
| BB-STD-068 | Soal essay | Jawab essay | Teks tersimpan | Teks tersimpan | ✅ PASS |
| BB-STD-069 | Semua soal harus dijawab | Skip 1 soal | Tidak bisa submit, warning | Tidak bisa submit, warning | ✅ PASS |
| BB-STD-070 | Navigasi nomor soal | Klik nomor 5 | Langsung ke soal 5 | Langsung ke soal 5 | ✅ PASS |

---

## 4. TEST CASES - MODUL INSTRUCTOR (35 TC)

### 4.1 Course & Material Management (25 TC)

| TC ID | Test Case | Input | Expected | Actual | Status |
|-------|-----------|-------|----------|--------|--------|
| BB-INS-001 | View dashboard | Login instructor | Stats tampil | Stats tampil | ✅ PASS |
| BB-INS-002 | View daftar kursus | Buka menu courses | List kursus diampu | List kursus diampu | ✅ PASS |
| BB-INS-003 | View detail kursus | Klik kursus | Detail & materials | Detail & materials | ✅ PASS |
| BB-INS-004 | Akses kursus bukan milik | URL kursus lain | Error 403 | Error 403 | ✅ PASS |
| BB-INS-005 | Tambah section | Nama: "Modul 1" | Section created | Section created | ✅ PASS |
| BB-INS-006 | Edit section | Nama baru | Section updated | Section updated | ✅ PASS |
| BB-INS-007 | Hapus section kosong | Section tanpa materi | Section deleted | Section deleted | ✅ PASS |
| BB-INS-008 | Hapus section berisi | Section ada materi | Warning/delete cascade | Warning/delete cascade | ✅ PASS |
| BB-INS-009 | Tambah materi text | Type: text | Materi created | Materi created | ✅ PASS |
| BB-INS-010 | Tambah materi video | Type: video, embed URL | Materi created | Materi created | ✅ PASS |
| BB-INS-011 | Tambah materi file | Upload PDF | Materi created | Materi created | ✅ PASS |
| BB-INS-012 | Tambah materi link | Type: link | Materi created | Materi created | ✅ PASS |
| BB-INS-013 | Tambah class session | Jadwal, lokasi | Session created | Session created | ✅ PASS |
| BB-INS-014 | Edit materi | Update content | Materi updated | Materi updated | ✅ PASS |
| BB-INS-015 | Hapus materi | Klik delete | Materi deleted | Materi deleted | ✅ PASS |
| BB-INS-016 | Preview materi | Klik preview | Preview tampil | Preview tampil | ✅ PASS |
| BB-INS-017 | Upload file > limit | File 50MB | Error "ukuran melebihi" | Error "ukuran melebihi" | ✅ PASS |
| BB-INS-018 | Tambah materi tanpa judul | Title: kosong | Error "judul wajib" | Error "judul wajib" | ✅ PASS |
| BB-INS-019 | Generate attendance | Klik generate | Attendance records created | Attendance records created | ✅ PASS |
| BB-INS-020 | Update attendance | Ubah ke "Hadir" | Status updated | Status updated | ✅ PASS |
| BB-INS-021 | Mark all present | Klik tandai semua | Semua "Hadir" | Semua "Hadir" | ✅ PASS |
| BB-INS-022 | Tambah bank soal | Nama, deskripsi | Bank created | Bank created | ✅ PASS |
| BB-INS-023 | Tambah soal MC | Pertanyaan + opsi | Soal created | Soal created | ✅ PASS |
| BB-INS-024 | Import soal ke quiz | Pilih soal | Soal imported | Soal imported | ✅ PASS |
| BB-INS-025 | Hapus soal terpakai | Soal di quiz | Error "sedang digunakan" | Error "sedang digunakan" | ✅ PASS |

### 4.2 Material Reordering - Fitur Baru (5 TC) ⭐ NEW

| TC ID | Test Case | Input | Expected | Actual | Status |
|-------|-----------|-------|----------|--------|--------|
| BB-INS-026 | Drag materi ke posisi baru | Drag materi 3 ke posisi 1 | Urutan berubah, tersimpan | Urutan berubah, tersimpan | ✅ PASS |
| BB-INS-027 | Reorder multiple materials | Drag beberapa materi | Semua urutan tersimpan | Semua urutan tersimpan | ✅ PASS |
| BB-INS-028 | Reorder antar section | Drag ke section lain | Tidak bisa (tetap di section) | Tetap di section | ✅ PASS |
| BB-INS-029 | Reorder via drag handle | Drag via icon grip | Bisa reorder | Bisa reorder | ✅ PASS |
| BB-INS-030 | Refresh setelah reorder | Refresh halaman | Urutan tetap seperti setelah reorder | Urutan tetap | ✅ PASS |

### 4.3 Final Quiz Settings - Fitur Baru (5 TC) ⭐ NEW

| TC ID | Test Case | Input | Expected | Actual | Status |
|-------|-----------|-------|----------|--------|--------|
| BB-INS-031 | Buat final quiz baru | Klik "Buat Final Quiz" | Form quiz tampil | Form quiz tampil | ✅ PASS |
| BB-INS-032 | UI tanpa dropdown | Buka settings | Tidak ada dropdown, hanya button | Tidak ada dropdown | ✅ PASS |
| BB-INS-033 | Nilai minimum integer | Input: 70 | Tersimpan sebagai 70 (bukan 70.00) | Tersimpan 70 | ✅ PASS |
| BB-INS-034 | Toggle aktivasi quiz | Klik Nonaktifkan | Quiz inactive, peserta ter-redirect | Quiz inactive | ✅ PASS |
| BB-INS-035 | Tampil info quiz aktif | Quiz sudah ada | Info nama, soal, durasi tampil | Info tampil | ✅ PASS |

---

## 5. TEST CASES - MODUL ADMIN (30 TC)

### 5.1 User & Course Management (20 TC)

| TC ID | Test Case | Input | Expected | Actual | Status |
|-------|-----------|-------|----------|--------|--------|
| BB-ADM-001 | View dashboard | Login admin | Full stats | Full stats | ✅ PASS |
| BB-ADM-002 | View daftar user | Buka Users | All users listed | All users listed | ✅ PASS |
| BB-ADM-003 | Search user | Search: "john" | Matching users | Matching users | ✅ PASS |
| BB-ADM-004 | Filter user by role | Role: student | Only students | Only students | ✅ PASS |
| BB-ADM-005 | Create user | Data valid | User created | User created | ✅ PASS |
| BB-ADM-006 | Create user email duplikat | Email existing | Error "sudah ada" | Error "sudah ada" | ✅ PASS |
| BB-ADM-007 | Edit user | Update data | User updated | User updated | ✅ PASS |
| BB-ADM-008 | Change user role | Role: instructor | Role changed | Role changed | ✅ PASS |
| BB-ADM-009 | Delete user | Klik delete | User deleted | User deleted | ✅ PASS |
| BB-ADM-010 | Delete diri sendiri | Delete own account | Error "tidak bisa" | Error "tidak bisa" | ✅ PASS |
| BB-ADM-011 | View semua kursus | Buka Courses | All courses | All courses | ✅ PASS |
| BB-ADM-012 | Create kursus | Data valid | Course created | Course created | ✅ PASS |
| BB-ADM-013 | Edit kursus | Update data | Course updated | Course updated | ✅ PASS |
| BB-ADM-014 | Publish kursus | Toggle publish | Status published | Status published | ✅ PASS |
| BB-ADM-015 | Delete kursus | Klik delete | Course deleted | Course deleted | ✅ PASS |
| BB-ADM-016 | View transaksi | Buka Transactions | All transactions | All transactions | ✅ PASS |
| BB-ADM-017 | Update status transaksi | Status: paid | Status updated, enrolled | Status updated, enrolled | ✅ PASS |
| BB-ADM-018 | Export transaksi | Klik Export | CSV downloaded | CSV downloaded | ✅ PASS |
| BB-ADM-019 | Create voucher | Data valid | Voucher created | Voucher created | ✅ PASS |
| BB-ADM-020 | Toggle voucher | Active/Inactive | Status toggled | Status toggled | ✅ PASS |

### 5.2 Material Reordering Admin - Fitur Baru (5 TC) ⭐ NEW

| TC ID | Test Case | Input | Expected | Actual | Status |
|-------|-----------|-------|----------|--------|--------|
| BB-ADM-021 | Drag materi ke posisi baru | Drag materi | Urutan berubah, tersimpan | Urutan berubah | ✅ PASS |
| BB-ADM-022 | Drag modul/section | Drag section | Urutan section berubah | Urutan berubah | ✅ PASS |
| BB-ADM-023 | Reorder via drag handle | Drag via icon grip | Bisa reorder | Bisa reorder | ✅ PASS |
| BB-ADM-024 | Refresh setelah reorder | Refresh halaman | Urutan tetap | Urutan tetap | ✅ PASS |
| BB-ADM-025 | Toggle expand/collapse section | Klik section | Materi di dalam toggle | Materi toggle | ✅ PASS |

### 5.3 Final Quiz Settings Admin - Fitur Baru (5 TC) ⭐ NEW

| TC ID | Test Case | Input | Expected | Actual | Status |
|-------|-----------|-------|----------|--------|--------|
| BB-ADM-026 | Buat final quiz baru | Klik "Buat Final Quiz" | Form quiz tampil | Form quiz tampil | ✅ PASS |
| BB-ADM-027 | UI tanpa dropdown | Buka settings | Tidak ada dropdown, hanya button | Tidak ada dropdown | ✅ PASS |
| BB-ADM-028 | Nilai minimum integer | Input: 70 | Tersimpan sebagai 70 | Tersimpan 70 | ✅ PASS |
| BB-ADM-029 | Toggle aktivasi quiz | Klik Nonaktifkan | Quiz inactive | Quiz inactive | ✅ PASS |
| BB-ADM-030 | View statistik quiz | Klik "Lihat Statistik" | Statistik tampil | Statistik tampil | ✅ PASS |

---

## 6. BOUNDARY VALUE ANALYSIS (10 TC)

| TC ID | Field | Min | Max | Test Values | Expected | Actual | Status |
|-------|-------|-----|-----|-------------|----------|--------|--------|
| BVA-001 | Password | 8 | 255 | 7, 8, 255, 256 | 8-255 valid | 8-255 valid | ✅ PASS |
| BVA-002 | Nama | 1 | 255 | 0, 1, 255, 256 | 1-255 valid | 1-255 valid | ✅ PASS |
| BVA-003 | Harga | 0 | 999999999 | -1, 0, max | 0-max valid | 0-max valid | ✅ PASS |
| BVA-004 | Diskon % | 1 | 100 | 0, 1, 100, 101 | 1-100 valid | 1-100 valid | ✅ PASS |
| BVA-005 | Passing Grade | 1 | 100 | 0, 1, 100, 101 | 1-100 valid | 1-100 valid | ✅ PASS |
| BVA-006 | Quiz Duration | 1 | 999 | 0, 1, 999, 1000 | 1-999 valid | 1-999 valid | ✅ PASS |
| BVA-007 | Max Attempts | 1 | 10 | 0, 1, 10, 11 | 1-10 valid | 1-10 valid | ✅ PASS |
| BVA-008 | Urutan Materi | 1 | 999 | 0, 1, 999 | 1+ valid | 1+ valid | ✅ PASS |
| BVA-009 | Poin Soal | 1 | 100 | 0, 1, 100 | 1+ valid | 1+ valid | ✅ PASS |
| BVA-010 | Section Name | 1 | 255 | 0, 1, 255 | 1-255 valid | 1-255 valid | ✅ PASS |

---

## 7. STATE TRANSITION TESTING

### 7.1 Transaction Status (5 TC)

| From | Action | To | Expected | Actual | Status |
|------|--------|-----|----------|--------|--------|
| (none) | Create | Pending | Transaksi dibuat | Transaksi dibuat | ✅ PASS |
| Pending | Pay success | Paid | Status Paid, user enrolled | Paid, enrolled | ✅ PASS |
| Pending | Cancel | Cancelled | Status Cancelled | Cancelled | ✅ PASS |
| Pending | Expire | Expired | Status Expired | Expired | ✅ PASS |
| Paid | Cancel | ❌ | Error tidak bisa cancel | Error | ✅ PASS |

### 7.2 Quiz Attempt Status - NEW (6 TC) ⭐

| From | Action | To | Expected | Actual | Status |
|------|--------|-----|----------|--------|--------|
| (none) | Start Quiz | In Progress | Attempt created, started_at filled | Attempt created | ✅ PASS |
| In Progress | Submit | Completed | Score calculated, completed_at filled | Score calculated | ✅ PASS |
| In Progress | Exit | In Progress | Tetap in progress, bisa lanjut | Tetap in progress | ✅ PASS |
| In Progress | Timer habis | Completed | Auto-submit | Auto-submit | ✅ PASS |
| Completed (Pass) | Start New | ❌ | Tidak bisa, sudah lulus | Tidak bisa | ✅ PASS |
| Completed (Fail) | Start New | In Progress | Attempt baru jika < max | Attempt baru | ✅ PASS |

### 7.3 Final Quiz Status - NEW (4 TC) ⭐

| From | Action | To | Expected | Actual | Status |
|------|--------|-----|----------|--------|--------|
| Non-exist | Create Quiz | Inactive | Quiz dibuat, is_active = false | Quiz dibuat | ✅ PASS |
| Inactive | Activate | Active | is_active = true, bisa dikerjakan | Active | ✅ PASS |
| Active | Deactivate | Inactive | Peserta ter-redirect | Ter-redirect | ✅ PASS |
| Active | Student taking | Active | Peserta bisa mengerjakan | Bisa mengerjakan | ✅ PASS |

---

## 8. DECISION TABLE TESTING

### 8.1 Final Quiz Access Decision

| Condition | C1 | C2 | C3 | C4 | C5 |
|-----------|----|----|----|----|-----|
| Progress 100%? | Y | Y | Y | N | Y |
| Quiz Active? | Y | Y | N | Y | Y |
| Has Incomplete? | Y | N | N | N | N |
| Max Attempts? | N | N | N | N | Y |
| **Action** | Lanjutkan | Mulai Baru | Error Maintenance | Error Selesaikan | Error Max |
| **Status** | ✅ PASS | ✅ PASS | ✅ PASS | ✅ PASS | ✅ PASS |

### 8.2 Certificate Generation Decision

| Condition | C1 | C2 | C3 |
|-----------|----|----|-----|
| Passed Final Quiz? | Y | N | N |
| All Materials Complete? | Y | Y | N |
| **Action** | Certificate Generated | No Certificate | No Certificate |
| **Status** | ✅ PASS | ✅ PASS | ✅ PASS |

---

## 9. RINGKASAN

| Modul | TC | Pass | Fail | Rate |
|-------|-----|------|------|------|
| Authentication | 25 | 25 | 0 | 100% |
| Student | 70 | 70 | 0 | 100% |
| Instructor | 35 | 35 | 0 | 100% |
| Admin | 30 | 30 | 0 | 100% |
| BVA | 10 | 10 | 0 | 100% |
| State Transition | 15 | 15 | 0 | 100% |
| Decision Table | 8 | 8 | 0 | 100% |
| **TOTAL** | **193** | **193** | **0** | **100%** |

---

## 10. FITUR BARU YANG DITEST (v2.0)

### 10.1 Final Quiz Improvements
- ✅ Timer menggunakan `started_at` dari database (tidak reset saat keluar)
- ✅ Progress jawaban tersimpan di localStorage
- ✅ Blocking incomplete attempt (tidak bisa mulai baru saat ada yang belum selesai)
- ✅ Poll status quiz setiap 30 detik (redirect jika deactivated)
- ✅ Modal exit dengan info "waktu tetap berjalan"

### 10.2 UI Simplification
- ✅ Final Quiz Settings tanpa dropdown (hanya button Create/Info)
- ✅ Nilai Minimum Kelulusan sebagai integer (tanpa .00)

### 10.3 Material Management
- ✅ Drag & drop reordering untuk materials
- ✅ Drag & drop reordering untuk sections/modules

---

*Black Box Testing v2.0 - 21 Desember 2024*
*Total Test Cases: 193*
*Pass Rate: 100%*
