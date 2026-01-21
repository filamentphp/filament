# ARSITEKTUR TEKNIS FASE 2A - E-GOV BANK PROGRAM PEMBANGUNAN PU

Dokumen ini menjelaskan arsitektur teknis tingkat tinggi (High-Level Technical Architecture) untuk sistem E-Gov Bank Program Pembangunan PU. Desain ini bersifat agnostik terhadap teknologi/framework tertentu dan berfokus pada struktur logis, pembagian modul, serta prinsip keamanan dan audit.

## 1. Arsitektur Umum (3-Layer Architecture)

Sistem akan dibangun menggunakan pendekatan arsitektur 3-lapis (3-Tier Architecture) untuk memastikan pemisahan tanggung jawab (separation of concerns) yang jelas.

1.  **Presentation Layer (Lapisan Presentasi)**
    *   **Fungsi:** Bertanggung jawab menampilkan antarmuka pengguna (UI) kepada user (ADMIN dan PU_PUSAT).
    *   **Tanggung Jawab:** Menerima input dari pengguna, menampilkan data program, visualisasi status workflow, dan menangani interaksi dasar (klik, navigasi).
    *   **Hubungan:** Lapisan ini tidak memiliki logika bisnis yang kompleks. Ia hanya mengirim permintaan (request) ke Application Layer dan menampilkan responnya.

2.  **Application Layer (Lapisan Aplikasi / Logika Bisnis)**
    *   **Fungsi:** "Otak" dari sistem yang memproses seluruh aturan bisnis, validasi, dan alur kerja (workflow).
    *   **Tanggung Jawab:** Memverifikasi hak akses (apakah user ADMIN atau PU_PUSAT), memvalidasi transisi status workflow (agar tidak lompat/mundur), memproses data input, dan mengoordinasikan penyimpanan data.
    *   **Hubungan:** Menerima perintah dari Presentation Layer, memprosesnya sesuai aturan bisnis, lalu berinteraksi dengan Data Layer untuk persistensi.

3.  **Data Layer (Lapisan Data)**
    *   **Fungsi:** Bertanggung jawab atas penyimpanan dan pengambilan data secara persisten.
    *   **Tanggung Jawab:** Menyimpan tabel/entity (Program, Catatan, Berita Acara, Log Audit) dan menjaga integritas relasi antar data.
    *   **Hubungan:** Hanya dapat diakses oleh Application Layer. Tidak boleh ada akses langsung dari Presentation Layer ke database.

---

## 2. Modul Sistem

Sistem dipecah menjadi modul-modul fungsional berikut untuk menjamin keteraturan dan keamanan:

### 🔹 Auth & Access Control Module
*   **Fungsi:** Gerbang utama keamanan sistem.
*   **Mekanisme:**
    *   **Autentikasi:** Memverifikasi identitas pengguna saat login.
    *   **Role Enforcement:** Secara ketat membedakan sesi pengguna. Jika user adalah **PU_PUSAT**, modul ini secara otomatis memblokir seluruh akses ke fungsi tulis/ubah (write/update) di level sistem, menjadikannya murni *Read-Only*. Jika user adalah **ADMIN**, akses penuh diberikan.

### 🔹 Program Management Module
*   **Fungsi:** Mengelola siklus hidup data utama Program.
*   **Mekanisme:**
    *   Menyediakan fungsi Create dan Update untuk properti program (Nama, Sektor, Lokasi, Biaya).
    *   Hanya mengizinkan eksekusi fungsi tersebut jika role = ADMIN.
    *   Menyediakan fungsi Read (List & Detail) yang dapat diakses oleh ADMIN dan PU_PUSAT.

### 🔹 Workflow Engine (Sederhana)
*   **Fungsi:** Penjaga gawang perubahan status program.
*   **Aturan Logika:**
    *   Hanya ADMIN yang boleh memicu perubahan status.
    *   Memastikan transisi status bersifat linear: `TERDAFTAR` → `DIBAHAS_PU` → `CATATAN_KL` → `KONSOLIDASI_PEMDA` → `BERITA_ACARA`.
    *   Menolak keras (Reject) segala percobaan untuk melompati tahapan atau memundurkan status.

