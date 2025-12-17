# TEST PLAN DOCUMENT  
# Mahi-MahiProject
Version 0.1 (17 Desember 2025)

1 TEST PLAN IDENTIFIER  
- TP-MAHI-0.1-2025-12-17

2 REFERENCES  
- routes/web.php (daftar fitur & peran)  
- docs/VOUCHER_GUIDE.md, docs/PROMO_BANNER_GUIDE.md, docs/FINAL_QUIZ_GUIDE.md  
- .env.example (konfigurasi Midtrans, OAuth Google, storage)  
- phpunit.xml (konfigurasi test suite)

3 INTRODUCTION  
- Tujuan: memastikan platform kursus daring (peran admin, instruktur, student) berjalan stabil untuk katalog kursus, pembelajaran, kuis, sertifikasi, dan pembayaran Midtrans.  
- Cakupan: pengujian fungsional web (Laravel), API internal, integrasi Midtrans sandbox, dan alur UI utama.

4 TEST ITEMS  
- Autentikasi & otorisasi: login/register manual + Google OAuth, reset password, verifikasi email, middleware role (admin/instructor/student), nocache.  
- Katalog publik: home & all-courses (search, filter kategori, pagination), daftar instruktur & detailnya, terms.  
- Kursus & materi (student): detail kursus, modul/materi (video/file/quiz), progress & completion, download, final quiz, sertifikat (preview/stream/download), notifikasi in-app.  
- Transaksi & pembayaran: checkout, snap token, callback Midtrans (finish/unfinish/error), status transaksi, voucher/promo banner, regenerasi token, ekspor transaksi, my-transactions.  
- Panel admin: dashboard, CRUD user, kursus (modules/materials/preview), jadwal offline/hybrid, assignment/quiz, final quiz, question bank, voucher/promo, transaksi.  
- Panel instruktur: dashboard, profil, kursus & materi, question bank, assignment, final quiz, jadwal, notifikasi.  
- Profil pengguna: update data, password, avatar, hapus avatar.

5 SOFTWARE RISK ISSUES  
- Inkonsistensi status pembayaran (callback vs UI) atau snap token kedaluwarsa.  
- Bypass otorisasi role pada akses kursus/materi atau download sertifikat.  
- Kerusakan data saat hapus modul/materi (orphans) atau import soal.  
- File upload/preview tidak aman (tipe/ukuran) dan akses langsung ke storage.  
- Duplikasi atau nomor sertifikat salah; progress tidak tersimpan.  
- Kinerja pencarian/paginasi katalog dan polling notifikasi.

6 FEATURES TO BE TESTED  
- Autentikasi: login/register, Google OAuth callback, reset password, verifikasi email, logout, throttle.  
- Role & akses: middleware role di semua prefix (admin/, instructor/, student/), larangan akses kursus non-published.  
- Katalog: search & filter kategori, hitung materi/video, banner promo aktif, pagination konsisten.  
- Kursus & materi: lihat/preview/unduh materi, tandai selesai, quiz per materi, final quiz attempt/result, batasan ketika belum enroll, progress di halaman learn/my-courses.  
- Sertifikat: generate saat kursus selesai, nomor & tanggal terbit, preview JSON, stream/download hanya pemilik.  
- Transaksi: alur checkout → process → midtrans callbacks → status/receipt, voucher valid/invalid, perhitungan diskon, regenerate snap token, cancel/confirm, debug response, ekspor transaksi admin.  
- Panel admin: CRUD user/kursus, modules/materials CRUD, jadwal, assignment/quiz publish/unpublish, bank soal impor/ekspor, final quiz config/toggle, vouchers/promo toggle, notifications actions.  
- Panel instruktur: CRUD materi & section, question bank, assignment, final quiz (edit/update/toggle), jadwal, notifikasi (poll/read).  
- Profil: edit data & password, hapus avatar, validasi format.  
- API webhook: /api/midtrans/notification validasi signature & idempotency.

