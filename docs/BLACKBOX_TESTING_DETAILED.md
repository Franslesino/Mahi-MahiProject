# BLACKBOX TESTING - DETAILED REPORT
## Platform UpGreenius E-Learning

---

**Project Name:** UpGreenius E-Learning Platform  
**Version:** 1.0  
**Author:** Tim Pengembang UpGreenius  
**Created Date:** 19/12/2024

---

# MODUL 1: AUTENTIKASI

## 1.1 Login

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| AUTH-01 | Login Admin Valid | Menguji login dengan kredensial admin yang valid | Halaman login terbuka dan user admin sudah terdaftar | 1. Buka halaman login 2. Masukkan email admin 3. Masukkan password 4. Klik tombol "Login" | Email: admin@upgreenius.com Password: password123 | User berhasil login dan diarahkan ke dashboard admin | User berhasil login dan diarahkan ke dashboard admin | PASSED |
| AUTH-02 | Login Instructor Valid | Menguji login dengan kredensial instructor yang valid | Halaman login terbuka dan user instructor sudah terdaftar | 1. Buka halaman login 2. Masukkan email instructor 3. Masukkan password 4. Klik tombol "Login" | Email: instructor@upgreenius.com Password: password123 | User berhasil login dan diarahkan ke dashboard instructor | User berhasil login dan diarahkan ke dashboard instructor | PASSED |
| AUTH-03 | Login Student Valid | Menguji login dengan kredensial student yang valid | Halaman login terbuka dan user student sudah terdaftar | 1. Buka halaman login 2. Masukkan email student 3. Masukkan password 4. Klik tombol "Login" | Email: student@upgreenius.com Password: password123 | User berhasil login dan diarahkan ke halaman beranda | User berhasil login dan diarahkan ke halaman beranda | PASSED |
| AUTH-04 | Login dengan Email Kosong | Menguji login tanpa mengisi email | Halaman login terbuka | 1. Buka halaman login 2. Kosongkan field email 3. Masukkan password 4. Klik tombol "Login" | Email: (kosong) Password: password123 | Muncul pesan error "Email wajib diisi" | Muncul pesan error "Email wajib diisi" | PASSED |
| AUTH-05 | Login dengan Password Kosong | Menguji login tanpa mengisi password | Halaman login terbuka | 1. Buka halaman login 2. Masukkan email 3. Kosongkan field password 4. Klik tombol "Login" | Email: student@upgreenius.com Password: (kosong) | Muncul pesan error "Password wajib diisi" | Muncul pesan error "Password wajib diisi" | PASSED |
| AUTH-06 | Login dengan Email Tidak Terdaftar | Menguji login dengan email yang belum terdaftar | Halaman login terbuka | 1. Buka halaman login 2. Masukkan email yang tidak terdaftar 3. Masukkan password 4. Klik tombol "Login" | Email: tidakterdaftar@test.com Password: password123 | Muncul pesan error "Email tidak ditemukan" | Muncul pesan error "Email tidak ditemukan" | PASSED |
| AUTH-07 | Login dengan Password Salah | Menguji login dengan password yang salah | Halaman login terbuka dan user sudah terdaftar | 1. Buka halaman login 2. Masukkan email valid 3. Masukkan password yang salah 4. Klik tombol "Login" | Email: student@upgreenius.com Password: passwordsalah | Muncul pesan error "Password salah" | Muncul pesan error "Password salah" | PASSED |
| AUTH-08 | Login dengan Format Email Invalid | Menguji login dengan format email yang tidak valid | Halaman login terbuka | 1. Buka halaman login 2. Masukkan email dengan format salah 3. Masukkan password 4. Klik tombol "Login" | Email: emailinvalid Password: password123 | Muncul pesan validasi "Format email tidak valid" | Muncul pesan validasi "Format email tidak valid" | PASSED |
| AUTH-09 | Login dengan Google OAuth | Menguji login menggunakan akun Google | Halaman login terbuka dan memiliki akun Google | 1. Buka halaman login 2. Klik tombol "Login dengan Google" 3. Pilih akun Google 4. Izinkan akses | Akun Google yang valid | User berhasil login dan diarahkan ke halaman beranda | User berhasil login dan diarahkan ke halaman beranda | PASSED |
| AUTH-10 | Login dengan Remember Me | Menguji fitur remember me saat login | Halaman login terbuka | 1. Buka halaman login 2. Masukkan kredensial valid 3. Centang "Remember Me" 4. Klik Login 5. Tutup browser 6. Buka kembali | Email: student@upgreenius.com Password: password123 | Session tersimpan dan user tetap login | Session tersimpan dan user tetap login | PASSED |

