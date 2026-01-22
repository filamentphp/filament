# DEFINITION OF DONE (DoD) - FINAL
**Sistem E-Gov Bank Program Pembangunan Infrastruktur PU**

Dokumen ini menandai batas akhir pengembangan sistem (Scope Lock). Sistem dinyatakan **SELESAI** dan **SIAP DEPLOY** apabila seluruh kriteria di bawah ini terpenuhi (Status: CHECKED).

---

## 1. Kriteria Fungsional (Functional Completion)

### Manajemen Program & Workflow
- [ ] **ADMIN** dapat membuat program baru dengan status awal `TERDAFTAR`.
- [ ] **ADMIN** dapat memajukan status program secara linear (`TERDAFTAR` → `DIBAHAS_PU` → `CATATAN_KL` → `KONSOLIDASI_PEMDA` → `BERITA_ACARA`).
- [ ] Sistem menolak perpindahan status yang melompat atau mundur.
- [ ] Sistem memblokir perubahan status setelah mencapai `BERITA_ACARA` (Final).

### Input Data Pendukung
- [ ] **ADMIN** dapat menambahkan `Catatan` pada setiap tahap pembahasan.
- [ ] **ADMIN** hanya dapat upload `Berita Acara` (PDF) saat status program `KONSOLIDASI_PEMDA`.
- [ ] Input `Catatan` memvalidasi kesesuaian tahap program (mismatch → reject).

### Pelaporan & Dashboard
- [ ] **Dashboard Eksekutif** menampilkan statistik realtime (Status, Sektor, Keputusan) untuk Internal User.
- [ ] **Laporan Tabelaris** tersedia untuk Program Aktif, Program Final, dan Catatan Konsolidasi.
- [ ] Fitur **Export CSV** berfungsi dengan watermark yang sesuai ("READ ONLY SYSTEM OUTPUT").

---

## 2. Keamanan & Otorisasi (Security & Access Control)

### Role Internal (Core)
- [ ] **ADMIN**: Memiliki akses penuh (Create, Update, Advance Status, Upload BA).
- [ ] **PU_PUSAT**: 100% Read-Only pada seluruh menu Internal Panel (`/admin`). Form disabled, Action buttons hidden.

### Role Eksternal (Limited Scope)
- [ ] **PEMDA**: Hanya dapat melihat program yang lokasi-nya mengandung nama wilayah scope user.
- [ ] **KL**: Hanya dapat melihat program yang sektor-nya sesuai scope user.
- [ ] **Panel Isolasi**: User eksternal hanya bisa login via `/external`. Tidak bisa akses `/admin` atau `/audit`.
- [ ] **Strict Read-Only**: User eksternal tidak memiliki tombol Create/Edit/Delete sama sekali.

### Anti-Tampering (Hardening)
- [ ] **Direct Update Block**: `ProgramObserver` melempar Exception jika status diubah tanpa `WorkflowGuard` (mencegah Tinker/Seeder abuse).
- [ ] **No Delete Policy**: Program tidak dapat dihapus melalui UI aplikasi (Soft Delete maupun Force Delete disabled).
- [ ] **Secure Storage**: File Berita Acara tersimpan di disk `local`/`private` dan hanya bisa diunduh oleh user berhak.

---

## 3. Kesiapan Audit & Pengawasan (Audit Readiness)

### Peran Pengawas (Independent Oversight)
- [ ] **ITJEN**: Memiliki akses Read-Only terbatas (tanpa melihat IP/User Agent sensitif).
- [ ] **BPK**: Memiliki akses Read-Only penuh (termasuk detail teknis Audit Log & History).
- [ ] **Panel Khusus**: Auditor mengakses sistem via `/audit`.

### Integritas Data Audit
- [ ] **Audit Log Lengkap**: Mencatat Event Type, Actor, Role, Old/New Values, IP Address, dan User Agent.
- [ ] **Status History**: Setiap perpindahan status tercatat di tabel `program_status_histories` (Who, When, From, To).
- [ ] **Immutability**: `AuditLog` dan `ProgramStatusHistory` **TIDAK BISA** diubah atau dihapus (Observer throws Exception).
- [ ] **Watermarked Export**: Export data oleh auditor memiliki watermark ("OFFICIAL STATE AUDIT" / "FOR INTERNAL AUDIT").

---

## 4. Integritas Teknis (Technical Integrity)

### Arsitektur
- [ ] **Service Layer Pattern**: Seluruh logika bisnis terpusat di `App\Services`, bukan di Controller/Resource.
- [ ] **Database Transaction**: Operasi kritikal (Create Program, Advance Status, Finalize BA) dibungkus transaction (Atomic).
- [ ] **Strict Typing**: Penggunaan Enum (`UserRole`, `ProgramStatus`) di seluruh kode.

### Dokumentasi
- [ ] Tersedia `BLUEPRINT.md` (Desain Fungsional).
- [ ] Tersedia `ARCHITECTURE_PHASE_2A.md` (Arsitektur Teknis).
- [ ] Tersedia `IMPLEMENTATION_MAPPING_PHASE_2B.md` (Peta Implementasi).
- [ ] Tersedia `SECURITY_CHECKLIST.md` (Hasil Uji Keamanan).
- [ ] Tersedia `REPORTING_SPEC.md` (Spesifikasi Laporan).
- [ ] Tersedia `EXTERNAL_ACCESS_SPEC.md` (Spesifikasi Akses Eksternal).
- [ ] Tersedia `AUDIT_OVERSIGHT_SPEC.md` (Spesifikasi Audit).

---

**PENGESAHAN:**
Dengan terpenuhinya checklist di atas, pengembangan fitur dinyatakan **DITUTUP**. Fokus selanjutnya adalah deployment dan maintenance infrastruktur.
