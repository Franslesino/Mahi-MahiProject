# Bank Soal Management

## Fitur Bank Soal

Sistem Bank Soal memungkinkan instruktur dan admin untuk:
- ✅ Membuat koleksi soal yang dapat digunakan kembali
- ✅ Mengelola soal dengan kategori
- ✅ Mendukung 3 tipe soal: Multiple Choice, Essay, True/False
- ✅ Import soal dari bank ke quiz
- ✅ Export soal quiz dalam format JSON

## Menu Navigasi

Bank Soal dapat diakses melalui sidebar instruktur:
- **Dashboard Instruktur** → **Bank Soal**
- URL: `/instructor/bank-soal`

## Manajemen Bank Soal

### Menu Bank Soal:

1. **Index** - Lihat semua soal dalam bank
   - Statistik: Total soal, Multiple Choice, Essay, True/False
   - Info penggunaan: Soal digunakan di berapa quiz
   - Actions: Edit, Delete

2. **Create** - Tambah soal baru
   - Form dinamis berdasarkan tipe soal
   - Multiple choice: Tambah/hapus opsi
   - True/False: Pilih jawaban benar
   - Essay: Hanya pertanyaan

3. **Edit** - Update soal existing
   - Edit kategori dan pertanyaan
   - Opsi jawaban read-only (tidak bisa diubah)
   - Warning jika soal sedang digunakan di quiz

4. **Delete** - Hapus soal
   - Validasi: Soal yang sedang digunakan tidak bisa dihapus
   - Cascade delete: Hapus juga opsi jawaban

## Route List

### Bank Soal Routes:
```php
GET    /instructor/bank-soal              - Index
GET    /instructor/bank-soal/create       - Create Form
POST   /instructor/bank-soal              - Store
GET    /instructor/bank-soal/{id}/edit    - Edit Form
PUT    /instructor/bank-soal/{id}         - Update
DELETE /instructor/bank-soal/{id}         - Delete
```
GET  /instructor/courses/{course}/final-quiz/{quiz}/import  - Import Form
POST /instructor/courses/{course}/final-quiz/{quiz}/import  - Import Process
```

## Database Schema

### bank_soal Table:
- `id` - Primary key
- `pertanyaan` - Text
- `image_url` - String (nullable)
- `tipe_soal` - Enum (multiple_choice, essay, true_false)
- `kategori` - String (nullable)
- `created_at`, `updated_at`

### opsi_jawaban Table:
- `id` - Primary key
- `bank_soal_id` - Foreign key
- `teks_opsi` - Text
- `is_benar` - Boolean
- `created_at`, `updated_at`

### relasi_quiz Table (Pivot):
- `id` - Primary key
- `quiz_id` - Foreign key
- `bank_soal_id` - Foreign key
- `is_active` - Boolean
- `urutan` - Integer
- `created_at`, `updated_at`

## Controllers

### BankSoalController
- `index()` - List bank soal dengan pagination
- `create()` - Form tambah soal
- `store()` - Simpan soal baru
- `edit()` - Form edit soal
- `update()` - Update soal
- `destroy()` - Hapus soal (dengan validasi)

### FinalQuizController (Extended)
- `export()` - Download soal dalam JSON
- `showImport()` - Form pilih soal dari bank
- `importFromBank()` - Proses import soal terpilih

## Authorization

- ✅ Hanya **Admin** dan **Instructor** yang bisa akses bank soal
- ✅ Authorization check di setiap method controller
- ✅ Validasi ownership untuk edit/delete quiz

## Use Cases

### Use Case 1: Buat Koleksi Soal
1. Instruktur masuk ke Bank Soal
2. Tambah soal dengan kategori (misal: "matematika")
3. Soal tersimpan dan dapat digunakan di berbagai quiz

### Use Case 2: Reuse Soal di Multiple Quiz
1. Buat final quiz untuk Course A
2. Import soal dari bank soal
3. Buat final quiz untuk Course B
4. Import soal yang sama dari bank soal
5. Soal yang sama digunakan di 2 quiz berbeda

### Use Case 3: Backup & Share Soal
1. Export soal dari final quiz ke JSON
2. Share file JSON ke instruktur lain
3. Instruktur lain dapat melihat struktur soal
4. (Future: Import dari JSON file)

## Best Practices

1. **Kategorisasi**: Beri kategori yang jelas (misal: "laravel", "php", "database")
2. **Naming**: Gunakan judul pertanyaan yang deskriptif
3. **Reusability**: Buat soal yang generic agar bisa digunakan di berbagai konteks
4. **Validation**: Pastikan minimal ada 1 jawaban benar untuk multiple choice
5. **Clean Up**: Hapus soal yang tidak terpakai untuk menjaga database tetap bersih

## Fitur Future (Roadmap)

- [ ] Import dari file JSON
- [ ] Import dari file Excel/CSV
- [ ] Bulk edit soal
- [ ] Tag system untuk soal
- [ ] Difficulty level
- [ ] Question analytics (berapa kali dipakai, success rate)
- [ ] Share bank soal antar instruktur
- [ ] Public question bank

## Troubleshooting

**Q: Soal tidak bisa dihapus?**
A: Soal yang sedang digunakan di quiz tidak dapat dihapus. Hapus dari quiz terlebih dahulu.

**Q: Export tidak download file?**
A: Pastikan browser tidak memblokir download. Check browser settings.

**Q: Import soal tidak muncul di quiz?**
A: Refresh halaman edit quiz. Soal akan muncul di urutan paling bawah.

**Q: Soal sudah di-import tapi tidak muncul di list?**
A: Soal yang sudah ada di quiz akan otomatis di-filter dari list import.