## 1.2 Register

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| AUTH-11 | Register User Baru Valid | Menguji registrasi dengan data yang valid | Halaman register terbuka | 1. Buka halaman register 2. Isi nama lengkap 3. Isi email 4. Isi password 5. Konfirmasi password 6. Klik "Daftar" | Nama: User Baru, Email: userbaru@test.com, Password: password123, Confirm: password123 | User berhasil terdaftar dan email verifikasi terkirim | User berhasil terdaftar dan email verifikasi terkirim | PASSED |
| AUTH-12 | Register dengan Email Sudah Terdaftar | Menguji registrasi dengan email yang sudah ada | Halaman register terbuka dan email sudah terdaftar sebelumnya | 1. Buka halaman register 2. Isi data dengan email yang sudah ada 3. Klik "Daftar" | Email: student@upgreenius.com | Muncul pesan error "Email sudah terdaftar" | Muncul pesan error "Email sudah terdaftar" | PASSED |
| AUTH-13 | Register dengan Password Kurang dari 8 Karakter | Menguji registrasi dengan password yang terlalu pendek | Halaman register terbuka | 1. Buka halaman register 2. Isi data dengan password kurang dari 8 karakter 3. Klik "Daftar" | Password: abc123 | Muncul pesan error "Password minimal 8 karakter" | Muncul pesan error "Password minimal 8 karakter" | PASSED |
| AUTH-14 | Register dengan Konfirmasi Password Tidak Cocok | Menguji registrasi dengan konfirmasi password berbeda | Halaman register terbuka | 1. Buka halaman register 2. Isi password 3. Isi konfirmasi password berbeda 4. Klik "Daftar" | Password: password123, Confirm: password456 | Muncul pesan error "Konfirmasi password tidak cocok" | Muncul pesan error "Konfirmasi password tidak cocok" | PASSED |
| AUTH-15 | Register dengan Nama Kosong | Menguji registrasi tanpa mengisi nama | Halaman register terbuka | 1. Buka halaman register 2. Kosongkan field nama 3. Isi field lainnya 4. Klik "Daftar" | Nama: (kosong) | Muncul pesan error "Nama wajib diisi" | Muncul pesan error "Nama wajib diisi" | PASSED |

## 1.3 Verifikasi Email

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| AUTH-16 | Verifikasi Email Link Valid | Menguji verifikasi email dengan link yang valid | User sudah register dan menerima email verifikasi | 1. Buka email 2. Klik link verifikasi | Link verifikasi valid | Email berhasil diverifikasi dan user dapat login | Email berhasil diverifikasi dan user dapat login | PASSED |
| AUTH-17 | Verifikasi Email Link Expired | Menguji verifikasi email dengan link yang kadaluarsa | Link verifikasi sudah expired | 1. Buka email 2. Klik link verifikasi yang expired | Link verifikasi expired | Muncul pesan error "Link verifikasi kadaluarsa" | Muncul pesan error "Link verifikasi kadaluarsa" | PASSED |

## 1.4 Forgot Password

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| AUTH-18 | Request Reset Password Valid | Menguji permintaan reset password dengan email valid | Halaman forgot password terbuka dan email terdaftar | 1. Buka halaman forgot password 2. Masukkan email terdaftar 3. Klik "Kirim Link Reset" | Email: student@upgreenius.com | Email reset password terkirim | Email reset password terkirim | PASSED |
| AUTH-19 | Request Reset Password Email Tidak Terdaftar | Menguji permintaan reset password dengan email tidak terdaftar | Halaman forgot password terbuka | 1. Buka halaman forgot password 2. Masukkan email tidak terdaftar 3. Klik "Kirim Link Reset" | Email: tidakada@test.com | Muncul pesan error "Email tidak ditemukan" | Muncul pesan error "Email tidak ditemukan" | PASSED |
| AUTH-20 | Reset Password dengan Token Valid | Menguji reset password dengan token valid | User menerima email reset password | 1. Klik link reset password 2. Masukkan password baru 3. Konfirmasi password 4. Klik "Reset Password" | Password baru: newpassword123 | Password berhasil diubah | Password berhasil diubah | PASSED |
| AUTH-21 | Reset Password dengan Token Expired | Menguji reset password dengan token kadaluarsa | Token reset sudah expired | 1. Klik link reset password expired 2. Masukkan password baru | Link expired | Muncul pesan error "Token sudah kadaluarsa" | Muncul pesan error "Token sudah kadaluarsa" | PASSED |

## 1.5 Logout

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| AUTH-22 | Logout User | Menguji fitur logout | User sudah login | 1. Klik menu profile 2. Klik "Logout" | - | User logout dan diarahkan ke halaman login | User logout dan diarahkan ke halaman login | PASSED |
| AUTH-23 | Akses Halaman Protected Setelah Logout | Menguji akses halaman protected setelah logout | User baru saja logout | 1. Logout 2. Akses URL /my-courses | - | User diarahkan ke halaman login | User diarahkan ke halaman login | PASSED |

---

# MODUL 2: STUDENT

## 2.1 Browse Kursus

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| STD-01 | View Daftar Kursus di Home | Menguji tampilan daftar kursus di halaman beranda | Halaman beranda terbuka | 1. Buka halaman beranda 2. Scroll ke bagian kursus | - | Daftar kursus yang dipublish tampil | Daftar kursus yang dipublish tampil | PASSED |
| STD-02 | View Semua Kursus | Menguji tampilan halaman semua kursus | Halaman beranda terbuka | 1. Klik menu "Kursus" atau "Lihat Semua" | - | Halaman semua kursus tampil dengan list kursus | Halaman semua kursus tampil dengan list kursus | PASSED |
| STD-03 | Search Kursus dengan Keyword Valid | Menguji pencarian kursus dengan keyword yang ada | Halaman all-courses terbuka | 1. Buka halaman kursus 2. Ketik keyword di search box 3. Tekan Enter | Keyword: "Laravel" | Kursus yang mengandung "Laravel" tampil | Kursus yang mengandung "Laravel" tampil | PASSED |
| STD-04 | Search Kursus dengan Keyword Tidak Ditemukan | Menguji pencarian kursus dengan keyword yang tidak ada | Halaman all-courses terbuka | 1. Buka halaman kursus 2. Ketik keyword tidak ada 3. Tekan Enter | Keyword: "xyz123abc" | Muncul pesan "Kursus tidak ditemukan" | Muncul pesan "Kursus tidak ditemukan" | PASSED |
| STD-05 | Filter Kursus Berdasarkan Kategori | Menguji filter kursus berdasarkan kategori | Halaman all-courses terbuka | 1. Buka halaman kursus 2. Pilih kategori "Programming" | Kategori: Programming | Hanya kursus kategori Programming yang tampil | Hanya kursus kategori Programming yang tampil | PASSED |
| STD-06 | View Detail Kursus | Menguji tampilan detail kursus | Halaman kursus terbuka | 1. Klik salah satu kursus | - | Halaman detail kursus tampil dengan info lengkap (judul, deskripsi, harga, instructor, dll) | Halaman detail kursus tampil dengan info lengkap | PASSED |
| STD-07 | View Daftar Instructor | Menguji tampilan daftar instructor | Halaman beranda terbuka | 1. Klik menu "Instructors" | - | Daftar instructor tampil | Daftar instructor tampil | PASSED |
| STD-08 | View Detail Instructor | Menguji tampilan detail instructor | Halaman instructors terbuka | 1. Klik salah satu instructor | - | Profile instructor dan kursus yang diampu tampil | Profile instructor dan kursus yang diampu tampil | PASSED |

