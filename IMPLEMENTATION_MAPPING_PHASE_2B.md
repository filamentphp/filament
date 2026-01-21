# PEMETAAN IMPLEMENTASI FASE 2B (LARAVEL + FILAMENT)

Dokumen ini memetakan desain konseptual (Fase 1 & 2A) ke dalam komponen teknis implementasi menggunakan Framework Laravel dan Filament. Fokus utama adalah penerjemahan aturan bisnis yang ketat menjadi logika kode yang aman dan terstruktur.

## 1. Ringkasan Pendek
Implementasi menggunakan **Service Layer (Service–Action Pattern)** sebagai Application Layer. Repository terpisah tidak digunakan; Eloquent Model diakses langsung oleh Service. Filament hanya berfungsi sebagai *Presentation Layer* (UI), sementara *Application Layer* ditangani oleh Service Class, dan *Authorization* dijaga ketat oleh Laravel Policies.

## 2. Mapping Entity → Laravel Model

Berikut adalah pemetaan entitas konseptual ke Model Eloquent Laravel:

| Entity Konseptual | Laravel Model | Relasi & Keterangan |
| :--- | :--- | :--- |
| **Program** | `App\Models\Program` | • `hasMany(Catatan::class)`<br>• `hasOne(BeritaAcara::class)`<br>• Field `status` menggunakan PHP Enum (`ProgramStatus`).<br>• Field `created_by` menyimpan ID User. |
| **Catatan** | `App\Models\Catatan` | • `belongsTo(Program::class)`<br>• `belongsTo(User::class, 'dicatat_oleh')`<br>• Model ini *immutable* (tidak boleh diedit setelah create). |
| **Berita_Acara** | `App\Models\BeritaAcara` | • `belongsTo(Program::class)`<br>• `belongsTo(User::class, 'dibuat_oleh')`<br>• Menyimpan path file PDF. |
| **Audit Log** | `App\Models\AuditLog` | • Atau menggunakan package `spatie/laravel-activitylog`.<br>• Mencatat `subject_type`, `subject_id`, `causer_id`, `properties` (old/new values). |

## 3. Mapping Modul → Service Layer

Logika bisnis tidak boleh bocor ke Filament Resource. Semua logika diletakkan di Service Class:

### 🔹 `App\Services\ProgramService`
*   **Tanggung Jawab:** Menangani CRUD dasar Program.
*   **Method Utama:**
    *   `createProgram(array $data): Program`
    *   `updateProgram(Program $program, array $data): Program`
*   **Kenapa Service?** Memastikan setiap create/update memicu audit log yang seragam, terlepas dari mana request berasal (Web UI atau API).

### 🔹 `App\Services\WorkflowService`
*   **Tanggung Jawab:** Satu-satunya pintu untuk mengubah status.
*   **Method Utama:**
    *   `advanceStatus(Program $program): void`
*   **Logika:**
    *   Mengecek status saat ini (misal: `TERDAFTAR`).
    *   Menentukan status berikutnya (misal: `DIBAHAS_PU`).
    *   Memvalidasi syarat (misal: "Apakah Catatan sudah ada?").
    *   Mengupdate status model.
    *   **PENTING:** Controller/Resource dilarang keras melakukan `$program->update(['status' => ...])` secara langsung.

### 🔹 `App\Services\CatatanService`
*   **Tanggung Jawab:** Menangani input catatan pembahasan.
*   **Method Utama:**
    *   `addCatatan(Program $program, array $data): Catatan`
*   **Logika:** Mengisi otomatis `dicatat_oleh` dengan user yang login, memastikan `tahap` sesuai dengan status program saat ini.

### 🔹 `App\Services\BeritaAcaraService`
*   **Tanggung Jawab:** Finalisasi program.
*   **Method Utama:**
    *   `finalizeProgram(Program $program, UploadedFile $file, array $data): BeritaAcara`
*   **Logika:** Upload file ke storage secure, create record BeritaAcara, dan memanggil `WorkflowService` untuk update status ke `BERITA_ACARA`.

## 4. Mapping Role → Authorization (Policy)

Implementasi menggunakan Laravel Policies (`php artisan make:policy`). User memiliki kolom `role` (Enum: 'ADMIN', 'PU_PUSAT').

### Matrix Policy

