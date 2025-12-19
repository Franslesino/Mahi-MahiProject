# Material Status Field Update

## Tanggal: 2025-12-19

### Perubahan yang Dilakukan

#### ✅ Tambah Field Status Published pada Form Create Material

**Problem:**
Admin dan Instructor harus edit materi lagi setelah create untuk mempublikasikan (set status dari draft ke published).

**Solution:**
Menambahkan field "Status Publikasi" pada form create material dengan pilihan:
- **Draft** (Belum Dipublikasikan)
- **Published** (Dipublikasikan) - Default

### Files Modified:

#### 1. Admin Create Material Form
**File:** `resources/views/admin/courses/materials/create.blade.php`

**Changes:**
- Ditambahkan dropdown "Status Publikasi" setelah field "Status Terkunci"
- Default value: "published" agar materi langsung tersedia
- Required field dengan validasi error handling

**Code Added:**
```blade
<!-- Status Published -->
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Status Publikasi <span class="text-red-500">*</span>
    </label>
    <select name="status" 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror"
            required>
        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (Belum Dipublikasikan)</option>
        <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>Published (Dipublikasikan)</option>
    </select>
    <p class="text-sm text-gray-500 mt-1">Pilih "Published" agar materi langsung tersedia untuk siswa</p>
    @error('status')
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
```

#### 2. Instructor Create Material Form
**File:** `resources/views/instructor/materials/create.blade.php`

**Status:** ✅ Sudah ada sebelumnya (Line 54-60)
- Field status sudah tersedia di form instructor
- Default: "draft"
- Pilihan: draft | published

### Controller Support

#### Admin Material Controller
**File:** `app/Http/Controllers/Admin/MaterialController.php`

**Status:** ✅ Sudah support
- Validasi field 'status' sudah ada (line 38)
- Accepts: 'published' atau 'draft'

#### Instructor Material Controller
**File:** `app/Http/Controllers/Instructor/MaterialController.php`

**Status:** ✅ Sudah support
- Validasi field 'status' sudah ada (line 320)
- Default: 'draft' (line 398)
- Auto-publish untuk class_session (line 411)

### Database Schema

**Table:** `materi`
**Column:** `status`
**Type:** VARCHAR/ENUM
**Values:** 'draft' | 'published'

### User Experience Impact

**Before:**
1. Admin/Instructor create materi → status otomatis draft
2. Harus masuk ke edit page
3. Set status ke published
4. Save

**After:**
1. Admin/Instructor create materi
2. Pilih status "Published" langsung di form create
3. Materi langsung tersedia untuk siswa
4. ✅ Tidak perlu edit lagi!

### Testing Checklist

- [ ] Admin: Create material dengan status "Published" → materi langsung visible untuk student
- [ ] Admin: Create material dengan status "Draft" → materi tidak visible untuk student
- [ ] Instructor: Create material dengan status "Published" → materi langsung visible
- [ ] Instructor: Create material dengan status "Draft" → materi tidak visible
- [ ] Form validation: Error jika status tidak dipilih (admin)
- [ ] Old input: Status tetap terpilih jika ada validation error
- [ ] Class session: Auto-publish tetap bekerja

### Notes

- Perubahan bersifat backward compatible
- Tidak ada perubahan database schema
- Tidak ada perubahan controller logic
- Default value untuk admin: "published" (langsung publish)
- Default value untuk instructor: "draft" (perlu review dulu)