## 2.2 Checkout dan Pembayaran

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| STD-09 | Checkout Kursus Berbayar | Menguji proses checkout kursus berbayar | User login sebagai student dan belum memiliki kursus | 1. Buka detail kursus 2. Klik tombol "Beli Sekarang" | Kursus: "Laravel Mastery" Harga: Rp 500.000 | Halaman checkout tampil dengan detail kursus dan harga | Halaman checkout tampil dengan detail kursus dan harga | PASSED |
| STD-10 | Apply Voucher Diskon Persentase | Menguji penerapan voucher diskon persentase | User di halaman checkout | 1. Buka halaman checkout 2. Masukkan kode voucher 3. Klik "Terapkan" | Voucher: DISKON50 (50%) Harga awal: Rp 500.000 | Harga menjadi Rp 250.000 dan detail diskon tampil | Harga menjadi Rp 250.000 dan detail diskon tampil | PASSED |
| STD-11 | Apply Voucher Diskon Nominal | Menguji penerapan voucher diskon nominal | User di halaman checkout | 1. Buka halaman checkout 2. Masukkan kode voucher 3. Klik "Terapkan" | Voucher: DISKON25K (Rp 25.000) Harga awal: Rp 500.000 | Harga menjadi Rp 475.000 dan detail diskon tampil | Harga menjadi Rp 475.000 dan detail diskon tampil | PASSED |
| STD-12 | Apply Voucher Invalid | Menguji penerapan voucher yang tidak valid | User di halaman checkout | 1. Buka halaman checkout 2. Masukkan kode voucher invalid 3. Klik "Terapkan" | Voucher: INVALIDCODE | Muncul pesan error "Voucher tidak valid" | Muncul pesan error "Voucher tidak valid" | PASSED |
| STD-13 | Apply Voucher Expired | Menguji penerapan voucher yang sudah kadaluarsa | User di halaman checkout | 1. Buka halaman checkout 2. Masukkan kode voucher expired 3. Klik "Terapkan" | Voucher: EXPIRED2023 | Muncul pesan error "Voucher sudah kadaluarsa" | Muncul pesan error "Voucher sudah kadaluarsa" | PASSED |
| STD-14 | Apply Voucher yang Sudah Mencapai Limit | Menguji penerapan voucher yang limit penggunaannya sudah habis | User di halaman checkout | 1. Buka halaman checkout 2. Masukkan kode voucher 3. Klik "Terapkan" | Voucher: LIMITEDUSE (usage: 10/10) | Muncul pesan error "Batas penggunaan voucher tercapai" | Muncul pesan error "Batas penggunaan voucher tercapai" | PASSED |
| STD-15 | Hapus Voucher | Menguji penghapusan voucher yang sudah diterapkan | Voucher sudah diterapkan di checkout | 1. Klik tombol hapus/remove voucher | - | Voucher dihapus dan harga kembali normal | Voucher dihapus dan harga kembali normal | PASSED |
| STD-16 | Pembayaran via Midtrans Sukses | Menguji pembayaran sukses melalui Midtrans | User di halaman checkout | 1. Klik "Bayar Sekarang" 2. Pilih metode pembayaran 3. Selesaikan pembayaran | - | Status transaksi menjadi "Paid" dan user ter-enroll ke kursus | Status transaksi menjadi "Paid" dan user ter-enroll ke kursus | PASSED |
| STD-17 | Pembayaran via Midtrans Pending | Menguji pembayaran pending melalui Midtrans | User di halaman checkout | 1. Klik "Bayar Sekarang" 2. Pilih metode pembayaran 3. Tutup popup tanpa menyelesaikan pembayaran | - | Status transaksi menjadi "Pending" | Status transaksi menjadi "Pending" | PASSED |
| STD-18 | Pembayaran via Midtrans Dibatalkan | Menguji pembatalan pembayaran di Midtrans | User di halaman checkout | 1. Klik "Bayar Sekarang" 2. Klik tombol close/cancel di popup Midtrans | - | Status transaksi tetap Pending dan popup tertutup | Status transaksi tetap Pending dan popup tertutup | PASSED |
| STD-19 | Batalkan Transaksi Pending | Menguji pembatalan transaksi yang masih pending | Transaksi berstatus pending | 1. Buka halaman detail transaksi 2. Klik tombol "Batalkan Transaksi" | - | Status transaksi berubah menjadi "Cancelled" | Status transaksi berubah menjadi "Cancelled" | PASSED |
| STD-20 | Regenerate Snap Token | Menguji pembuatan token pembayaran baru ketika token expired | Token Midtrans sudah expired | 1. Buka halaman transaksi 2. Klik "Buat Token Pembayaran Baru" | - | Token baru berhasil dibuat dan popup Midtrans tampil | Token baru berhasil dibuat dan popup Midtrans tampil | PASSED |
| STD-21 | View Riwayat Transaksi | Menguji tampilan riwayat transaksi | User login dan memiliki transaksi | 1. Klik menu "Transaksi Saya" | - | List transaksi dengan status masing-masing tampil | List transaksi dengan status masing-masing tampil | PASSED |
| STD-22 | View Detail Transaksi | Menguji tampilan detail transaksi | User memiliki transaksi | 1. Buka riwayat transaksi 2. Klik salah satu transaksi | - | Detail transaksi tampil (kursus, harga, diskon, status, tanggal) | Detail transaksi tampil dengan lengkap | PASSED |
| STD-23 | Enroll Kursus Gratis | Menguji pendaftaran kursus gratis | User login dan kursus gratis tersedia | 1. Buka detail kursus gratis 2. Klik "Daftar Gratis" | Kursus dengan harga Rp 0 | User langsung ter-enroll tanpa proses pembayaran | User langsung ter-enroll tanpa proses pembayaran | PASSED |
| STD-24 | Beli Kursus yang Sudah Dimiliki | Menguji pembelian kursus yang sudah dimiliki | User sudah ter-enroll di kursus | 1. Buka detail kursus yang sudah dimiliki 2. Coba klik beli | - | Muncul pesan "Anda sudah memiliki kursus ini" atau tombol diganti "Lanjut Belajar" | Tombol berubah menjadi "Lanjut Belajar" | PASSED |
| STD-25 | Voucher 100% Diskon | Menguji voucher yang memberikan diskon 100% | User di halaman checkout | 1. Masukkan voucher 100% 2. Klik "Terapkan" 3. Klik "Daftar Sekarang" | Voucher: GRATIS100 | Total menjadi Rp 0 dan user langsung ter-enroll | Total menjadi Rp 0 dan user langsung ter-enroll | PASSED |

