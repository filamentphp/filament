<?php

use Filament\Actions\Imports\Downloaders\Contracts\Downloader;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

uses(TestCase::class, RefreshDatabase::class);

// A real importer so the controller can resolve `$import->importer` for the
// `getFailedRowsDownloader()` static call instead of a non-existent class string.
class DownloadFailureTestImporter extends Importer
{
    public static function getColumns(): array
    {
        return [];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return '';
    }
}

class TestCustomImportFailureDownloader implements Downloader
{
    public function __invoke(Import $import): RedirectResponse
    {
        return redirect()->away('https://example.com/import-failures.csv');
    }
}

class TestCustomDownloadFailureImporter extends Importer
{
    public static function getColumns(): array
    {
        return [];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return '';
    }

    public static function getFailedRowsDownloader(): Downloader
    {
        return app(TestCustomImportFailureDownloader::class);
    }
}

// Policy that grants `view` access to any user, so that a non-owner can download.
class AllowImportViewPolicy
{
    public function view(Authenticatable $user, Import $import): bool
    {
        return true;
    }
}

// Policy that denies `view` access to every user.
class DenyImportViewPolicy
{
    public function view(Authenticatable $user, Import $import): bool
    {
        return false;
    }
}

function createImportForOwner(User $owner, string $importer = DownloadFailureTestImporter::class): Import
{
    return Import::create([
        'file_name' => 'import.csv',
        'file_path' => 'imports/import.csv',
        'importer' => $importer,
        'total_rows' => 1,
        'successful_rows' => 0,
        'user_id' => $owner->getKey(),
    ]);
}

function signedImportFailureDownloadUrl(Import $import): string
{
    return URL::signedRoute('filament.imports.failed-rows.download', [
        'import' => $import,
    ], absolute: false);
}

it('aborts with `401` when the user is not authenticated', function (): void {
    $owner = User::factory()->create();

    $import = createImportForOwner($owner);

    $this->get(signedImportFailureDownloadUrl($import))
        ->assertStatus(401);
});

it('aborts with `403` when an authenticated non-owner has no `view` policy', function (): void {
    $owner = User::factory()->create();
    $nonOwner = User::factory()->create();

    $import = createImportForOwner($owner);

    $this->actingAs($nonOwner)
        ->get(signedImportFailureDownloadUrl($import))
        ->assertStatus(403);
});

it('streams with `200` when the authenticated owner has no `view` policy', function (): void {
    $owner = User::factory()->create();

    $import = createImportForOwner($owner);

    $this->actingAs($owner)
        ->get(signedImportFailureDownloadUrl($import))
        ->assertStatus(200);
});

it('streams with `200` when a `view` policy allows a non-owner', function (): void {
    Gate::policy(Import::class, AllowImportViewPolicy::class);

    $owner = User::factory()->create();
    $nonOwner = User::factory()->create();

    $import = createImportForOwner($owner);

    $this->actingAs($nonOwner)
        ->get(signedImportFailureDownloadUrl($import))
        ->assertStatus(200);
});

it('aborts with `403` when a `view` policy denies the user', function (): void {
    Gate::policy(Import::class, DenyImportViewPolicy::class);

    $owner = User::factory()->create();

    $import = createImportForOwner($owner);

    $this->actingAs($owner)
        ->get(signedImportFailureDownloadUrl($import))
        ->assertStatus(403);
});

it('uses the importer\'s `getFailedRowsDownloader()` override', function (): void {
    $owner = User::factory()->create();

    $import = createImportForOwner($owner, importer: TestCustomDownloadFailureImporter::class);

    $this->actingAs($owner)
        ->get(signedImportFailureDownloadUrl($import))
        ->assertRedirect('https://example.com/import-failures.csv');
});
