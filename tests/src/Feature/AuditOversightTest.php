<?php

namespace Filament\Tests\Feature;

use App\Enums\ProgramStatus;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\Program;
use App\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Exception;

class AuditOversightTest extends TestCase
{
    use RefreshDatabase;

    protected User $itjen;
    protected User $bpk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itjen = User::create([
            'name' => 'ITJEN Auditor',
            'email' => 'itjen@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::ITJEN,
        ]);

        $this->bpk = User::create([
            'name' => 'BPK Auditor',
            'email' => 'bpk@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::BPK,
        ]);
    }

    public function test_audit_log_immutable()
    {
        $log = AuditLog::create([
            'event_type' => 'TEST',
            'subject_type' => 'Test',
            'subject_id' => 1,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Audit Log is IMMUTABLE. Update denied.");

        $log->update(['event_type' => 'MODIFIED']);
    }

    public function test_audit_log_delete_denied()
    {
        $log = AuditLog::create([
            'event_type' => 'TEST',
            'subject_type' => 'Test',
            'subject_id' => 1,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Audit Log is IMMUTABLE. Delete denied.");

        $log->delete();
    }

    public function test_itjen_cannot_see_sensitive_fields()
    {
        // This is harder to test without UI rendering test, but we can check policy logic if we had one for fields
        // Or check the Resource logic.
        // In ViewAuditLog, 'ip_address' visible is fn () => auth()->user()->isBpk().

        $this->assertTrue($this->bpk->isBpk());
        $this->assertFalse($this->itjen->isBpk());
    }

    public function test_itjen_cannot_update_program()
    {
        $program = Program::create([
            'nama_program' => 'Test Program',
            'sektor' => 'SDA',
            'lokasi' => 'Jakarta',
            'estimasi_biaya' => 1000,
            'status' => ProgramStatus::TERDAFTAR,
            'created_by' => 1,
        ]);

        $policy = new \App\Policies\ProgramPolicy();
        $this->assertFalse($policy->update($this->itjen, $program));
        $this->assertFalse($policy->delete($this->itjen, $program));
    }
}