### 🔹 Catatan Management Module
*   **Fungsi:** Mengelola dokumentasi hasil pembahasan/rapat offline.
*   **Mekanisme:**
    *   Setiap catatan terikat pada satu Program ID dan satu Tahap spesifik.
    *   Mencatat atribut metadata: Sumber (PU/Balai/KL/Pemda) dan Waktu.
    *   Secara otomatis merekam ID ADMIN yang melakukan input sebagai audit trail pencatat.

### 🔹 Berita Acara Module
*   **Fungsi:** Mengelola dokumen final keputusan program.
*   **Mekanisme:**
    *   Menangani input keputusan (Lanjut/Tangguh) dan ringkasan kesepakatan.
    *   Menangani proses upload dan penyimpanan file PDF Berita Acara secara aman.
    *   Memastikan file dapat diunduh/dibaca oleh PU_PUSAT tetapi tidak dapat diubah/dihapus oleh siapapun (kecuali mekanisme koreksi khusus oleh ADMIN yang tercatat log).

### 🔹 Audit & Logging Module
*   **Fungsi:** "Kotak Hitam" sistem yang merekam segala aktivitas.
*   **Cakupan:**
    *   Mencatat *siapa* (User ID), *kapan* (Timestamp), *apa* (Action Type), dan *data apa* (Old Value vs New Value) untuk setiap operasi perubahan data.
    *   Fokus utama pada perubahan Status Program dan Upload Berita Acara.

---

## 3. Alur Data & Kontrol (Narrative Flow)

Berikut adalah narasi bagaimana data bergerak antar modul dalam skenario utama:

### A. Saat Program Dibuat
1.  **Presentation Layer** menerima input data program dari form yang diisi ADMIN.
2.  Data dikirim ke **Application Layer**.
3.  **Auth Module** memverifikasi bahwa pengirim adalah ADMIN.
4.  **Program Module** memvalidasi kelengkapan data.
5.  **Workflow Engine** menetapkan status awal otomatis menjadi `TERDAFTAR`.
6.  **Data Layer** menyimpan record baru ke tabel Program.
7.  **Audit Module** mencatat event "Create Program" oleh ADMIN.

### B. Saat Status Berubah
1.  **Presentation Layer** menerima perintah ADMIN untuk memajukan status (misal: dari `TERDAFTAR` ke `DIBAHAS_PU`).
2.  **Application Layer** menerima request.
3.  **Auth Module** memverifikasi user adalah ADMIN.
4.  **Workflow Engine** mengecek status saat ini. Jika status saat ini adalah `TERDAFTAR` dan target adalah `DIBAHAS_PU`, transisi diizinkan. Jika target tidak valid (misal langsung ke `BERITA_ACARA`), request ditolak error.
5.  **Data Layer** mengupdate kolom status pada tabel Program.
6.  **Audit Module** mencatat perubahan status (From: TERDAFTAR, To: DIBAHAS_PU) beserta timestamp.

### C. Saat Catatan Ditambahkan
1.  ADMIN menginput hasil rapat offline melalui UI.
2.  **Application Layer** memvalidasi input (tidak boleh kosong).
3.  **Catatan Module** mengaitkan catatan dengan ID Program yang sedang aktif.
4.  Sistem secara otomatis mengisi field `dicatat_oleh` dengan ID ADMIN yang sedang login (diambil dari sesi Auth).
5.  **Data Layer** menyimpan catatan baru.

