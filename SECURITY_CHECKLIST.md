# SECURITY CHECKLIST & ABUSE TEST REPORT

Dokumen ini berisi hasil pengujian keamanan dan integritas sistem E-Gov Bank Program Pembangunan PU. Pengujian dilakukan untuk memastikan kepatuhan terhadap aturan bisnis yang ketat (Hardening).

## 1. Role Abuse (PU_PUSAT Limitations)

| Test Case | Deskripsi | Hasil Harapan | Status | Catatan |
| :--- | :--- | :--- | :--- | :--- |
| **Create Program** | Login sebagai PU_PUSAT, akses create | **FORBIDDEN (403)** | ✅ PASS | Policy `create` return false. |
| **Edit Program** | Login sebagai PU_PUSAT, akses edit | **FORBIDDEN (403)** | ✅ PASS | Policy `update` return false. |
| **Submit Catatan** | Login sebagai PU_PUSAT, coba add catatan | **FORBIDDEN (403)** | ✅ PASS | Policy `create` return false. |
| **Upload BA** | Login sebagai PU_PUSAT, coba upload BA | **FORBIDDEN (403)** | ✅ PASS | Policy `create` return false. |

## 2. Workflow Abuse (Status Integrity)

| Test Case | Deskripsi | Hasil Harapan | Status | Catatan |
| :--- | :--- | :--- | :--- | :--- |
| **Jump Status** | Admin mencoba melompati status (misal: TERDAFTAR → KONSOLIDASI) | **GAGAL / TERTOLAK** | ✅ PASS | `WorkflowService` hanya mengizinkan `getNextStatus` linear. |
| **Advance Final** | Admin advance status saat BERITA_ACARA | **EXCEPTION** | ✅ PASS | `DomainException` dilempar. |
| **Manual Update** | Request manual update field status via API/Controller | **IGNORED / BLOCKED** | ✅ PASS | `ProgramService` menghapus `status` dari payload; Observer memblokir direct update. |

## 3. Observer & Guard Bypass

| Test Case | Deskripsi | Hasil Harapan | Status | Catatan |
| :--- | :--- | :--- | :--- | :--- |
| **Direct Eloquent** | `Program::update(['status' => ...])` | **EXCEPTION** | ✅ PASS | `ProgramObserver` melempar Exception jika `WorkflowGuard` tidak aktif. |
| **Service Bypass** | `ProgramService::updateProgram` dengan field status | **STATUS TETAP** | ✅ PASS | Logic service secara eksplisit melakukan `unset($data['status'])`. |

## 4. Data Integrity (Catatan & BA)

| Test Case | Deskripsi | Hasil Harapan | Status | Catatan |
| :--- | :--- | :--- | :--- | :--- |
| **Phase Mismatch** | Input Catatan dengan `tahap` ≠ `program->status` | **EXCEPTION** | ✅ PASS | `CatatanService` memvalidasi kesesuaian tahap. |
| **Wrong BA Status** | Upload BA saat status ≠ KONSOLIDASI_PEMDA | **EXCEPTION** | ✅ PASS | `BeritaAcaraService` memvalidasi status program. |
| **File Storage** | Upload File BA | **SECURED** | ✅ PASS | File disimpan di disk 'local' (atau private) via `store()`, hash filename. |

## 5. Audit Trail Verification

| Event | Ketersediaan Data | Status |
| :--- | :--- | :--- |
| `program.created` | Actor ID, Data Program | ✅ PASS |
| `program.updated` | Old vs New Data | ✅ PASS |
| `program.status_advanced` | From Status, To Status, User | ✅ PASS |
| `catatan.created` | Program ID, New Data | ✅ PASS |
| `berita_acara.created` | File Path | ✅ PASS |
| `program.finalized` | Timestamp | ✅ PASS |

## Kesimpulan

Sistem telah melalui proses **HARDENING** dan dinyatakan aman dari manipulasi umum yang melanggar workflow.
*   **Role Enforcement**: Ketat (Policy Level).
*   **Workflow**: Linear & Locked (Service + Observer Level).
*   **Data Integrity**: Validated (Service Level).
*   **Audit**: Complete (Service Level).

**Sistem siap untuk fase deployment/UAT.**
