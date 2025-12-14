# Integrasi Final Quiz dengan Question Banks

## Overview
Final Quiz sekarang mendukung import soal dari **2 sumber**:
1. **Bank Soal Lama** (`bank_soal` table)
2. **Question Banks Baru** (`question_banks` & `questions` table)

## Fitur Baru

### 1. Tab Interface
Halaman **"Buat Final Quiz Baru"** sekarang memiliki 2 tab:
- **Bank Soal Lama**: Soal dari tabel `bank_soal` (sistem lama)
- **Question Banks (Baru)**: Soal dari tabel `questions` (sistem baru seperti screenshot)

### 2. Import dari Question Banks
Ketika soal dari Question Banks dipilih, sistem akan:
1. Membaca soal dari tabel `questions`
2. **Otomatis copy** soal ke tabel `bank_soal` untuk kompatibilitas
3. Copy semua opsi jawaban (jika ada) ke `opsi_jawaban`
4. Link soal ke quiz melalui `relasi_quiz`

### 3. Kategori Otomatis
Soal yang diimport dari Question Banks akan diberi kategori:
- `kategori = "Imported from Question Bank"`

## Cara Menggunakan

### Untuk Instruktur:

1. **Akses halaman Final Quiz**
   - Buka kursus → Klik "Final Quiz" → "Buat Quiz Baru"

2. **Isi informasi quiz**
   - Judul quiz
   - Durasi (opsional)
   - Maksimal percobaan
   - Passing grade

3. **Pilih soal** dari 2 tab:
   
   **Tab 1: Bank Soal Lama**
   - Soal dari `/instructor/bank-soal`
   - Format lama dengan kategori manual
   
   **Tab 2: Question Banks (Baru)**
   - Soal dari `/instructor/question-banks`
   - Terorganisir per folder bank
   - Mendukung sharing (public/private)
   - Menampilkan poin per soal

4. **Centang soal** yang ingin digunakan (bisa dari kedua tab)

5. **Klik "Simpan & Buat Final Quiz"**

## Technical Details

### Controller Update
**File**: `app/Http/Controllers/Instructor/FinalQuizController.php`

**Method**: `createQuiz()`
```php
// Ambil Question Banks dengan filter
$questionBanks = \App\Models\QuestionBank::with(['questions.options'])
    ->where(function($query) {
        $query->where('created_by', Auth::id())
              ->orWhere('is_public', true);
    })
    ->get();
```

**Method**: `storeQuiz()`
```php
// Validasi input
'question_bank_questions' => 'nullable|array',
'question_bank_questions.*' => 'exists:questions,id',

// Copy soal dari questions ke bank_soal
$bankSoalId = DB::table('bank_soal')->insertGetId([...]);

// Copy options
foreach ($question->options as $option) {
    DB::table('opsi_jawaban')->insert([...]);
}
```

### View Update
**File**: `resources/views/instructor/courses/create-final-quiz.blade.php`

**Struktur Tab**:
- Tab navigation dengan styling Tailwind
- Content area per tab (show/hide dengan JavaScript)
- Search & filter untuk setiap tab
- Select all/deselect all per tab

**JavaScript Functions**:
- `switchTab(tabName)` - Switch antara tab
- `selectAllOld()` / `deselectAllOld()` - Bank Soal Lama
- `selectAllNew()` / `deselectAllNew()` - Question Banks
- `filterOld()` / `filterNew()` - Search functionality

## Database Flow

```
Question Banks → questions → (COPY) → bank_soal → relasi_quiz → quiz
                  ↓                        ↓
            question_options          opsi_jawaban
```

### Data Copy Process:
1. User pilih soal dari Question Bank
2. Sistem copy `question_text` → `pertanyaan`
3. Sistem copy `type` → `tipe_soal`
4. Sistem copy `option_text` → `teks_opsi`
5. Sistem copy `is_correct` → `is_benar`
6. Link ke quiz via `relasi_quiz`

## Benefits

✅ **Backward Compatible**: Sistem lama tetap berfungsi
✅ **Unified Interface**: Satu tempat untuk pilih soal dari 2 sistem
✅ **No Data Loss**: Soal dicopy, tidak dimove
✅ **Easy Migration**: User bisa pindah ke sistem baru bertahap
✅ **Public Sharing**: Bisa gunakan Question Banks public dari instruktur lain

## Future Improvements

- [ ] Import bulk dari multiple Question Banks sekaligus
- [ ] Preview soal sebelum import
- [ ] Edit soal yang sudah diimport
- [ ] Sync changes dari Question Bank ke quiz yang sudah ada
- [ ] Export final quiz ke Question Bank format

## Troubleshooting

**Q: Soal tidak muncul di tab Question Banks?**
A: Pastikan Question Bank sudah dibuat dan memiliki soal. Check di `/instructor/question-banks`

**Q: Setelah import, soal tidak muncul di quiz?**
A: Check tabel `relasi_quiz` untuk memastikan link berhasil dibuat

**Q: Error saat submit form?**
A: Check validasi - pastikan soal yang dipilih masih ada di database

## Routes

```php
GET  /instructor/courses/{kursus}/final-quiz/create-quiz
POST /instructor/courses/{kursus}/final-quiz/store-quiz
```

## Models Involved

- `QuestionBank` - Container untuk koleksi soal
- `Question` - Soal individual (sistem baru)
- `QuestionOption` - Opsi jawaban (sistem baru)
- `BankSoal` - Soal individual (sistem lama)
- `OpsiJawaban` - Opsi jawaban (sistem lama)
- `Quiz` - Quiz/Final Quiz
- `RelasiQuiz` - Pivot table soal-quiz

## Security

- Authorization check: `canManageCourse()`
- Validasi foreign key: `exists:questions,id`
- Transaction rollback jika error
- Filter Question Banks: only owned or public
