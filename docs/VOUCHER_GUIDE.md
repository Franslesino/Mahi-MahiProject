# Panduan Pengelolaan Voucher/Diskon

## Overview
Sistem voucher/diskon telah **sepenuhnya diimplementasikan** untuk admin mengelola voucher yang ditampilkan kepada peserta/mahasiswa.

## Akses Menu Voucher

1. **Login sebagai Admin**
2. Di sidebar kiri, klik menu **"Voucher"** (dengan icon ticket)
3. Anda akan masuk ke halaman daftar voucher

## Fitur yang Tersedia

### 1. **Lihat Daftar Voucher** (`admin/vouchers`)
   - Menampilkan semua voucher dalam bentuk tabel
   - Informasi yang ditampilkan:
     - **Kode**: Kode unik voucher (huruf kapital)
     - **Nama**: Nama deskriptif voucher
     - **Tipe**: Persentase (%) atau Nominal Tetap (Rp)
     - **Nilai**: Jumlah diskon
     - **Berlaku Untuk**: Semua kursus atau kursus tertentu
     - **Penggunaan**: Jumlah yang sudah dipakai / batas maksimal
     - **Periode**: Tanggal mulai dan berakhir
     - **Status**: Aktif, Nonaktif, Expired, atau Aktif Permanen
   - **Aksi** yang bisa dilakukan:
     - 👁️ **Detail**: Lihat informasi lengkap voucher
     - ✏️ **Edit**: Ubah data voucher
     - ⚡ **Toggle Status**: Aktifkan/Nonaktifkan voucher
     - 🗑️ **Hapus**: Hapus voucher (tidak bisa jika sudah dipakai)

### 2. **Tambah Voucher Baru** 
   Klik tombol **"+ Tambah Voucher"** di halaman daftar voucher.

   **Field yang wajib diisi:**
   - **Kode Voucher**: Kode unik (otomatis uppercase). Contoh: `DISKON50`, `PROMO2024`
   - **Nama Voucher**: Nama deskriptif. Contoh: "Diskon 50% Hari Kemerdekaan"
   - **Tipe Diskon**: Pilih Persentase (%) atau Nominal Tetap (Rp)
   - **Nilai Diskon**: 
     - Jika Persentase: Masukkan angka 1-100
     - Jika Nominal: Masukkan nominal dalam Rupiah

   **Field opsional:**
   - **Deskripsi**: Penjelasan singkat tentang voucher
   - **Maksimal Diskon**: (Khusus tipe persentase) Batas maksimal potongan dalam Rupiah
   - **Minimal Pembelian**: Syarat minimal harga kursus untuk bisa pakai voucher
   - **Batas Penggunaan**: Jumlah maksimal voucher bisa dipakai (kosongkan untuk unlimited)
   - **Tanggal Mulai**: Kapan voucher mulai berlaku
   - **Tanggal Berakhir**: Kapan voucher expired (kosongkan untuk permanen)
   - **Pilih Kursus**: Tentukan kursus mana saja yang bisa pakai voucher (kosongkan untuk semua kursus)
   - **Status**: Centang "Aktifkan voucher sekarang" agar langsung bisa dipakai

### 3. **Edit Voucher**
   Klik icon ✏️ pada voucher yang ingin diubah.
   
   **Catatan:**
   - Semua field bisa diubah
   - Jika voucher sudah digunakan, akan ada peringatan
   - Kode voucher harus tetap unik

### 4. **Lihat Detail Voucher**
   Klik icon 👁️ untuk melihat:
   - Informasi lengkap voucher
   - Statistik penggunaan
   - Daftar user yang sudah pakai voucher
   - History transaksi terkait

### 5. **Toggle Status (Aktifkan/Nonaktifkan)**
   Klik icon ⚡ untuk:
   - **Aktifkan**: Voucher bisa langsung dipakai peserta
   - **Nonaktifkan**: Voucher tidak bisa dipakai sementara (tanpa harus dihapus)