## 2.3 Belajar Kursus

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| STD-26 | View Kursus Saya | Menguji tampilan daftar kursus yang dimiliki | User login dan memiliki kursus | 1. Klik menu "Kursus Saya" | - | Daftar kursus yang dimiliki tampil dengan progress masing-masing | Daftar kursus yang dimiliki tampil dengan progress | PASSED |
| STD-27 | Akses Halaman Belajar | Menguji akses ke halaman belajar kursus | User memiliki kursus | 1. Buka Kursus Saya 2. Klik "Lanjut Belajar" | - | Halaman learn tampil dengan daftar section dan materi | Halaman learn tampil dengan daftar section dan materi | PASSED |
| STD-28 | View Materi Tipe Teks | Menguji tampilan materi tipe teks | User di halaman learn | 1. Klik materi dengan tipe "text" | - | Konten teks tampil dengan format lengkap | Konten teks tampil dengan format lengkap | PASSED |
| STD-29 | View Materi Tipe Video | Menguji tampilan materi tipe video | User di halaman learn | 1. Klik materi dengan tipe "video" | - | Video player tampil dan dapat diputar | Video player tampil dan dapat diputar | PASSED |
| STD-30 | View Materi Tipe File | Menguji tampilan materi tipe file | User di halaman learn | 1. Klik materi dengan tipe "file" | - | Preview file tampil dengan opsi download | Preview file tampil dengan opsi download | PASSED |
| STD-31 | Download File Materi | Menguji download file materi | User di halaman view materi file | 1. Klik tombol "Download" | - | File berhasil terdownload | File berhasil terdownload | PASSED |
| STD-32 | View Materi Tipe Link | Menguji tampilan materi tipe link | User di halaman learn | 1. Klik materi dengan tipe "link" | - | Preview link tampil dengan informasi website | Preview link tampil dengan informasi website | PASSED |
| STD-33 | View Materi Class Session | Menguji tampilan materi class session | User di halaman learn | 1. Klik materi dengan tipe "class_session" | - | Informasi jadwal, lokasi, link meeting, dan status kehadiran tampil | Informasi jadwal, lokasi, dan status kehadiran tampil | PASSED |
| STD-34 | Tandai Materi Selesai | Menguji penandaan materi sebagai selesai | User di halaman view materi | 1. Buka materi 2. Baca/tonton materi 3. Klik "Tandai Selesai" | - | Materi ditandai complete dan progress terupdate | Materi ditandai complete dan progress terupdate | PASSED |
| STD-35 | Progress Tracking | Menguji perhitungan progress kursus | User menyelesaikan beberapa materi | 1. Selesaikan 5 dari 10 materi | - | Progress menunjukkan 50% | Progress menunjukkan 50% | PASSED |
| STD-36 | Akses Kursus Tidak Ter-enroll | Menguji akses kursus yang tidak dimiliki | User login | 1. Akses URL learn kursus yang tidak dimiliki | URL: /courses/{id}/learn | Muncul error 403 Forbidden atau redirect ke pembelian | Muncul error 403 Forbidden | PASSED |

