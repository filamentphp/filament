# SPESIFIKASI AKSES EKSTERNAL (Fase 5)

Dokumen ini menjelaskan desain dan implementasi akses Read-Only untuk pihak eksternal (Pemda & K/L) pada Sistem E-Gov Bank Program Pembangunan PU.

## 1. Role External
Dua role baru ditambahkan untuk mengakomodasi kebutuhan transparansi:

| Role | Deskripsi | Scope Akses |
| :--- | :--- | :--- |
| **PEMDA** | Pemerintah Daerah (Prov/Kab/Kota) | Terbatas pada **Lokasi** wilayahnya. |
| **KL** | Kementerian / Lembaga Lain | Terbatas pada **Sektor** kewenangannya. |

## 2. Aturan Scope (Data Scoping)
Penerapan scope dilakukan di dua layer untuk keamanan berlapis:

### A. Layer Policy (Authorization)
*   **PEMDA:** `ProgramPolicy::view` mengecek apakah string `users.pemda_scope['lokasi']` terkandung dalam `programs.lokasi`.
*   **KL:** `ProgramPolicy::view` mengecek apakah `users.kl_scope['sektor']` sama persis dengan `programs.sektor`.
*   **Deny Default:** Jika scope user kosong, akses ditolak mutlak.

### B. Layer Query (Filament Resource)
*   `ExternalProgramResource` meng-override `getEloquentQuery()`.
*   Query otomatis menambahkan `where` clause sesuai scope user yang login.
*   User tidak akan pernah melihat data di luar scope-nya di list table, meskipun mencoba menebak URL (karena Policy view juga memblokir).

## 3. Desain UI: Separate Panel
Diputuskan menggunakan **Separate Panel** (`/external`) untuk isolasi maksimal.

*   **Keuntungan:**
    *   **Reduced Attack Surface:** Panel external tidak memuat resource sensitive (User Management, Audit Log Internal) sama sekali.
    *   **Simplified UI:** Menu navigasi hanya berisi Dashboard ringkas dan Program List.
    *   **Auditability:** Segregasi traffic log antara `/admin` dan `/external` lebih mudah.

## 4. Matriks Akses (Policy)

| Fitur | ADMIN | PU_PUSAT | PEMDA | KL |
| :--- | :--- | :--- | :--- | :--- |
| **View List** | ✅ All | ✅ All | ✅ Scoped | ✅ Scoped |
| **View Detail** | ✅ | ✅ | ✅ Scoped | ✅ Scoped |
| **Create/Edit** | ✅ | ❌ | ❌ | ❌ |
| **Delete** | ❌ | ❌ | ❌ | ❌ |
| **Download BA** | ✅ | ✅ | ✅ | ✅ |
| **Export Excel** | ✅ | ✅ | ❌ | ❌ |

## 5. Audit & Traceability
Setiap aksi user external dicatat dengan flag khusus `actor_type = EXTERNAL`:
1.  **Login:** Tercatat di sesi.
2.  **View Detail:** Tercatat di `AuditLog` saat halaman dibuka.
3.  **Download BA:** Tercatat di `AuditLog` saat tombol download diklik.

## 6. Alasan Keamanan (Rationale)
*   **No Bulk Export:** Mencegah penyedotan data massal oleh pihak external (Data scraping protection).
*   **No Hidden Actions:** Resource External dibuat terpisah (`ExternalProgramResource`) sehingga tidak ada risiko tombol "Edit" yang hanya disembunyikan CSS/Logic tapi masih ada di PHP class.
*   **Strict Scope:** Mencegah sengketa antar daerah atau antar kementerian dengan hanya menampilkan data yang relevan secara tupoksi.