### 6. **Hapus Voucher**
   Klik icon 🗑️ untuk menghapus voucher.
   
   **Penting:**
   - Voucher yang **sudah digunakan** (`used_count > 0`) **TIDAK BISA DIHAPUS**
   - Sistem akan menampilkan error jika mencoba hapus voucher yang sudah dipakai
   - Alternatif: Nonaktifkan voucher jika tidak ingin dihapus

## Contoh Use Case

### Use Case 1: Promo Diskon 20%
```
Kode: PROMO20
Nama: Promo Diskon 20%
Tipe: Persentase
Nilai: 20
Maksimal Diskon: 100000 (Rp 100.000)
Minimal Pembelian: 50000 (Rp 50.000)
Batas Penggunaan: 100
Tanggal Mulai: 01-01-2024
Tanggal Berakhir: 31-01-2024
Status: ✅ Aktif
```

### Use Case 2: Diskon Tetap untuk Kursus Premium
```
Kode: HEMAT50K
Nama: Hemat Rp 50.000 untuk Kursus Premium
Tipe: Nominal Tetap
Nilai: 50000
Minimal Pembelian: 200000 (Rp 200.000)
Pilih Kursus: [Kursus A, Kursus B, Kursus C]
Batas Penggunaan: 50
Status: ✅ Aktif
```

### Use Case 3: Voucher Unlimited
```
Kode: MAHASISWA2024
Nama: Diskon Mahasiswa 2024
Tipe: Persentase
Nilai: 15
Batas Penggunaan: (kosong - unlimited)
Tanggal Berakhir: (kosong - permanen)
Berlaku Untuk: Semua Kursus
Status: ✅ Aktif Permanen
```

## Status Voucher

| Status | Icon | Keterangan |
|--------|------|------------|
| **Aktif** | ✅ | Voucher sedang berlaku dan bisa digunakan |
| **Nonaktif** | ❌ | Voucher dimatikan oleh admin |
| **Expired** | ⏰ | Tanggal berakhir sudah lewat |
| **Aktif Permanen** | ♾️ | Voucher aktif tanpa batas waktu |

## Validasi & Pembatasan

### Sistem akan otomatis validasi:
1. ✅ **Kode unik**: Tidak boleh duplikat dengan voucher lain
2. ✅ **Persentase maksimal 100%**: Tidak boleh lebih dari 100%
3. ✅ **Tanggal valid**: Tanggal berakhir harus setelah tanggal mulai
4. ✅ **Batas penggunaan**: Tidak bisa dipakai jika sudah mencapai `max_usage`
5. ✅ **Status aktif**: Hanya voucher aktif yang bisa dipakai peserta
6. ✅ **Periode berlaku**: Hanya bisa dipakai dalam rentang tanggal yang ditentukan
7. ✅ **Minimal pembelian**: Transaksi harus memenuhi minimal harga
8. ✅ **Restriksi kursus**: Jika ada pembatasan, hanya kursus yang dipilih yang bisa pakai voucher

### Proteksi Penghapusan:
- Voucher yang **sudah digunakan** tidak bisa dihapus
- Sistem akan menampilkan error: "Voucher sudah digunakan dan tidak bisa dihapus"
- Alternatif: Nonaktifkan voucher untuk menghentikan penggunaannya

## Cara Peserta Menggunakan Voucher

1. Peserta masuk ke halaman **Checkout**
2. Ada field input **"Kode Voucher"**
3. Peserta memasukkan kode (contoh: `PROMO20`)
4. Klik **"Gunakan Voucher"**
5. Sistem validasi otomatis
6. Jika valid, diskon langsung terapkan ke total harga
7. Peserta melanjutkan pembayaran dengan harga setelah diskon

## Tips & Best Practices

