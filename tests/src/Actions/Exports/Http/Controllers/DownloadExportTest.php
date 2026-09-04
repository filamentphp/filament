<?php

use Filament\Actions\Exports\Downloaders\Contracts\Downloader;
use Filament\Actions\Exports\Enums\Contracts\ExportFormat as ExportFormatInterface;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
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

class TestDownloadExportExporter extends Exporter
{
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export completed';
    }
}

class TestCustomDownloader implements Downloader
{
    public function __construct(
        protected ExportFormatInterface $format,
    ) {}

    public function __invoke(Export $export): RedirectResponse
    {
        $extension = match ($this->format) {
            ExportFormat::Csv => 'csv',
            ExportFormat::Xlsx => 'xlsx',
            default => 'unknown',
        };

        return redirect()->away("https://example.com/export.{$extension}");
    }
}

class TestCustomDownloaderExporter extends Exporter
{
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export completed';
    }

    public static function getDownloader(ExportFormatInterface $format): Downloader
    {
        return app(TestCustomDownloader::class, ['format' => $format]);
    }
}

function createExportForOwner(User $owner, string $exporter = TestDownloadExportExporter::class): Export
{
    return Export::create([
        'file_disk' => 'local',
        'file_name' => 'export',
        'exporter' => $exporter,
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

it('uses the exporter\'s `getDownloader()` override for the requested format', function (string $format): void {
    $owner = User::factory()->create();

    $export = createExportForOwner($owner, exporter: TestCustomDownloaderExporter::class);

    $this->actingAs($owner)
        ->get(signedExportDownloadUrl($export, format: $format))
        ->assertRedirect("https://example.com/export.{$format}");
})->with([
    'CSV' => 'csv',
    'XLSX' => 'xlsx',
]);

it('uses the CSV format downloader when the stored `Exporter` class does not exist', function (): void {
    $owner = User::factory()->create();

    $export = createExportForOwner($owner, exporter: 'App\\Filament\\Exports\\MissingExporter');

    fakeExportFile($export);

    $response = $this->actingAs($owner)
        ->get(signedExportDownloadUrl($export))
        ->assertStatus(200);

    expect($response->streamedContent())->toContain('id,name');
});
