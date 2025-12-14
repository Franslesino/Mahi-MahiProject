# Panduan Pengelolaan Banner Promo

## Overview
Sistem banner promo memungkinkan admin untuk mengelola carousel promo yang ditampilkan di halaman utama peserta.

## Fitur Lengkap

### ✅ **CRUD Banner Promo**
- **Create**: Tambah banner promo baru
- **Read**: Lihat daftar & detail banner
- **Update**: Edit banner yang ada
- **Delete**: Hapus banner
- **Toggle Status**: Aktifkan/Nonaktifkan banner

### ✅ **Kustomisasi Banner**
- **Judul**: Teks utama banner
- **Badge**: Label kecil (FLASH SALE, HOT DEAL, dll)
- **Deskripsi**: Penjelasan promo (support HTML break `<br>`)
- **Button**: Teks & link tombol CTA
- **Gradient**: Pilih 10 kombinasi warna gradient
- **Urutan**: Atur urutan tampil banner
- **Periode**: Set tanggal mulai & berakhir

### ✅ **Tampilan di Halaman Peserta**
- Carousel otomatis slide setiap 5 detik
- Navigation dots
- Arrow navigation (prev/next)
- Responsive design
- Smooth transition effects

## Cara Menggunakan

### **1. Akses Menu Banner Promo**
1. Login sebagai admin
2. Di sidebar, klik menu **"Banner Promo"** (icon images)
3. Anda akan masuk ke halaman daftar banner

### **2. Tambah Banner Baru**
1. Klik tombol **"+ Tambah Banner"**
2. Isi form:
   - **Judul**: "Weekend Special", "Hot Deal", dll
   - **Badge**: "FLASH SALE", "35% OFF" (opsional)
   - **Urutan**: Angka urutan tampil (semakin kecil semakin awal)
   - **Deskripsi**: Teks promo (gunakan `<br>` untuk baris baru)
   - **Teks Tombol**: "SHOP NOW", "LIHAT SEKARANG"
   - **Link Tombol**: URL tujuan (opsional)
   - **Gradient Awal**: Pilih warna (Orange, Red, Purple, dll)
   - **Gradient Akhir**: Pilih warna (Red, Pink, Blue, dll)
   - **Tanggal Mulai**: Kapan banner mulai tampil (opsional)
   - **Tanggal Berakhir**: Kapan banner berhenti (opsional)
   - **Status**: Centang untuk aktifkan langsung
3. Klik **"Simpan Banner"**

### **3. Edit Banner**
1. Di daftar banner, klik icon ✏️ pada banner yang ingin diubah
2. Edit field yang diperlukan
3. Klik **"Update Banner"**

### **4. Lihat Detail Banner**
1. Klik icon 👁️ pada banner
2. Lihat preview banner & informasi lengkap

### **5. Aktifkan/Nonaktifkan Banner**
1. Klik icon ⚡ pada banner
2. Status akan toggle otomatis
3. Banner nonaktif tidak ditampilkan di halaman peserta

### **6. Hapus Banner**
1. Klik icon 🗑️ pada banner
2. Konfirmasi penghapusan
3. Banner akan dihapus permanen

## Contoh Kasus Penggunaan

### **Kasus 1: Promo Flash Sale Akhir Pekan**
```
Judul: Weekend Special
Badge: FLASH SALE
Deskripsi: Diskon hingga 60% untuk<br>Kursus Pilihan Akhir Pekan
Teks Tombol: SHOP NOW
Link Tombol: (kosong)
Gradient: Orange 500 → Red 600
Urutan: 1
Tanggal Mulai: Jumat ini
Tanggal Berakhir: Minggu ini
Status: ✅ Aktif
```

### **Kasus 2: Promo Khusus Mahasiswa PNJ**
```
Judul: PNJ SPECIAL
Badge: 35% OFF
Deskripsi: Dapatkan Voucher Kode Bagi<br>Mahasiswa Politeknik Negeri Jakarta
Teks Tombol: MASUKKAN NIM ANDA
Link Tombol: (kosong - hanya dekoratif)
Gradient: Teal 600 → Teal 700
Urutan: 1
Tanggal Mulai: (kosong - mulai sekarang)
Tanggal Berakhir: (kosong - permanen)
Status: ✅ Aktif
```

### **Kasus 3: Bundle Package Deal**
```
Judul: Bundle Package
Badge: HOT DEAL
Deskripsi: Beli 3 Kursus Dapat Diskon 50%<br>Penawaran Terbatas!
Teks Tombol: LIHAT BUNDLE
Link Tombol: https://upgrennius.com/bundles
Gradient: Purple 600 → Pink 600
Urutan: 2
Tanggal Mulai: 01-12-2025
Tanggal Berakhir: 31-12-2025
Status: ✅ Aktif
```

## Pilihan Warna Gradient

### **Gradient From (Awal):**
- Orange 500 🟠
- Red 500 🔴
- Pink 500 🌸
- Purple 500 🟣
- Indigo 500 🔵
- Blue 500 💙
- Cyan 500 🐟
- Teal 500 🌊
- Green 500 🟢
- Emerald 500 💚

### **Gradient To (Akhir):**
- Red 600 🔴
- Orange 600 🟠
- Pink 600 🌸
- Purple 600 🟣
- Indigo 600 🔵
- Blue 600 💙
- Cyan 600 🐟
- Teal 600 🌊
- Green 600 🟢
- Emerald 600 💚