### 1. **Penamaan Kode**
   - Gunakan kode yang mudah diingat
   - Hindari karakter spesial
   - Gunakan huruf kapital semua (otomatis)
   - Contoh bagus: `PROMO50`, `DISKON2024`, `HEMAT100K`

### 2. **Set Batas Penggunaan**
   - Untuk promo terbatas: Set `max_usage` (contoh: 100 orang)
   - Untuk voucher unlimited: Kosongkan field `max_usage`

### 3. **Gunakan Tanggal Berlaku**
   - Promo bulanan: Set `start_date` dan `end_date`
   - Voucher permanen: Kosongkan `end_date`

### 4. **Pembatasan Kursus**
   - Promo spesifik: Pilih kursus tertentu
   - Promo general: Kosongkan (berlaku untuk semua)

### 5. **Minimal Pembelian**
   - Untuk melindungi dari kerugian, set `min_purchase`
   - Contoh: Diskon 50% dengan minimal Rp 100.000

### 6. **Maksimal Diskon (Persentase)**
   - Set `max_discount` untuk membatasi potongan maksimal
   - Contoh: Diskon 20% maksimal Rp 50.000

## Troubleshooting

### Problem: Voucher tidak bisa dipakai peserta
**Solusi:**
1. Cek status voucher: Pastikan **Aktif** ✅
2. Cek tanggal: Pastikan masih dalam periode berlaku
3. Cek batas penggunaan: Pastikan belum mencapai `max_usage`
4. Cek minimal pembelian: Pastikan harga kursus memenuhi syarat
5. Cek restriksi kursus: Pastikan kursus termasuk dalam daftar

### Problem: Tidak bisa hapus voucher
**Penyebab:** Voucher sudah digunakan (`used_count > 0`)  
**Solusi:** Nonaktifkan voucher dengan toggle status

### Problem: Peserta dapat diskon lebih besar dari seharusnya
**Penyebab:** Tidak set `max_discount` untuk voucher persentase  
**Solusi:** Edit voucher dan set field `Maksimal Diskon`

## Route API (untuk Developer)

| Method | Route | Fungsi |
|--------|-------|--------|
| GET | `/admin/vouchers` | Daftar voucher |
| GET | `/admin/vouchers/create` | Form tambah voucher |
| POST | `/admin/vouchers` | Simpan voucher baru |
| GET | `/admin/vouchers/{id}` | Detail voucher |
| GET | `/admin/vouchers/{id}/edit` | Form edit voucher |
| PUT/PATCH | `/admin/vouchers/{id}` | Update voucher |
| DELETE | `/admin/vouchers/{id}` | Hapus voucher |
| PATCH | `/admin/vouchers/{id}/toggle` | Toggle status aktif/nonaktif |

## Struktur Database

**Table: `vouchers`**
```
- id (PK)
- code (unique, uppercase)
- name
- description (nullable)
- type (percentage/fixed)
- value (decimal)
- max_usage (nullable)
- used_count (default: 0)
- min_purchase (nullable)
- max_discount (nullable)
- start_date (nullable)
- end_date (nullable)
- is_active (boolean)
- allowed_courses (json array)
- allowed_users (json array)
- created_at
- updated_at
```

## Kesimpulan

✅ **Sistem voucher/diskon sudah fully functional!**

Admin dapat dengan mudah:
- ✅ Membuat voucher baru
- ✅ Mengedit voucher yang ada
- ✅ Melihat statistik penggunaan
- ✅ Mengaktifkan/nonaktifkan voucher
- ✅ Menghapus voucher (yang belum dipakai)
- ✅ Mengatur pembatasan dan validasi

Peserta dapat:
- ✅ Memasukkan kode voucher saat checkout
- ✅ Mendapat diskon otomatis jika valid
- ✅ Melihat detail diskon yang didapat

---
**Dibuat:** {{ date('Y-m-d') }}  
**Versi:** 1.0  
**Status:** Production Ready ✅