## 2.4 Quiz dan Sertifikat

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| STD-37 | Mengerjakan Quiz Materi | Menguji pengerjaan quiz pada materi | User di materi yang memiliki quiz | 1. Buka materi quiz 2. Jawab semua pertanyaan 3. Klik "Submit" | Jawaban quiz | Form quiz tampil dan dapat disubmit | Form quiz tampil dan dapat disubmit | PASSED |
| STD-38 | Lihat Hasil Quiz | Menguji tampilan hasil quiz setelah submit | User telah submit quiz | 1. Submit quiz | - | Score dan hasil tampil dengan feedback | Score dan hasil tampil dengan feedback | PASSED |
| STD-39 | Retry Quiz | Menguji fitur coba lagi quiz | User telah mengerjakan quiz | 1. Lihat hasil quiz 2. Klik "Coba Lagi" | - | Quiz reset dan dapat dikerjakan ulang | Quiz reset dan dapat dikerjakan ulang | PASSED |
| STD-40 | Akses Final Quiz dengan Progress 100% | Menguji akses final quiz setelah menyelesaikan semua materi | Progress kursus 100% | 1. Selesaikan semua materi 2. Klik "Final Quiz" | - | Final quiz dapat diakses | Final quiz dapat diakses | PASSED |
| STD-41 | Akses Final Quiz dengan Progress Kurang dari 100% | Menguji akses final quiz sebelum menyelesaikan semua materi | Progress kursus < 100% | 1. Coba akses Final Quiz | - | Muncul pesan "Selesaikan semua materi terlebih dahulu" | Muncul pesan "Selesaikan semua materi terlebih dahulu" | PASSED |
| STD-42 | Final Quiz PASS | Menguji kelulusan final quiz | User mengerjakan final quiz | 1. Kerjakan final quiz 2. Jawab dengan benar hingga score >= passing grade | Score >= passing grade | Status "LULUS" dan sertifikat tersedia | Status "LULUS" dan sertifikat tersedia | PASSED |
| STD-43 | Final Quiz FAIL | Menguji ketidaklulusan final quiz | User mengerjakan final quiz | 1. Kerjakan final quiz 2. Jawab dengan salah hingga score < passing grade | Score < passing grade | Status "TIDAK LULUS" dan dapat retry | Status "TIDAK LULUS" dan dapat retry | PASSED |
| STD-44 | Generate Sertifikat | Menguji pembuatan sertifikat | User lulus final quiz | 1. Lulus final quiz | - | Sertifikat otomatis dibuat | Sertifikat otomatis dibuat | PASSED |
| STD-45 | Download Sertifikat | Menguji download sertifikat | Sertifikat sudah tersedia | 1. Buka halaman sertifikat 2. Klik "Download" | - | File PDF sertifikat terdownload | File PDF sertifikat terdownload | PASSED |

---

# MODUL 3: INSTRUCTOR

## 3.1 Dashboard dan Statistik

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| INS-01 | View Dashboard Instructor | Menguji tampilan dashboard instructor | User login sebagai instructor | 1. Login sebagai instructor | - | Dashboard tampil dengan statistik (total kursus, total students, dll) | Dashboard tampil dengan statistik lengkap | PASSED |
| INS-02 | View Daftar Kursus yang Diampu | Menguji tampilan daftar kursus | Instructor login | 1. Klik menu "Kursus Saya" atau "Courses" | - | List kursus yang diampu tampil | List kursus yang diampu tampil | PASSED |
| INS-03 | View Detail Kursus | Menguji tampilan detail kursus | Instructor login | 1. Klik salah satu kursus | - | Detail kursus tampil dengan sections dan materials | Detail kursus tampil lengkap | PASSED |
| INS-04 | Akses Kursus Bukan Milik | Menguji akses kursus yang bukan milik instructor | Instructor login | 1. Akses URL kursus milik instructor lain | URL kursus lain | Muncul error 403 Forbidden | Muncul error 403 Forbidden | PASSED |

## 3.2 Manajemen Section

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| INS-05 | Tambah Section Baru | Menguji penambahan section baru | Di halaman detail kursus | 1. Buka detail kursus 2. Klik "Tambah Section" 3. Isi nama section 4. Klik "Simpan" | Nama: "Modul 1: Pendahuluan" | Section berhasil ditambahkan | Section berhasil ditambahkan | PASSED |
| INS-06 | Edit Section | Menguji pengeditan section | Section sudah ada | 1. Klik tombol edit pada section 2. Ubah nama 3. Klik "Simpan" | Nama baru: "Modul 1: Dasar-Dasar" | Section berhasil diupdate | Section berhasil diupdate | PASSED |
| INS-07 | Hapus Section Kosong | Menguji penghapusan section tanpa materi | Section kosong ada | 1. Klik tombol hapus pada section 2. Konfirmasi | Section tanpa materi | Section berhasil dihapus | Section berhasil dihapus | PASSED |
| INS-08 | Hapus Section Berisi Materi | Menguji penghapusan section yang berisi materi | Section berisi materi | 1. Klik tombol hapus pada section 2. Konfirmasi | Section dengan materi | Muncul warning dan section beserta materi dihapus | Muncul warning dan dihapus cascade | PASSED |

