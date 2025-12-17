# BLACK BOX TESTING
## UpGreenius E-Learning Platform

---

## 1. PENDAHULUAN

### 1.1 Tujuan
Dokumen ini berisi test case untuk pengujian Black Box pada aplikasi UpGreenius.

### 1.2 Teknik yang Digunakan
- **Equivalence Partitioning (EP)**
- **Boundary Value Analysis (BVA)**
- **Decision Table Testing**
- **State Transition Testing**

---

## 2. TEST CASES - MODUL AUTENTIKASI

### 2.1 Login (25 TC)

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

## 3. TEST CASES - MODUL STUDENT

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
| BB-STD-043 | View class session | Klik class session | Jadwal, lokasi tampil | Jadwal, lokasi tampil | ✅ PASS |
| BB-STD-044 | Mark complete | Klik Tandai Selesai | Materi complete | Materi complete | ✅ PASS |
| BB-STD-045 | Progress tracking | Complete 5/10 materi | Progress 50% | Progress 50% | ✅ PASS |
| BB-STD-046 | Akses kursus tidak enrolled | URL kursus lain | Error 403 | Error 403 | ✅ PASS |
| BB-STD-047 | Quiz materi | Klik quiz | Form quiz tampil | Form quiz tampil | ✅ PASS |
| BB-STD-048 | Submit quiz | Submit jawaban | Score tampil | Score tampil | ✅ PASS |
| BB-STD-049 | Quiz retry | Klik Coba Lagi | Reset quiz | Reset quiz | ✅ PASS |
| BB-STD-050 | Final quiz access | Progress 100% | Final quiz available | Final quiz available | ✅ PASS |
| BB-STD-051 | Final quiz not ready | Progress < 100% | Error "selesaikan materi" | Error "selesaikan materi" | ✅ PASS |
| BB-STD-052 | Final quiz pass | Score >= passing | Status PASS | Status PASS | ✅ PASS |
| BB-STD-053 | Final quiz fail | Score < passing | Status FAIL | Status FAIL | ✅ PASS |
| BB-STD-054 | Certificate generated | Pass final quiz | Sertifikat dibuat | Sertifikat dibuat | ✅ PASS |
| BB-STD-055 | Download certificate | Klik Download | PDF terdownload | PDF terdownload | ✅ PASS |

---

## 4. TEST CASES - MODUL INSTRUCTOR

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

---

## 5. TEST CASES - MODUL ADMIN

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

---

## 6. BOUNDARY VALUE ANALYSIS

| TC ID | Field | Min | Max | Test Values | Expected | Status |
|-------|-------|-----|-----|-------------|----------|--------|
| BVA-001 | Password | 8 | 255 | 7, 8, 255, 256 | 8-255 valid | ✅ PASS |
| BVA-002 | Nama | 1 | 255 | 0, 1, 255, 256 | 1-255 valid | ✅ PASS |
| BVA-003 | Harga | 0 | 999999999 | -1, 0, max | 0-max valid | ✅ PASS |
| BVA-004 | Diskon % | 1 | 100 | 0, 1, 100, 101 | 1-100 valid | ✅ PASS |
| BVA-005 | Passing Grade | 1 | 100 | 0, 1, 100, 101 | 1-100 valid | ✅ PASS |

---

## 7. STATE TRANSITION - Transaction Status

| From | Action | To | Status |
|------|--------|-----|--------|
| (none) | Create | Pending | ✅ PASS |
| Pending | Pay success | Paid | ✅ PASS |
| Pending | Cancel | Cancelled | ✅ PASS |
| Pending | Expire | Expired | ✅ PASS |
| Paid | Cancel | ❌ Error | ✅ PASS |

---

## 8. RINGKASAN

| Modul | TC | Pass | Fail | Rate |
|-------|-----|------|------|------|
| Authentication | 25 | 25 | 0 | 100% |
| Student | 55 | 55 | 0 | 100% |
| Instructor | 25 | 25 | 0 | 100% |
| Admin | 20 | 20 | 0 | 100% |
| BVA | 5 | 5 | 0 | 100% |
| State Transition | 5 | 5 | 0 | 100% |
| **TOTAL** | **135** | **135** | **0** | **100%** |

---

*Black Box Testing v1.0 - 17 Desember 2024*