### D. Saat Berita Acara Dibuat
1.  ADMIN mengupload file PDF dan mengisi ringkasan keputusan.
2.  **Berita Acara Module** memproses penyimpanan file fisik ke storage aman dan menyimpan metadata ke database.
3.  **Workflow Engine** memverifikasi bahwa program telah melalui tahap `KONSOLIDASI_PEMDA` sebelum mengizinkan pembuatan Berita Acara (masuk tahap `BERITA_ACARA`).
4.  **Audit Module** mencatat finalisasi program ini sebagai event penting.

---

## 4. Boundary Frontend vs Backend

Pemisahan tanggung jawab antara Frontend (Tampilan) dan Backend (Server) sangat krusial untuk keamanan:

### Frontend (UI)
*   **Tugas:** Menyediakan form input, menampilkan pesan error yang ramah pengguna, dan menyembunyikan tombol-tombol aksi bagi user `PU_PUSAT`.
*   **Sifat:** *Tidak dapat dipercaya*. Validasi di frontend hanya demi kenyamanan pengguna (UX), bukan untuk keamanan. Manipulasi elemen HTML/JS masih mungkin dilakukan user.

### Backend (Server)
*   **Tugas:** Melakukan validasi keras (Hard Validation).
*   **Kewajiban:**
    *   Meskipun tombol "Edit" disembunyikan di UI untuk PU_PUSAT, Backend **WAJIB** menolak request API `/update` jika token pengguna bukan ADMIN.
    *   Meskipun dropdown status di UI hanya menampilkan status selanjutnya, Backend **WAJIB** mengecek ulang apakah status target valid sesuai urutan workflow sebelum menyimpan ke database.
    *   **Governance:** Backend adalah penegak hukum mutlak aturan bisnis dan otorisasi.

---

## 5. Konsep Keamanan Dasar

Tanpa bergantung pada framework tertentu, prinsip keamanan berikut diterapkan:

1.  **Least Privilege (Hak Akses Minimal):**
    *   User `PU_PUSAT` diberikan akses seminimal mungkin, yaitu hanya *Read*. Tidak ada satupun jalur (route/endpoint) yang mengizinkan operasi *Write* yang terbuka untuk role ini.
2.  **Role-Based Access Control (RBAC) di Level API:**
    *   Pengecekan role dilakukan di setiap permintaan modifikasi data. "Hiding button" di UI tidak cukup; "Blocking request" di server adalah wajib.
3.  **Immutability of Audit Trails:**
    *   Log audit dan riwayat status dirancang untuk tidak bisa diubah atau dihapus, bahkan oleh ADMIN, guna menjaga jejak forensik.
4.  **Workflow Integrity Protection:**
    *   Mencegah manipulasi status melalui injeksi data langsung. Logika transisi status dikunci mati (hard-coded logic) di Application Layer, bukan dinamis dari input user.

---

## 6. Konsep Audit & Compliance

Sistem dirancang agar "Audit-Ready" kapan saja untuk kebutuhan internal maupun eksternal (Itjen/BPK):

### Data yang Di-log (Audit Trail)
*   **Who:** Username/ID aktor pelaksana.
*   **When:** Tanggal dan jam presisi (termasuk timezone).
*   **Action:** Jenis aksi (Create, Update, Status Change, Upload).
*   **Object:** ID Program atau entity yang dimanipulasi.
*   **Detail:** Snapshot data sebelum dan sesudah perubahan (untuk update kritikal seperti Status).

### Waktu Logging
*   Logging terjadi secara *synchronous* (langsung) saat transaksi database berhasil dilakukan. Jika log gagal ditulis, transaksi utama sebaiknya dibatalkan (atomic) untuk menjamin konsistensi jejak.

### Kesiapan Pemeriksaan
*   **Audit Internal:** Log tersentralisasi memudahkan penelusuran jika ada sengketa proses ("Siapa yang mengubah status ini?").
*   **Review Itjen/BPK:** Sistem dapat menyajikan "History Report" per program yang menampilkan kronologis lengkap dari `TERDAFTAR` hingga `BERITA_ACARA`, lengkap dengan siapa yang bertanggung jawab di setiap titik, tanpa ada celah data yang hilang.