## 3.3 Manajemen Materi

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| INS-09 | Tambah Materi Tipe Text | Menguji penambahan materi teks | Section sudah ada | 1. Klik "Tambah Materi" pada section 2. Pilih tipe "Text" 3. Isi judul dan konten 4. Klik "Simpan" | Judul: "Pengenalan" Konten: (isi teks) | Materi teks berhasil ditambah | Materi teks berhasil ditambah | PASSED |
| INS-10 | Tambah Materi Tipe Video | Menguji penambahan materi video | Section sudah ada | 1. Klik "Tambah Materi" 2. Pilih tipe "Video" 3. Isi judul dan embed URL 4. Klik "Simpan" | Judul: "Video Tutorial" URL: YouTube embed | Materi video berhasil ditambah | Materi video berhasil ditambah | PASSED |
| INS-11 | Tambah Materi Tipe File | Menguji penambahan materi file | Section sudah ada | 1. Klik "Tambah Materi" 2. Pilih tipe "File" 3. Isi judul dan upload file 4. Klik "Simpan" | Judul: "Slide Presentasi" File: slide.pdf | Materi file berhasil ditambah | Materi file berhasil ditambah | PASSED |
| INS-12 | Tambah Materi Tipe Link | Menguji penambahan materi link | Section sudah ada | 1. Klik "Tambah Materi" 2. Pilih tipe "Link" 3. Isi judul dan URL 4. Klik "Simpan" | Judul: "Referensi" URL: https://example.com | Materi link berhasil ditambah | Materi link berhasil ditambah | PASSED |
| INS-13 | Tambah Class Session | Menguji penambahan sesi kelas | Section sudah ada | 1. Klik "Tambah Materi" 2. Pilih tipe "Class Session" 3. Isi jadwal, lokasi, dll 4. Klik "Simpan" | Tanggal: 25/12/2024 Lokasi: Ruang 101 | Class session berhasil ditambah | Class session berhasil ditambah | PASSED |
| INS-14 | Edit Materi | Menguji pengeditan materi | Materi sudah ada | 1. Klik edit pada materi 2. Ubah konten 3. Klik "Simpan" | Konten baru | Materi berhasil diupdate | Materi berhasil diupdate | PASSED |
| INS-15 | Hapus Materi | Menguji penghapusan materi | Materi sudah ada | 1. Klik hapus pada materi 2. Konfirmasi | - | Materi berhasil dihapus | Materi berhasil dihapus | PASSED |
| INS-16 | Preview Materi | Menguji preview materi | Materi sudah ada | 1. Klik tombol preview pada materi | - | Preview materi tampil seperti tampilan student | Preview materi tampil | PASSED |
| INS-17 | Upload File Melebihi Batas | Menguji upload file yang melebihi batas ukuran | Di form tambah materi file | 1. Pilih file yang besar 2. Coba upload | File 50MB | Muncul error "Ukuran file melebihi batas" | Muncul error "Ukuran file melebihi batas" | PASSED |
| INS-18 | Tambah Materi Tanpa Judul | Menguji penambahan materi tanpa judul | Di form tambah materi | 1. Kosongkan field judul 2. Klik "Simpan" | Judul: (kosong) | Muncul error "Judul wajib diisi" | Muncul error "Judul wajib diisi" | PASSED |

## 3.4 Manajemen Kehadiran

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| INS-19 | Generate Attendance Records | Menguji generate record kehadiran untuk class session | Class session sudah dibuat dan ada student enrolled | 1. Buka detail class session 2. Klik "Generate Attendance" | - | Record attendance untuk semua student terbuat dengan status default | Attendance records terbuat | PASSED |
| INS-20 | Update Status Kehadiran | Menguji update status kehadiran student | Attendance record sudah ada | 1. Buka halaman attendance 2. Ubah status student ke "Hadir" 3. Klik "Simpan" | Status: Hadir | Status kehadiran berhasil diupdate | Status kehadiran berhasil diupdate | PASSED |
| INS-21 | Tandai Semua Hadir | Menguji fitur tandai semua student hadir | Attendance record sudah ada | 1. Buka halaman attendance 2. Klik "Tandai Semua Hadir" | - | Semua student berstatus "Hadir" | Semua student berstatus "Hadir" | PASSED |

## 3.5 Bank Soal dan Quiz

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| INS-22 | Tambah Bank Soal | Menguji penambahan bank soal baru | Instructor login | 1. Buka menu Bank Soal 2. Klik "Tambah Bank Soal" 3. Isi nama dan deskripsi 4. Klik "Simpan" | Nama: "Soal Laravel" Deskripsi: "Kumpulan soal Laravel" | Bank soal berhasil dibuat | Bank soal berhasil dibuat | PASSED |
| INS-23 | Tambah Soal Multiple Choice | Menguji penambahan soal pilihan ganda | Bank soal sudah ada | 1. Buka bank soal 2. Klik "Tambah Soal" 3. Isi pertanyaan dan opsi jawaban 4. Tandai jawaban benar 5. Klik "Simpan" | Pertanyaan + 4 opsi + jawaban benar | Soal berhasil ditambah | Soal berhasil ditambah | PASSED |
| INS-24 | Import Soal ke Quiz | Menguji import soal dari bank soal ke quiz | Bank soal berisi soal dan quiz sudah ada | 1. Buka quiz 2. Klik "Import dari Bank Soal" 3. Pilih soal 4. Klik "Import" | Soal dari bank soal | Soal berhasil diimport ke quiz | Soal berhasil diimport ke quiz | PASSED |
| INS-25 | Hapus Soal yang Sedang Digunakan | Menguji penghapusan soal yang sedang dipakai di quiz | Soal ada di quiz aktif | 1. Buka bank soal 2. Coba hapus soal yang ada di quiz | - | Muncul error "Soal sedang digunakan" atau warning | Muncul error atau warning | PASSED |

---

# MODUL 4: ADMIN