## Status Banner

| Status | Icon | Keterangan |
|--------|------|------------|
| **Aktif** | ✅ | Banner tampil di halaman peserta |
| **Terjadwal** | ⏰ | Banner akan aktif sesuai periode |
| **Nonaktif** | ❌ | Banner tidak ditampilkan |

## Urutan Tampil

- Banner dengan **urutan terkecil** ditampilkan **paling awal**
- Contoh: Banner urutan 1 → 2 → 3
- Jika urutan sama, banner terbaru ditampilkan lebih dulu

## Periode Berlaku

### **Tanggal Mulai:**
- **Kosong**: Banner mulai tampil sekarang
- **Diisi**: Banner mulai tampil pada tanggal tersebut

### **Tanggal Berakhir:**
- **Kosong**: Banner permanen (tidak expired)
- **Diisi**: Banner berhenti tampil setelah tanggal tersebut

## Carousel Behavior

### **Di Halaman Peserta:**
- Auto slide setiap **5 detik**
- Navigation dots di bawah
- Arrow navigation (prev/next)
- Pause on hover
- Smooth transition
- Responsive (mobile & desktop)

### **Jika Tidak Ada Banner:**
- Tampil **default banner** "UpGrennius"
- Informasi umum platform
- Button "JELAJAHI KURSUS"

## Tips & Best Practices

### **1. Judul yang Menarik**
✅ "Weekend Special", "Hot Deal", "Flash Sale"  
❌ "Promo", "Diskon"

### **2. Gunakan Badge untuk Urgency**
✅ "FLASH SALE", "LIMITED TIME", "HOT DEAL"  
❌ "Info", "Pengumuman"

### **3. Deskripsi Singkat & Jelas**
✅ "Diskon hingga 60% untuk<br>Kursus Pilihan Akhir Pekan"  
❌ "Ada diskon untuk kursus-kursus tertentu yang sedang promo"

### **4. CTA Button yang Jelas**
✅ "SHOP NOW", "DAFTAR SEKARANG", "LIHAT PENAWARAN"  
❌ "Klik di sini", "Info lebih lanjut"

### **5. Kombinasi Warna yang Menarik**
✅ Orange → Red (energik)  
✅ Purple → Pink (elegan)  
✅ Blue → Cyan (fresh)  
❌ Terlalu banyak warna berbeda

### **6. Atur Urutan Prioritas**
- Banner **paling penting** → urutan **1**
- Banner **seasonal** → urutan **2-3**
- Banner **general** → urutan **4-5**

### **7. Set Periode untuk Promo Terbatas**
- Flash Sale → Set tanggal berakhir
- Promo bulanan → Set start & end date
- Info permanen → Kosongkan tanggal

## Troubleshooting

### **Problem: Banner tidak muncul di halaman peserta**
**Solusi:**
1. Cek status: Pastikan banner **Aktif** ✅
2. Cek tanggal: Pastikan masih dalam periode berlaku
3. Cek urutan: Banner dengan urutan lebih kecil ditampilkan dulu
4. Clear cache browser

### **Problem: Gradient warna tidak tampil**
**Penyebab:** Tailwind CSS belum compile warna baru  
**Solusi:** Gunakan warna yang sudah tersedia di dropdown

### **Problem: Button tidak bisa diklik**
**Penyebab:** Link tombol kosong  
**Solusi:** 
- Isi field "Link Tombol" dengan URL
- Atau biarkan tombol sebagai dekoratif saja

### **Problem: Carousel tidak slide otomatis**
**Penyebab:** JavaScript error atau hanya 1 banner  
**Solusi:** 
- Pastikan ada minimal 2 banner aktif
- Check browser console untuk error
- Refresh halaman

## Database Schema

**Table: `promo_banners`**
```
- id (PK)
- title (string)
- badge (string, nullable)
- description (text)
- button_text (string)
- button_link (string, nullable)
- gradient_from (string) // Tailwind color class
- gradient_to (string)   // Tailwind color class
- order (integer)
- is_active (boolean)
- start_date (date, nullable)
- end_date (date, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

## Routes API

| Method | Route | Fungsi |
|--------|-------|--------|
| GET | `/admin/promo-banners` | Daftar banner |
| GET | `/admin/promo-banners/create` | Form tambah |
| POST | `/admin/promo-banners` | Simpan banner baru |
| GET | `/admin/promo-banners/{id}` | Detail banner |
| GET | `/admin/promo-banners/{id}/edit` | Form edit |
| PUT | `/admin/promo-banners/{id}` | Update banner |
| DELETE | `/admin/promo-banners/{id}` | Hapus banner |
| PATCH | `/admin/promo-banners/{id}/toggle` | Toggle status |

## Model Methods

**PromoBanner Model:**
```php
// Scope
->active()  // Filter banner aktif & dalam periode

// Methods
->isValid() // Check if banner is valid (active + in period)
```

## Kesimpulan

✅ **Admin bisa mengelola banner promo dengan mudah**  
✅ **CRUD lengkap dengan UI user-friendly**  
✅ **Banner tampil dinamis di halaman peserta**  
✅ **Support periode berlaku & toggle status**  
✅ **Kustomisasi lengkap (warna, teks, link)**  
✅ **Carousel otomatis dengan navigation**  

---
**Dibuat:** December 13, 2025  
**Versi:** 1.0  
**Status:** Production Ready ✅
