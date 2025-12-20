# ANALISIS PRINSIP BELMONT
## Platform E-Learning UpGreenius

**Tanggal:** 21 Desember 2024  
**Versi Dokumen:** 1.0

---

## 1. PENDAHULUAN

### 1.1 Tentang Prinsip Belmont
Prinsip Belmont adalah pedoman etika penelitian yang melibatkan subjek manusia, diterbitkan pada tahun 1979 oleh National Commission for the Protection of Human Subjects. Prinsip ini terdiri dari **3 prinsip utama**:

1. **Respect for Persons** (Penghormatan terhadap Individu)
2. **Beneficence** (Kemanfaatan)
3. **Justice** (Keadilan)

### 1.2 Tentang UpGreenius
UpGreenius adalah platform e-learning berbasis web dengan karakteristik:
- **3 Role Pengguna:** Admin, Instructor, Student
- **Framework:** Laravel 10.x dengan PostgreSQL
- **Integrasi:** Midtrans (Pembayaran), Google OAuth, Email Verification
- **Testing:** SUS Testing dengan 15 responden

---

## 2. ANALISIS PRINSIP BELMONT

### 2.1 RESPECT FOR PERSONS (Penghormatan terhadap Individu)

> *"Individu harus diperlakukan sebagai agen otonom dan orang dengan otonomi yang berkurang berhak mendapat perlindungan."*

| Aspek | Implementasi di UpGreenius | Status |
|-------|---------------------------|--------|
| **Informed Consent** | ✅ User harus register dan menyetujui syarat sebelum menggunakan platform | ✅ Terpenuhi |
| **Verifikasi Email** | ✅ Email wajib diverifikasi sebelum akun aktif | ✅ Terpenuhi |
| **Kontrol Data Pribadi** | ✅ User dapat mengedit profil dan menghapus akun | ✅ Terpenuhi |
| **Transparansi** | ✅ Informasi kursus, harga, dan syarat kelulusan ditampilkan jelas | ✅ Terpenuhi |
| **Hak Menolak** | ✅ User dapat membatalkan transaksi sebelum pembayaran | ✅ Terpenuhi |

**Bukti Implementasi:**
- Login dengan Google OAuth memberikan pilihan kepada user
- Progress kursus dapat dihentikan kapan saja
- User tidak dipaksa menyelesaikan quiz dalam sekali duduk (progress tersimpan)

**Skor: 5/5** ✅

---

### 2.2 BENEFICENCE (Kemanfaatan)

> *"Memaksimalkan manfaat dan meminimalkan risiko/kerugian bagi subjek."*

| Aspek | Implementasi di UpGreenius | Status |
|-------|---------------------------|--------|
| **Manfaat Edukasi** | ✅ Platform menyediakan materi belajar beragam (video, text, file, quiz) | ✅ Terpenuhi |
| **Sertifikasi** | ✅ Sertifikat diberikan setelah lulus final quiz | ✅ Terpenuhi |
| **Progress Tracking** | ✅ User dapat melihat kemajuan belajar mereka | ✅ Terpenuhi |
| **Keamanan Data** | ✅ Password di-hash, file disimpan di cloud (Supabase) | ✅ Terpenuhi |
| **Keamanan Transaksi** | ✅ Pembayaran via Midtrans (gateway terpercaya) | ✅ Terpenuhi |
| **Minimalisasi Risiko** | ✅ Validasi input untuk mencegah serangan (XSS, SQL Injection) | ✅ Terpenuhi |

**Manfaat yang Diberikan:**
1. Akses pendidikan berkualitas
2. Fleksibilitas belajar (kapan saja, di mana saja)
3. Sertifikasi sebagai bukti kompetensi
4. Kesempatan percobaan ulang quiz

**Risiko yang Diminimalkan:**
1. Password tidak disimpan plain text
2. File tidak langsung disimpan di server lokal (cloud storage)
3. Transaksi melalui payment gateway resmi

**Skor: 6/6** ✅

---

### 2.3 JUSTICE (Keadilan)

> *"Distribusi manfaat dan beban penelitian secara adil."*

| Aspek | Implementasi di UpGreenius | Status |
|-------|---------------------------|--------|
| **Akses Setara** | ✅ Semua user dengan role sama memiliki hak akses yang sama | ✅ Terpenuhi |
| **Harga Transparan** | ✅ Harga kursus ditampilkan jelas, termasuk diskon voucher | ✅ Terpenuhi |
| **Kesempatan yang Sama** | ✅ Setiap student memiliki jumlah percobaan quiz yang sama | ✅ Terpenuhi |
| **Penilaian Objektif** | ✅ Quiz dinilai otomatis berdasarkan jawaban benar | ✅ Terpenuhi |
| **Kursus Gratis** | ✅ Ada opsi kursus gratis (harga Rp 0) | ✅ Terpenuhi |
| **Voucher Diskon** | ✅ Sistem voucher untuk akses lebih terjangkau | ✅ Terpenuhi |

**Bukti Keadilan:**
1. Semua student mendapat nilai kelulusan minimum yang sama
2. Timer quiz berjalan adil untuk semua peserta
3. Tidak ada diskriminasi berdasarkan identitas user

**Skor: 6/6** ✅

---

## 3. ANALISIS SUS TESTING

Pengujian SUS (System Usability Scale) dilakukan dengan **15 responden** sesuai prinsip Belmont:

| Prinsip Belmont | Penerapan dalam SUS Testing |
|-----------------|----------------------------|
| **Respect for Persons** | ✅ Responden mengisi secara sukarela |
| **Beneficence** | ✅ Hasil digunakan untuk perbaikan sistem |
| **Justice** | ✅ Responden dipilih secara acak/representatif |

**Hasil:**
- Skor SUS: **72.8** (Grade B - Good)
- Target ≥ 68: **✅ TERCAPAI**

---

## 4. RINGKASAN KEPATUHAN

| Prinsip | Skor | Status |
|---------|------|--------|
| Respect for Persons | 5/5 | ✅ **SEPENUHNYA PATUH** |
| Beneficence | 6/6 | ✅ **SEPENUHNYA PATUH** |
| Justice | 6/6 | ✅ **SEPENUHNYA PATUH** |
| **TOTAL** | **17/17** | ✅ **100% PATUH** |

---

## 5. KESIMPULAN

Platform **UpGreenius** telah memenuhi ketiga prinsip Belmont secara komprehensif:

1. ✅ **Respect for Persons** - User memiliki otonomi penuh atas akun, data, dan proses pembelajaran mereka.

2. ✅ **Beneficence** - Platform memberikan manfaat edukasi maksimal dengan risiko minimal terhadap keamanan data dan transaksi.

3. ✅ **Justice** - Semua user diperlakukan setara dengan akses yang adil terhadap fitur, penilaian, dan kesempatan.

---

## 6. REKOMENDASI PERBAIKAN

Meskipun sudah patuh, beberapa peningkatan dapat dilakukan:

| Area | Rekomendasi | Prioritas |
|------|-------------|-----------|
| Privacy Policy | Tambahkan halaman kebijakan privasi yang lebih detail | Medium |
| Data Export | Berikan opsi user untuk mengunduh semua data mereka (GDPR compliance) | Low |
| Accessibility | Tambahkan fitur aksesibilitas untuk penyandang disabilitas | Medium |
| Consent Log | Simpan log persetujuan user untuk audit trail | Low |

---

*Dokumen Analisis Prinsip Belmont v1.0*  
*UpGreenius E-Learning Platform*  
*21 Desember 2024*
