<?php

namespace Filament\Tests\Feature;

use App\Enums\ProgramStatus;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\BeritaAcara;
use App\Models\Catatan;
use App\Models\Program;
use App\Models\User;
use App\Services\BeritaAcaraService;
use App\Services\CatatanService;
use App\Services\ProgramService;
use App\Services\WorkflowService;
use DomainException;
use Exception;
use Filament\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AbuseTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $puPusat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::ADMIN,
        ]);

        $this->puPusat = User::create([
            'name' => 'PU Pusat',
            'email' => 'pupusat@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::PU_PUSAT,
        ]);
    }

    // A. Role Abuse
    public function test_role_abuse_pu_pusat_cannot_create_program()
    {
        $this->actingAs($this->puPusat);

        // Try access create page (simulated via Policy check)
        $this->assertFalse($this->puPusat->can('create', Program::class));
    }

    public function test_role_abuse_pu_pusat_cannot_edit_program()
    {
        $this->actingAs($this->puPusat);
        $program = Program::create([
            'nama_program' => 'Test Program',
            'sektor' => 'Sektor A',
            'lokasi' => 'Jakarta',
            'estimasi_biaya' => 1000000,
            'status' => ProgramStatus::TERDAFTAR,
            'created_by' => $this->admin->id,
        ]);

        $this->assertFalse($this->puPusat->can('update', $program));
    }

    public function test_role_abuse_pu_pusat_cannot_create_catatan()
    {
        $this->actingAs($this->puPusat);
        $this->assertFalse($this->puPusat->can('create', Catatan::class));
    }

    public function test_role_abuse_pu_pusat_cannot_create_berita_acara()
    {
        $this->actingAs($this->puPusat);
        $this->assertFalse($this->puPusat->can('create', BeritaAcara::class));
    }

    // B. Workflow Abuse
    public function test_workflow_abuse_cannot_jump_status()
    {
        $this->actingAs($this->admin);
        $program = app(ProgramService::class)->createProgram([
            'nama_program' => 'Test Program',
            'sektor' => 'Sektor A',
            'lokasi' => 'Jakarta',
            'estimasi_biaya' => 1000000,
        ]);

        // Correct next status is DIBAHAS_PU
        // Admin tries to jump via direct update (Observer should catch this even if Service was used improperly, but Service logic itself prevents it)

        // Let's try to bypass service logic but use service? No, WorkflowService enforces next status.
        // Let's verify WorkflowService only advances one step.

        app(WorkflowService::class)->advanceStatus($program);
        $this->assertEquals(ProgramStatus::DIBAHAS_PU, $program->refresh()->status);

        app(WorkflowService::class)->advanceStatus($program);
        $this->assertEquals(ProgramStatus::CATATAN_KL, $program->refresh()->status);

        // Cannot jump back
        // WorkflowService has no method to go back.
    }

    public function test_workflow_abuse_cannot_advance_beyond_final()
    {
        $this->actingAs($this->admin);
        $program = app(ProgramService::class)->createProgram([
            'nama_program' => 'Test Program',
            'sektor' => 'Sektor A',
            'lokasi' => 'Jakarta',
            'estimasi_biaya' => 1000000,
        ]);

        // Advance to end
        app(WorkflowService::class)->advanceStatus($program); // DIBAHAS_PU
        app(WorkflowService::class)->advanceStatus($program); // CATATAN_KL
        app(WorkflowService::class)->advanceStatus($program); // KONSOLIDASI_PEMDA
        app(WorkflowService::class)->advanceStatus($program); // BERITA_ACARA

        $this->expectException(DomainException::class);
        app(WorkflowService::class)->advanceStatus($program);
    }

    // C. Observer Bypass Attempt
    public function test_observer_bypass_direct_update_throws_exception()
    {
        $this->actingAs($this->admin);
        $program = Program::create([
            'nama_program' => 'Test Program',
            'sektor' => 'Sektor A',
            'lokasi' => 'Jakarta',
            'estimasi_biaya' => 1000000,
            'status' => ProgramStatus::TERDAFTAR,
            'created_by' => $this->admin->id,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Perubahan status program hanya boleh dilakukan melalui WorkflowService.');

        // Direct Eloquent update
        $program->update(['status' => ProgramStatus::DIBAHAS_PU]);
    }

    public function test_observer_bypass_program_service_update_ignores_status()
    {
        $this->actingAs($this->admin);
        $program = app(ProgramService::class)->createProgram([
            'nama_program' => 'Test Program',
            'sektor' => 'Sektor A',
            'lokasi' => 'Jakarta',
            'estimasi_biaya' => 1000000,
        ]);

        // Try to sneak status update via updateProgram service
        app(ProgramService::class)->updateProgram($program, [
            'nama_program' => 'Updated Name',
            'status' => ProgramStatus::BERITA_ACARA, // Sneaky
        ]);

        $program->refresh();
        $this->assertEquals('Updated Name', $program->nama_program);
        $this->assertEquals(ProgramStatus::TERDAFTAR, $program->status); // Status should remain
    }

    // D. Catatan Integrity
    public function test_catatan_integrity_mismatch_phase()
    {
        $this->actingAs($this->admin);
        $program = app(ProgramService::class)->createProgram([
            'nama_program' => 'Test Program',
            'sektor' => 'Sektor A',
            'lokasi' => 'Jakarta',
            'estimasi_biaya' => 1000000,
        ]);
        // Status is TERDAFTAR

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Tahap catatan (DIBAHAS_PU) tidak sesuai dengan status program saat ini (TERDAFTAR).');

        app(CatatanService::class)->addCatatan($program, [
            'tahap' => ProgramStatus::DIBAHAS_PU->value, // Mismatch
            'sumber' => 'PU',
            'isi_catatan' => 'Test',
        ]);
    }

    // E. Berita Acara Integrity
    public function test_berita_acara_integrity_wrong_status()
    {
        $this->actingAs($this->admin);
        $program = app(ProgramService::class)->createProgram([
            'nama_program' => 'Test Program',
            'sektor' => 'Sektor A',
            'lokasi' => 'Jakarta',
            'estimasi_biaya' => 1000000,
        ]);
        // Status is TERDAFTAR

        Storage::fake('local');
        $file = UploadedFile::fake()->create('ba.pdf');

        $this->expectException(DomainException::class);
        app(BeritaAcaraService::class)->finalizeProgram($program, $file, [
            'keputusan' => 'LANJUT',
            'ringkasan_kesepakatan' => 'Ok',
            'tanggal' => now(),
        ]);
    }

    public function test_berita_acara_success_flow()
    {
        $this->actingAs($this->admin);
        $program = app(ProgramService::class)->createProgram([
            'nama_program' => 'Test Program',
            'sektor' => 'Sektor A',
            'lokasi' => 'Jakarta',
            'estimasi_biaya' => 1000000,
        ]);

        // Advance to KONSOLIDASI_PEMDA
        app(WorkflowService::class)->advanceStatus($program); // DIBAHAS_PU
        app(WorkflowService::class)->advanceStatus($program); // CATATAN_KL
        app(WorkflowService::class)->advanceStatus($program); // KONSOLIDASI_PEMDA

        Storage::fake('local');
        $file = UploadedFile::fake()->create('ba.pdf', 100, 'application/pdf');

        $ba = app(BeritaAcaraService::class)->finalizeProgram($program, $file, [
            'keputusan' => 'LANJUT',
            'ringkasan_kesepakatan' => 'Ok',
            'tanggal' => now(),
        ]);

        $this->assertDatabaseHas('berita_acaras', ['id' => $ba->id]);
        $this->assertEquals(ProgramStatus::BERITA_ACARA, $program->refresh()->status);

        // Verify File
        // Note: The service uses $file->store(), which generates a hash name.
        // We verify that the file exists in storage.
        Storage::disk('local')->assertExists($ba->file_pdf);
    }

    // Task 2: Audit Trail Verification
    public function test_audit_trail_logging()
    {
        $this->actingAs($this->admin);
        $program = app(ProgramService::class)->createProgram([
            'nama_program' => 'Audit Test',
            'sektor' => 'Audit',
            'lokasi' => 'Log',
            'estimasi_biaya' => 500,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'program.created',
            'subject_type' => Program::class,
            'subject_id' => $program->id,
            'actor_id' => $this->admin->id,
        ]);

        app(WorkflowService::class)->advanceStatus($program);

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'program.status_advanced',
            'subject_id' => $program->id,
        ]);
    }
}
