# SPESIFIKASI AUDIT DAN PENGAWASAN (Fase 6)

Dokumen ini menjelaskan mekanisme pengawasan independen oleh ITJEN dan BPK pada Sistem E-Gov Bank Program Pembangunan PU.

## 1. Peran Pengawas (Oversight Roles)

| Role | Institusi | Kewenangan | Fokus Audit |
| :--- | :--- | :--- | :--- |
| **ITJEN** | Inspektorat Jenderal | **Limited Read-Only** | Kepatuhan internal, kelengkapan dokumen. |
| **BPK** | Badan Pemeriksa Keuangan | **Full Read-Only** | Audit keuangan, forensik data, state transition. |

## 2. Batas Kewenangan (Authority Boundaries)

Sistem menerapkan prinsip **"Zero Control, Full Visibility"** untuk auditor.

*   **View All:** Auditor dapat melihat seluruh data program tanpa batasan wilayah/sektor (berbeda dengan PEMDA/KL).
*   **No Action:** Auditor **DILARANG KERAS** melakukan Create, Update, Delete, atau Advance Status.
*   **Immutable Logs:** Auditor juga tidak bisa menghapus jejak audit mereka sendiri.

## 3. Struktur Audit Log (Upgraded)

Audit Log ditingkatkan untuk memenuhi standar forensik digital:

*   **Event Type:** Jenis aksi (CREATE, UPDATE, STATUS_CHANGE, VIEW_DETAIL, DOWNLOAD_BA).
*   **Actor Context:** Siapa (ID, Nama, Role), Dari mana (IP, User Agent).
*   **Data Change:** Snapshot `old_values` vs `new_values`.
*   **Immutability:** Dilindungi oleh `AuditLogObserver` yang memblokir `UPDATE` dan `DELETE` di level aplikasi.

## 4. Mitigasi Manipulasi (Anti-Tampering)

1.  **Database Level Protection:** Observer melempar Exception jika ada query `UPDATE/DELETE` ke tabel audit.
2.  **Separate Panel:** Auditor masuk via `/audit` panel yang tidak memiliki form input sama sekali (hanya View & Infolist).
3.  **Watermarking:** Hasil export CSV auditor ditandai dengan "OFFICIAL STATE AUDIT" (untuk BPK) atau "FOR INTERNAL AUDIT" (untuk ITJEN) beserta Timestamp dan User pencetak.
4.  **Status History:** Tabel terpisah `program_status_histories` mencatat kronologis perubahan status untuk memudahkan rekonstruksi kejadian.

## 5. Kesesuaian Standar
Desain ini mendukung prinsip SPIP (Sistem Pengendalian Intern Pemerintah) dan kebutuhan pemeriksaan BPK RI dengan menyediakan:
*   **Traceability:** Siapa melakukan apa dan kapan.
*   **Transparency:** Tidak ada data yang disembunyikan dari pemeriksa eksternal (BPK).
*   **Accountability:** Sistem menolak intervensi manual tanpa jejak.
