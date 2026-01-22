<?php

namespace Filament\Tests\Feature;

use App\Enums\ProgramStatus;
use App\Enums\UserRole;
use App\Models\BeritaAcara;
use App\Models\Program;
use App\Models\User;
use App\Policies\ProgramPolicy;
use Filament\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExternalAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $pemdaJabar;
    protected User $klSDA;
    protected Program $programJabar;
    protected Program $programJatim;
    protected Program $programSDA;
    protected Program $programBinaMarga;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemdaJabar = User::create([
            'name' => 'Pemda Jabar',
            'email' => 'jabar@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::PEMDA,
            'pemda_scope' => ['lokasi' => 'Jawa Barat'],
        ]);

        $this->klSDA = User::create([
            'name' => 'KL SDA',
            'email' => 'sda@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::KL,
            'kl_scope' => ['sektor' => 'SDA'],
        ]);

        $this->programJabar = Program::create([
            'nama_program' => 'Program Jabar',
            'sektor' => 'SDA',
            'lokasi' => 'Bandung, Jawa Barat',
            'estimasi_biaya' => 1000,
            'status' => ProgramStatus::TERDAFTAR,
            'created_by' => 1, // dummy
        ]);

        $this->programJatim = Program::create([
            'nama_program' => 'Program Jatim',
            'sektor' => 'SDA',
            'lokasi' => 'Surabaya, Jawa Timur',
            'estimasi_biaya' => 1000,
            'status' => ProgramStatus::TERDAFTAR,
            'created_by' => 1,
        ]);

        $this->programSDA = $this->programJabar; // Reuse
        $this->programBinaMarga = Program::create([
            'nama_program' => 'Program Bina Marga',
            'sektor' => 'Bina Marga',
            'lokasi' => 'Jakarta',
            'estimasi_biaya' => 1000,
            'status' => ProgramStatus::TERDAFTAR,
            'created_by' => 1,
        ]);
    }

    public function test_pemda_scope_access()
    {
        $policy = new ProgramPolicy();

        // Should see Jabar
        $this->assertTrue($policy->view($this->pemdaJabar, $this->programJabar));

        // Should NOT see Jatim
        $this->assertFalse($policy->view($this->pemdaJabar, $this->programJatim));
    }

    public function test_kl_scope_access()
    {
        $policy = new ProgramPolicy();

        // Should see SDA
        $this->assertTrue($policy->view($this->klSDA, $this->programSDA));

        // Should NOT see Bina Marga
        $this->assertFalse($policy->view($this->klSDA, $this->programBinaMarga));
    }

    public function test_external_cannot_create_update_delete()
    {
        $policy = new ProgramPolicy();

        $this->assertFalse($policy->create($this->pemdaJabar));
        $this->assertFalse($policy->update($this->pemdaJabar, $this->programJabar));
        $this->assertFalse($policy->delete($this->pemdaJabar, $this->programJabar));

        $this->assertFalse($policy->create($this->klSDA));
        $this->assertFalse($policy->update($this->klSDA, $this->programSDA));
        $this->assertFalse($policy->delete($this->klSDA, $this->programSDA));
    }
}