7 FEATURES NOT TO BE TESTED  
- Keandalan layanan pihak ketiga (Midtrans, Google OAuth) di luar sandbox; performa CDN eksternal.  
- Compatibilitas browser legacy (IE) kecuali diminta; perangkat mobile lama di luar daftar target.  
- Uji penetrasi penuh atau audit keamanan mendalam (di luar scope test fungsional).  
- Uji beban skala besar kecuali ada jadwal terpisah.

8 APPROACH (STRATEGY)  
- Tim: 1 QA lead, 1-2 QA engineer, developer sebagai SME modul.  
- Lingkungan: staging dengan database terpisah, Midtrans sandbox, storage local/MinIO; seed data kursus/instruktur/user uji.  
- Teknik: equivalence & boundary untuk form, state-transition untuk status transaksi, path coverage untuk middleware & callbacks.  
- Tingkat pengujian:  
  - Unit: model, helper, service (voucher/promo, perhitungan harga, nomor sertifikat, progress).  
  - Feature/API: HTTP test untuk route student/admin/instructor, webhook Midtrans, akses kontrol.  
  - UI manual: skenario end-to-end per peran (lihat bagian “BLACK BOX” & “SYSTEM TESTING”).  
- Otomasi: jalankan `php artisan test` atau `./vendor/bin/phpunit`; siapkan data dengan factory/seed; stub Midtrans callback di test.  
- Pelaporan: test case di sheet/tool, bug di tracker; daily smoke pada build terbaru.  
- Jadwal ringkas:  
  - Minggu 1: siapkan environment & seed, susun test case.  
  - Minggu 2: unit/feature tests, smoke katalog & auth.  
  - Minggu 3: end-to-end transaksi/sertifikat, admin/instruktur panel.  
  - Minggu 4: regression, UAT, SUS.

9 ITEM PASS/FAIL CRITERIA  
- Pass: hasil aktual = ekspektasi, tidak ada error/exception, data tersimpan benar, log/callback tercatat.  
- Fail: deviasi fungsional, error 4xx/5xx tak terduga, data korup/hilang, regressions pada alur kritis (auth, pembayaran, sertifikat).  
- Defect severity: blocker (tidak bisa belajar/bayar), critical (kehilangan data/akses ilegal), major (fungsi utama terhambat), minor (UI/teks).

TAMPILKAN HASIL (jenis pengujian & sasaran)
- UNIT TESTING: model Kursus/Enrollment/Voucher/Sertifikat (harga akhir, status pendaftaran, nomor sertifikat unik), service Midtrans (payload/signature), helper progress & completion.  
- WHITE BOX TESTING: middleware role/auth, guard download sertifikat, kontrol alur callback Midtrans (branch status), kalkulasi jadwal/quiz publish, impor/ekspor bank soal.  
- BLACK BOX TESTING:  
  - Student: daftar kursus → detail → enroll (voucher valid/invalid) → bayar → belajar materi (complete) → quiz → final quiz → sertifikat preview/download → notifikasi terbaca.  
  - Instructor: login → buat kursus + modul/materi → publish → buat assignment/quiz → impor soal → lihat statistik final quiz.  
  - Admin: CRUD user/kursus, toggle promo/voucher, update status transaksi, ekspor transaksi, hapus materi (cek orphan).  
- SYSTEM TESTING: alur ujung-ke-ujung lintas peran (admin publish kursus → instruktur tambah materi → student beli & selesai → sertifikat terbit), rollback pembayaran gagal, regenerasi snap token, timeout callback.  
- AUTOMATION TESTING: jalankan regression `php artisan test`; tambahkan feature test untuk voucher validate, Midtrans webhook, akses kursus tanpa role, sertifikat preview JSON, pagination katalog.  
- ACCEPTANCE TESTING: uji terhadap requirement kursus & pembayaran; siapkan Acceptance Test Report dengan hasil UAT dan daftar blokir go-live.  
- SUS TESTING: survei 10–15 pengguna (peran student/instructor/admin) setelah UAT; target skor ≥ 75, catat keluhan navigasi, performa, kejelasan status pembayaran.
