# SPESIFIKASI PELAPORAN DAN DASHBOARD (Fase 4)

Dokumen ini menjelaskan spesifikasi teknis dan fungsional modul pelaporan untuk Sistem E-Gov Bank Program Pembangunan PU.

## 1. Dashboard Eksekutif (Executive Dashboard)

Dashboard ini dirancang untuk memberikan gambaran cepat (helicopter view) kepada pimpinan mengenai status portofolio program.

### Widget A: Ringkasan Status Program (`ProgramStatusOverview`)
*   **Tipe:** Stats Overview (Kartu Angka)
*   **Sumber Data:** `Program::count()` dengan grouping berdasarkan status.
*   **Metrik:**
    *   Total Program: Jumlah seluruh program yang masuk sistem.
    *   Terdaftar - Berita Acara: Breakdown jumlah program di setiap tahap pipeline.
*   **Tujuan:** Monitoring bottleneck atau penumpukan program di tahap tertentu.

### Widget B: Program per Sektor (`ProgramBySektorChart`)
*   **Tipe:** Bar Chart
*   **Sumber Data:** Aggregation `count(*)` grouping by `sektor`.
*   **Tujuan:** Melihat distribusi beban kerja atau fokus pembangunan berdasarkan sektor (misal: SDA vs Bina Marga).

### Widget C: Keputusan Final (`ProgramDecisionChart`)
*   **Tipe:** Pie Chart
*   **Sumber Data:** Entity `BeritaAcara`, grouping by `keputusan` (LANJUT vs TANGGUH).
*   **Tujuan:** Mengetahui rasio keberhasilan program yang lolos seleksi akhir.

## 2. Laporan Detail (Report Pages)

Halaman laporan tabelaris untuk kebutuhan audit dan rapat detail. Semua laporan bersifat Read-Only dan dilengkapi fitur Export CSV.

### Laporan A: Program Aktif (`ReportProgramAktif`)
*   **Lingkup:** Semua program yang **BELUM** mencapai status `BERITA_ACARA`.
*   **Kolom:** Nama, Sektor, Lokasi, Status, Last Update.
*   **Penggunaan:** Untuk rapat monitoring mingguan progres pembahasan.

### Laporan B: Program Final (`ReportProgramFinal`)
*   **Lingkup:** Semua program dengan status `BERITA_ACARA`.
*   **Kolom:** Nama, Sektor, Lokasi, Keputusan (Lanjut/Tangguh), Tanggal BA.
*   **Penggunaan:** Dokumen arsip hasil keputusan pimpinan.

### Laporan C: Catatan Konsolidasi (`ReportCatatan`)
*   **Lingkup:** Seluruh `Catatan` yang pernah diinput ke sistem.
*   **Fitur:** Filter berdasarkan Tahap (Dibahas PU, Catatan KL, Konsolidasi Pemda).
*   **Penggunaan:** Menelusuri rekam jejak masukan atau masalah pada suatu program.

## 3. Fitur Ekspor & Keamanan

### Mekanisme Ekspor
*   **Format:** CSV (Comma Separated Values) untuk kompatibilitas Excel.
*   **Metadata (Watermark):**
    *   Header: "READ ONLY SYSTEM OUTPUT"
    *   User: Nama user pencetak.
    *   Timestamp: Waktu unduh.
*   **Logic:** Streaming response untuk performa memori (chunking 100 record).

### Hak Akses (Access Control)
*   **ADMIN:** Akses penuh ke Dashboard dan Laporan.
*   **PU_PUSAT:** Akses penuh ke Dashboard dan Laporan (Read-Only).
*   **Konsistensi:** Kedua role melihat angka yang sama persis (Single Source of Truth).

## 4. Query & Performance Strategy
*   **Indexing:** Kolom `status`, `sektor`, `keputusan` diasumsikan terindeks (implicit index on FK/Enum often sufficient for low volume, add manual index if volume > 100k).
*   **Eager Loading:** Report menggunakan `with('beritaAcara')` dan `with('program')` untuk menghindari N+1 query problem.
*   **No Logic:** Tidak ada manipulasi data di layer View/Widget. Semua angka murni hasil agregasi database.
