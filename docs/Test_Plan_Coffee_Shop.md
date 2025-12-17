# TEST PLAN
## Sistem Pemesanan Kopi Secara Online untuk Coffee Shop
**Software Quality Assurance**

---

## Metadata
- **Prepared by:** Kelompok 4  
- **Institusi:** Politeknik Negeri Jakarta  
- **Kelas:** TI 4A  
- **Versi:** 1.0 (Juni 2025)  
- **Dokumen:** UAS – SQA  

---

## Daftar Isi
1. Teks Identifier  
2. References  
3. Introduction  
4. Test Items  
5. Software Risk Issues  
6. Features To Be Tested  
7. Features Not To Be Tested  
8. Approach (Strategy)  
9. Item Pass/Fail Criteria  
10. Suspension Criteria  
11. Test Deliverables  
12. Remaining Test Tasks  
13. Environmental Needs  
14. Glossary  
15. White Box Testing  
16. Black Box Testing  
17. Performance & Stress Testing  
18. Acceptance Testing  
19. Usability Testing  

---

## 1. Teks Identifier
Dokumen test plan ini disusun menggunakan standar IEEE 829 sebagai acuan utama.

## 2. References
- Dokumen SRS  
- Dokumen Desain Sistem  

## 3. Introduction
Dokumen ini menjelaskan rencana pengujian untuk sistem pemesanan kopi online berbasis web menggunakan PHP Native.

### 3.1 Tujuan
- Menjamin kualitas sistem  
- Menjadi acuan pengujian  

### 3.2 Ruang Lingkup
- Pemesanan online  
- Pembayaran  
- Manajemen produk dan kategori  

---

## 4. Test Items
| Modul | Komponen | Metode |
|------|---------|--------|
| Pemesanan | Menu & Keranjang | Black Box |
| Pembayaran | Gateway | Integration |
| Admin | Dashboard | White Box |

---

## 5. Software Risk Issues
| Risiko | Dampak | Mitigasi |
|------|--------|----------|
| Pembayaran gagal | Kehilangan pendapatan | Uji error handling |
| Performa lambat | UX buruk | Optimasi sistem |

---

## 6. Features To Be Tested
- Add/Edit Category  
- Add/Edit Product  
- Checkout  
- Payment  
- Report  

---

## 7. Features Not To Be Tested
- Login / Register  
- Logout  
- Custom Order  

---

## 8. Approach (Strategy)
- Black Box Testing (Katalon)  
- White Box Testing (Cyclomatic Complexity)  

---

## 9. Item Pass/Fail Criteria
- Semua fungsi berjalan normal  
- Data valid dan konsisten  

---

## 10. Suspension Criteria
Pengujian dihentikan sementara jika mayoritas test case gagal.

---

## 11. Test Deliverables
- Test Plan  
- Test Case  
- White Box Result  
- Black Box Result  

---

## 12. Remaining Test Tasks
- Custom Menu  
- Login & Register  

---

## 13. Environmental Needs
- Web Browser  
- XAMPP  
- Katalon  

---

## 14. Glossary
- **Data:** Input sistem  
- **Hosting:** Server aplikasi  

---

## 15. White Box Testing
Berisi analisis flow program, statement coverage, decision coverage, dan cyclomatic complexity.

---

## 16. Black Box Testing
Berisi test case detail untuk setiap fitur sistem.

---

## 17. Performance & Stress Testing
Pengujian beban sistem pada kondisi ekstrem.

---

## 18. Acceptance Testing
Validasi sistem terhadap kebutuhan user.

---

## 19. Usability Testing
Evaluasi kemudahan penggunaan sistem.

---
