<?php

use Filament\Actions\Exports\Models\Export;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

uses(TestCase::class, RefreshDatabase::class);

// Policy that grants `view` access to any user, so that a non-owner can download.
class AllowExportViewPolicy
{
    public function view(Authenticatable $user, Export $export): bool
    {
        return true;
    }
}

// Policy that denies `view` access to every user.
class DenyExportViewPolicy
{
    public function view(Authenticatable $user, Export $export): bool
    {
        return false;
    }
}

function createExportForOwner(User $owner): Export
{
    return Export::create([
        'file_disk' => 'local',
        'file_name' => 'export',
        'exporter' => 'App\\Filament\\Exports\\TestExporter',
        'total_rows' => 1,
        'successful_rows' => 1,
        'user_id' => $owner->getKey(),
    ]);
}

function signedExportDownloadUrl(Export $export, string $format = 'csv'): string
{
    return URL::signedRoute('filament.exports.download', [
        'export' => $export,
        'format' => $format,
    ], absolute: false);
}

function fakeExportFile(Export $export): void
{
    Storage::fake('local');

    Storage::disk('local')->put(
        $export->getFileDirectory() . DIRECTORY_SEPARATOR . 'headers.csv',
        "id,name\n",
    );
}

it('aborts with `401` when the user is not authenticated', function (): void {
    $owner = User::factory()->create();

    $export = createExportForOwner($owner);

    $this->get(signedExportDownloadUrl($export))
        ->assertStatus(401);
});

it('aborts with `403` when an authenticated non-owner has no `view` policy', function (): void {
    $owner = User::factory()->create();
    $nonOwner = User::factory()->create();

    $export = createExportForOwner($owner);

    $this->actingAs($nonOwner)
        ->get(signedExportDownloadUrl($export))
        ->assertStatus(403);
});

it('streams with `200` when the authenticated owner has no `view` policy', function (): void {
    $owner = User::factory()->create();

    $export = createExportForOwner($owner);

    fakeExportFile($export);

    $response = $this->actingAs($owner)
        ->get(signedExportDownloadUrl($export))
        ->assertStatus(200);

    expect($response->streamedContent())->toContain('id,name');
});

it('streams with `200` when a `view` policy allows a non-owner', function (): void {
    Gate::policy(Export::class, AllowExportViewPolicy::class);

    $owner = User::factory()->create();
    $nonOwner = User::factory()->create();

    $export = createExportForOwner($owner);

    fakeExportFile($export);

    $response = $this->actingAs($nonOwner)
        ->get(signedExportDownloadUrl($export))
        ->assertStatus(200);

    expect($response->streamedContent())->toContain('id,name');
});

it('aborts with `403` when a `view` policy denies the user', function (): void {
    Gate::policy(Export::class, DenyExportViewPolicy::class);

    $owner = User::factory()->create();

    $export = createExportForOwner($owner);

    $this->actingAs($owner)
        ->get(signedExportDownloadUrl($export))
        ->assertStatus(403);
});

it('aborts with `404` when the requested format is unknown', function (): void {
    $owner = User::factory()->create();

    $export = createExportForOwner($owner);

    $this->actingAs($owner)
        ->get(signedExportDownloadUrl($export, format: 'unknown'))
        ->assertStatus(404);
});