| Model / Feature | Action | Policy Method | Logic Authorization |
| :--- | :--- | :--- | :--- |
| **Program** | View List | `viewAny` | `ADMIN` OR `PU_PUSAT` |
| | View Detail | `view` | `ADMIN` OR `PU_PUSAT` |
| | Create | `create` | **ONLY** `ADMIN` |
| | Update | `update` | **ONLY** `ADMIN` |
| | Delete | `delete` | **Delete Program: Tidak diizinkan dalam sistem aplikasi (No Delete Policy).**<br>Program hanya dapat diarsipkan atau dihapus melalui prosedur administratif di luar aplikasi (database-level/manual), bukan melalui UI atau API aplikasi. |
| **Catatan** | Create | `create` | **ONLY** `ADMIN` |
| | View | `viewAny` | `ADMIN` OR `PU_PUSAT` |
| **BeritaAcara** | Create | `create` | **ONLY** `ADMIN` |
| | View/Download | `view` | `ADMIN` OR `PU_PUSAT` |

**Catatan:** Jika user `PU_PUSAT` mencoba mengakses URL edit paksa, Policy akan melempar `403 Forbidden`.

## 5. Mapping Workflow → State Guard

Workflow dijaga ketat di Backend menggunakan **State Pattern** sederhana atau validasi Enum.

1.  **Status Enum:**
    ```php
    enum ProgramStatus: string {
        case TERDAFTAR = 'terdaftar';
        case DIBAHAS_PU = 'dibahas_pu';
        case CATATAN_KL = 'catatan_kl';
        case KONSOLIDASI_PEMDA = 'konsolidasi_pemda';
        case BERITA_ACARA = 'berita_acara';
    }
    ```

2.  **Validasi Transisi (di WorkflowService):**
    ```php
    public function advanceStatus(Program $program) {
        match ($program->status) {
            ProgramStatus::TERDAFTAR => $this->setStatus($program, ProgramStatus::DIBAHAS_PU),
            ProgramStatus::DIBAHAS_PU => $this->setStatus($program, ProgramStatus::CATATAN_KL),
            // ... dan seterusnya
            default => throw new DomainException("Status cannot be advanced further."),
        };
    }
    ```

## 6. Mapping ke Filament Resource

Filament Resource di `App\Filament\Resources\ProgramResource` akan menyesuaikan tampilan berdasarkan Role.

### A. ProgramResource
*   **Form Schema:**
    *   Seluruh field (`nama_program`, `sektor`, dll) akan memiliki chain `->disabled(fn() => ! auth()->user()->isAdmin())`.
    *   Ini membuat form menjadi *Read-Only* secara visual bagi PU_PUSAT.
*   **Table Actions:**
    *   `EditAction::make()->visible(fn() => auth()->user()->isAdmin())`
    *   `CreateAction::make()->visible(fn() => auth()->user()->isAdmin())`
    *   Custom Action `AdvanceStatusAction`:
        *   Tombol ini memicu `WorkflowService::advanceStatus`.
        *   Visibility: `isAdmin()` AND `! isFinalStatus()`.
        *   RequiresConfirmation: Yes.

### B. Relation Managers (Catatan & Berita Acara)
*   **CatatanRelationManager:**
    *   Table Header Action `CreateAction`: Hanya visible untuk ADMIN.
    *   Row Action `Edit/Delete`: **Disabled/Hidden** untuk semua (Catatan bersifat kekal/audit log).
*   **BeritaAcaraRelationManager:**
    *   Hanya mengizinkan 1 record (hasOne).
    *   Create/Upload Action: Hanya visible untuk ADMIN.
    *   View Action: Visible untuk semua.

## 7. Enforcement Backend (Boundary UI vs Logic)

Penting untuk dipahami bahwa Filament hanyalah antarmuka. Keamanan sesungguhnya ada di lapisan bawah:

1.  **Request Manual:** Jika seseorang menggunakan Postman dengan token PU_PUSAT menembak endpoint `/admin/programs/create`, **ProgramPolicy** akan mencegat dan membatalkan request.
2.  **Bypass Workflow:** Jika developer mencoba mengubah status langsung lewat Database Seeder atau Tinker tanpa lewat Service, audit log mungkin tidak tercatat (pelanggaran SOP). Oleh karena itu, di kode aplikasi, pemanggilan `Program::update(['status' => ...])` harus dihindari dan diganti `WorkflowService`.
3.  **Model Observers:** Model Observer **WAJIB** dipasang pada Model Program sebagai last-resort guard untuk kolom status. Observer hanya mengizinkan perubahan status jika perubahan tersebut dilakukan melalui `WorkflowService`. Perubahan status langsung melalui Eloquent (`$program->update()`, Seeder, Tinker, atau query manual) harus diblokir.

---
*Dokumen ini menjadi panduan bagi developer dalam menulis kode, memastikan setiap baris kode Filament dan Laravel patuh pada Blueprint Fase 1.*
