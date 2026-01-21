# BLUEPRINT SISTEM E-GOV BANK PROGRAM PEMBANGUNAN PU

## 1. Ringkasan Sistem
Sistem ini dirancang sebagai bank data program, alat kendali proses, dan dokumentasi keputusan untuk Program Pembangunan PU. Sistem ini berfungsi sebagai repositori sentral di mana seluruh data program dikelola dan statusnya dipantau secara ketat sesuai dengan tahapan yang telah ditetapkan. Interaksi diskusi dan negosiasi antar pemangku kepentingan (Pusat, Balai, K/L, Pemda) dilakukan secara offline/rapat fisik. Sistem hanya bertugas mencatat hasil keputusan dan progres dari pertemuan-pertemuan tersebut. Pengelolaan data dalam sistem sepenuhnya dilakukan oleh operator tunggal (ADMIN), sementara pihak PU Pusat hanya memiliki akses untuk memantau data (view-only) tanpa kemampuan intervensi data secara langsung di dalam sistem.

## 2. Tabel Role & Hak Akses

Sistem ini hanya mengenal dua aktor (role) dengan pembagian hak akses yang tegas:

| Role | Deskripsi | Hak Akses (Permissions) |
| :--- | :--- | :--- |
| **ADMIN** | Operator Tunggal Sistem | ✅ **FULL ACCESS**<br>• Membuat program baru.<br>• Mengedit data program.<br>• Mengubah status program (sesuai alur).<br>• Mencatat seluruh hasil pembahasan (Catatan).<br>• Membuat dan mengunggah Berita Acara.<br>• Mengelola pengguna (jika ada). |
| **PU_PUSAT** | Viewer / Pemantau | 👁️ **READ ONLY**<br>• Melihat daftar seluruh program.<br>• Melihat detail data program.<br>• Melihat riwayat catatan pembahasan.<br>• Melihat/mengunduh Berita Acara.<br>❌ **TIDAK BISA** input, edit, atau hapus data apapun. |

**Catatan:** Tidak ada role lain (seperti Balai, Pemda, K/L) dan tidak ada akses publik.

## 3. Workflow Status Program

Perjalanan status program bersifat linear (satu arah) dan tidak boleh melompat atau mundur. Status ini menggambarkan posisi program dalam siklus pematangan perencanaan.

1. **TERDAFTAR**: Status awal saat program pertama kali dimasukkan ke dalam sistem oleh Admin.
2. **DIBAHAS_PU**: Program sedang dalam tahap pembahasan internal di lingkup PU. Admin mencatat masukan dari unit internal/Balai.
3. **CATATAN_KL**: Program dibahas dengan Kementerian/Lembaga lain. Admin mencatat sinkronisasi atau masukan lintas sektor.
4. **KONSOLIDASI_PEMDA**: Program dikonsolidasikan dengan Pemerintah Daerah untuk kesiapan lahan/dukungan lokal. Admin mencatat komitmen daerah.
5. **BERITA_ACARA**: Tahap akhir di mana keputusan (Lanjut/Tangguh) ditetapkan dan dokumen legal (PDF) diunggah.

## 4. Alur Logis (Pseudo-flow)

Berikut adalah narasi logika alur sistem dari awal hingga akhir:

1. **START**
2. **ADMIN** menerima usulan program secara offline.
3. **ADMIN** input data program ke sistem → System set Status: **TERDAFTAR**.
4. Rapat/Pembahasan Internal PU terjadi (Offline).
   - **ADMIN** input hasil pembahasan ke Entity `Catatan` (Tahap: DIBAHAS_PU).
   - **ADMIN** update status program → Status: **DIBAHAS_PU**.
5. Rapat/Koordinasi dengan K/L Lain terjadi (Offline).
   - **ADMIN** input masukan K/L ke Entity `Catatan` (Tahap: CATATAN_KL).
   - **ADMIN** update status program → Status: **CATATAN_KL**.
6. Rapat/Konsolidasi dengan Pemda terjadi (Offline).
   - **ADMIN** input komitmen Pemda ke Entity `Catatan` (Tahap: KONSOLIDASI_PEMDA).
   - **ADMIN** update status program → Status: **KONSOLIDASI_PEMDA**.
7. Finalisasi Keputusan Program (Offline).
   - **ADMIN** membuat entri keputusan di Entity `Berita_Acara` (Upload PDF).
   - **ADMIN** update status program → Status: **BERITA_ACARA**.
8. **PU_PUSAT** memantau progres dan melihat detail di setiap tahapan (Read-Only).
9. **END**

## 5. Struktur Data (Entities)

Desain struktur data minimum untuk mendukung fungsionalitas di atas:

### Entity: Program
Menyimpan data utama usulan program.
- `id` (Primary Key)
- `nama_program` (Text)
- `sektor` (Text)
- `lokasi` (Text)
- `estimasi_biaya` (Number/Currency)
- `status` (Enum: TERDAFTAR, DIBAHAS_PU, CATATAN_KL, KONSOLIDASI_PEMDA, BERITA_ACARA)
- `created_by` (Reference to ADMIN)
- `created_at` (Timestamp)

### Entity: Catatan
Menyimpan riwayat pembahasan dan masukan dari berbagai pihak di setiap tahap.
- `id` (Primary Key)
- `program_id` (Foreign Key to Program)
- `tahap` (Enum: DIBAHAS_PU, CATATAN_KL, KONSOLIDASI_PEMDA)
- `sumber` (Enum: PU, BALAI, KL, PEMDA)
- `isi_catatan` (Text/Long Text)
- `dicatat_oleh` (Reference to ADMIN)
- `waktu` (Timestamp)

### Entity: Berita_Acara
Menyimpan dokumen final keputusan program.
- `id` (Primary Key)
- `program_id` (Foreign Key to Program)
- `keputusan` (Enum: LANJUT, TANGGUH)
- `ringkasan_kesepakatan` (Text)
- `tanggal` (Date)
- `file_pdf` (File Path / URL)
- `dibuat_oleh` (Reference to ADMIN)

## 6. Aturan Otorisasi (Authorization Rules)

Aturan ketat yang wajib diterapkan dalam logika aplikasi:

*   **Rule 1 (Akses Mutlak)**: Hanya user dengan role **ADMIN** yang diizinkan melakukan operasi *Create*, *Update*, dan *Upload*.
*   **Rule 2 (Pasif)**: User dengan role **PU_PUSAT** dilarang keras melakukan perubahan data apapun (Strict Read-Only). Tombol simpan/edit harus disembunyikan atau dinonaktifkan untuk role ini.
*   **Rule 3 (Integritas Workflow)**: Perubahan kolom `status` pada entity `Program` hanya boleh dilakukan oleh ADMIN dan harus mengikuti urutan yang telah ditentukan (1→2→3→4→5). Tidak boleh mundur atau melompat.
*   **Rule 4 (Audit Pencatat)**: Setiap penambahan data pada `Catatan` dan `Berita_Acara` otomatis merekam ID Admin yang sedang login sebagai `dicatat_oleh` atau `dibuat_oleh`.
*   **Rule 5 (Single Source)**: Tidak ada pendaftaran user secara mandiri (self-registration). Akun role dikelola secara internal/pre-seeded.