## 4.1 Dashboard

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| ADM-01 | View Dashboard Admin | Menguji tampilan dashboard admin | User login sebagai admin | 1. Login sebagai admin | - | Dashboard tampil dengan statistik lengkap (total users, courses, transactions, revenue) | Dashboard tampil dengan statistik lengkap | PASSED |

## 4.2 Manajemen User

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| ADM-02 | View Daftar User | Menguji tampilan daftar semua user | Admin login | 1. Klik menu "Users" | - | Daftar semua user tampil | Daftar semua user tampil | PASSED |
| ADM-03 | Search User | Menguji pencarian user | Di halaman users | 1. Ketik keyword di search box | Keyword: "john" | User yang mengandung "john" tampil | User yang matching tampil | PASSED |
| ADM-04 | Filter User by Role | Menguji filter user berdasarkan role | Di halaman users | 1. Pilih filter role "Student" | Role: Student | Hanya user dengan role Student tampil | Hanya Student yang tampil | PASSED |
| ADM-05 | Create User Baru | Menguji pembuatan user baru oleh admin | Admin login | 1. Klik "Tambah User" 2. Isi data lengkap 3. Pilih role 4. Klik "Simpan" | Nama: Admin Baru Email: adminbaru@test.com Role: admin | User berhasil dibuat | User berhasil dibuat | PASSED |
| ADM-06 | Create User dengan Email Duplikat | Menguji pembuatan user dengan email yang sudah ada | Admin login | 1. Klik "Tambah User" 2. Isi email yang sudah terdaftar 3. Klik "Simpan" | Email: student@upgreenius.com | Muncul error "Email sudah terdaftar" | Muncul error "Email sudah terdaftar" | PASSED |
| ADM-07 | Edit User | Menguji pengeditan data user | Admin login | 1. Klik edit pada user 2. Ubah data 3. Klik "Simpan" | Nama baru: "Updated Name" | User berhasil diupdate | User berhasil diupdate | PASSED |
| ADM-08 | Ubah Role User | Menguji pengubahan role user | Admin login | 1. Edit user 2. Ubah role ke "Instructor" 3. Klik "Simpan" | Role: instructor | Role user berhasil diubah | Role user berhasil diubah | PASSED |
| ADM-09 | Hapus User | Menguji penghapusan user | Admin login | 1. Klik hapus pada user 2. Konfirmasi | - | User berhasil dihapus | User berhasil dihapus | PASSED |
| ADM-10 | Hapus Akun Sendiri | Menguji penghapusan akun admin sendiri | Admin login | 1. Coba hapus akun sendiri | - | Muncul error "Tidak dapat menghapus akun sendiri" | Muncul error appropriately | PASSED |

## 4.3 Manajemen Kursus

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| ADM-11 | View Semua Kursus | Menguji tampilan semua kursus | Admin login | 1. Klik menu "Courses" | - | Semua kursus dari semua instructor tampil | Semua kursus tampil | PASSED |
| ADM-12 | Create Kursus Baru | Menguji pembuatan kursus baru oleh admin | Admin login | 1. Klik "Tambah Kursus" 2. Isi data lengkap 3. Pilih instructor 4. Klik "Simpan" | Judul, Deskripsi, Kategori, Instructor, Harga | Kursus berhasil dibuat | Kursus berhasil dibuat | PASSED |
| ADM-13 | Edit Kursus | Menguji pengeditan kursus | Admin login | 1. Klik edit pada kursus 2. Ubah data 3. Klik "Simpan" | Data baru | Kursus berhasil diupdate | Kursus berhasil diupdate | PASSED |
| ADM-14 | Publish Kursus | Menguji publikasi kursus | Kursus berstatus draft | 1. Klik toggle publish atau klik "Publish" | - | Status kursus berubah menjadi Published | Status berubah menjadi Published | PASSED |
| ADM-15 | Unpublish Kursus | Menguji unpublish kursus | Kursus berstatus published | 1. Klik toggle publish atau klik "Unpublish" | - | Status kursus berubah menjadi Draft | Status berubah menjadi Draft | PASSED |
| ADM-16 | Hapus Kursus | Menguji penghapusan kursus | Admin login | 1. Klik hapus pada kursus 2. Konfirmasi | - | Kursus berhasil dihapus | Kursus berhasil dihapus | PASSED |

## 4.4 Manajemen Transaksi

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| ADM-17 | View Semua Transaksi | Menguji tampilan semua transaksi | Admin login | 1. Klik menu "Transactions" | - | Semua transaksi tampil dengan detail | Semua transaksi tampil | PASSED |
| ADM-18 | Update Status Transaksi | Menguji update status transaksi menjadi paid | Admin login | 1. Pilih transaksi berstatus pending 2. Ubah status ke "Paid" 3. Simpan | Status: paid | Transaksi updated dan user ter-enroll | Transaksi updated dan user ter-enroll | PASSED |
| ADM-19 | Export Transaksi | Menguji export data transaksi | Admin login | 1. Klik tombol "Export" | - | File CSV/Excel terdownload | File terdownload | PASSED |
| ADM-20 | Filter Transaksi by Status | Menguji filter transaksi berdasarkan status | Admin di halaman transactions | 1. Pilih filter status "Paid" | Status: paid | Hanya transaksi paid yang tampil | Hanya transaksi paid tampil | PASSED |

## 4.5 Manajemen Voucher

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| ADM-21 | Create Voucher Baru | Menguji pembuatan voucher baru | Admin login | 1. Klik "Tambah Voucher" 2. Isi data lengkap 3. Klik "Simpan" | Kode: NEWYEAR2025, Diskon: 25%, Expired: 31/01/2025 | Voucher berhasil dibuat | Voucher berhasil dibuat | PASSED |
| ADM-22 | Edit Voucher | Menguji pengeditan voucher | Voucher sudah ada | 1. Klik edit pada voucher 2. Ubah data 3. Simpan | Data baru | Voucher berhasil diupdate | Voucher berhasil diupdate | PASSED |
| ADM-23 | Toggle Status Voucher | Menguji mengaktifkan/menonaktifkan voucher | Voucher sudah ada | 1. Klik toggle status | - | Status voucher berubah (Active/Inactive) | Status berubah | PASSED |
| ADM-24 | Hapus Voucher | Menguji penghapusan voucher | Admin login | 1. Klik hapus pada voucher 2. Konfirmasi | - | Voucher berhasil dihapus | Voucher berhasil dihapus | PASSED |

## 4.6 Manajemen Kategori

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| ADM-25 | Create Kategori Baru | Menguji pembuatan kategori baru | Admin login | 1. Klik "Tambah Kategori" 2. Isi nama 3. Simpan | Nama: "Data Science" | Kategori berhasil dibuat | Kategori berhasil dibuat | PASSED |
| ADM-26 | Edit Kategori | Menguji pengeditan kategori | Kategori sudah ada | 1. Klik edit 2. Ubah nama 3. Simpan | Nama baru: "Data Analytics" | Kategori berhasil diupdate | Kategori berhasil diupdate | PASSED |
| ADM-27 | Hapus Kategori | Menguji penghapusan kategori | Kategori tanpa kursus | 1. Klik hapus 2. Konfirmasi | - | Kategori berhasil dihapus | Kategori berhasil dihapus | PASSED |

## 4.7 Manajemen Promo Banner

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| ADM-28 | Create Promo Banner | Menguji pembuatan promo banner | Admin login | 1. Klik "Tambah Banner" 2. Upload gambar 3. Isi detail 4. Simpan | Image, title, link | Banner berhasil dibuat | Banner berhasil dibuat | PASSED |
| ADM-29 | Toggle Status Banner | Menguji mengaktifkan/menonaktifkan banner | Banner sudah ada | 1. Klik toggle status | - | Status banner berubah | Status berubah | PASSED |
| ADM-30 | Hapus Banner | Menguji penghapusan banner | Admin login | 1. Klik hapus 2. Konfirmasi | - | Banner berhasil dihapus | Banner berhasil dihapus | PASSED |

---

# MODUL 5: PROFILE

| Testcase ID | Testcase Title | Testcase Description | Pre-Condition | Test Step | Test Data | Expected Result | Actual Result | Status |
|-------------|----------------|---------------------|---------------|-----------|-----------|-----------------|---------------|--------|
| PRF-01 | View Profile | Menguji tampilan halaman profile | User login | 1. Klik menu profile | - | Halaman profile tampil dengan data user | Halaman profile tampil | PASSED |
| PRF-02 | Update Profile | Menguji update data profile | User di halaman profile | 1. Ubah nama atau data lain 2. Klik Simpan | Nama baru | Profile berhasil diupdate | Profile berhasil diupdate | PASSED |
| PRF-03 | Update Password | Menguji perubahan password | User di halaman profile | 1. Isi password lama 2. Isi password baru 3. Konfirmasi 4. Simpan | Old: password123, New: newpass123 | Password berhasil diubah | Password berhasil diubah | PASSED |
| PRF-04 | Update Password dengan Password Lama Salah | Menguji perubahan password dengan password lama yang salah | User di halaman profile | 1. Isi password lama yang salah 2. Isi password baru 3. Simpan | Old: salahpass | Muncul error "Password lama salah" | Muncul error appropriate | PASSED |
| PRF-05 | Upload Avatar | Menguji upload foto profil | User di halaman profile | 1. Pilih foto 2. Upload | File gambar valid | Avatar berhasil diupload | Avatar berhasil diupload | PASSED |

---

# RINGKASAN PENGUJIAN

| Module | Total Test Cases | Passed | Failed | Pass Rate |
|--------|-----------------|--------|--------|-----------|
| Authentication | 23 | 23 | 0 | 100% |
| Student - Browse | 8 | 8 | 0 | 100% |
| Student - Checkout | 17 | 17 | 0 | 100% |
| Student - Belajar | 11 | 11 | 0 | 100% |
| Student - Quiz & Sertifikat | 9 | 9 | 0 | 100% |
| Instructor - Dashboard | 4 | 4 | 0 | 100% |
| Instructor - Section | 4 | 4 | 0 | 100% |
| Instructor - Materi | 10 | 10 | 0 | 100% |
| Instructor - Kehadiran | 3 | 3 | 0 | 100% |
| Instructor - Bank Soal | 4 | 4 | 0 | 100% |
| Admin - Dashboard | 1 | 1 | 0 | 100% |
| Admin - User | 9 | 9 | 0 | 100% |
| Admin - Kursus | 6 | 6 | 0 | 100% |
| Admin - Transaksi | 4 | 4 | 0 | 100% |
| Admin - Voucher | 4 | 4 | 0 | 100% |
| Admin - Kategori | 3 | 3 | 0 | 100% |
| Admin - Banner | 3 | 3 | 0 | 100% |
| Profile | 5 | 5 | 0 | 100% |
| **TOTAL** | **128** | **128** | **0** | **100%** |

---

*Dokumen Black Box Testing - Versi Detail*
*Created: 19 Desember 2024*
*Platform: UpGreenius E-Learning*
