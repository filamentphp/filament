<?php

use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

uses(TestCase::class);

class AuthorizationTestExporter extends Exporter
{
    public static function getColumns(): array
    {
        return [];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return '';
    }
}

class AuthorizationTestImporter extends Importer
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

class DownloadReviewerPolicy
{
    public function view(Authenticatable $user, Export | Import $download): bool
    {
        return $user->name === 'Download reviewer';
    }
}

class DownloadPolicyWithoutView {}

class DownloadCustomUser extends User
{
    // Reuse a separate fixture table to exercise colliding IDs across user models.
    protected $table = 'teams';
}

beforeEach(function (): void {
    Storage::fake('local');
    $this->owner = User::factory()->create();
    $this->nonOwner = User::factory()->create(['name' => 'Download reviewer']);

    config()->set('auth.guards.downloads', ['driver' => 'session', 'provider' => 'users']);
});

function createSensitiveDownload(string $type, User $owner): array
{
    if ($type === 'export') {
        $download = Export::create([
            'file_disk' => 'local',
            'file_name' => 'export',
            'exporter' => AuthorizationTestExporter::class,
            'total_rows' => 1,
            'user_id' => $owner->getKey(),
        ]);

        Storage::disk('local')->put($download->getFileDirectory() . '/headers.csv', "name\n");
        Storage::disk('local')->put($download->getFileDirectory() . '/0000000000000001.csv', "confidential-download-row\n");

        return [$download, 'filament.exports.download', ['export' => $download, 'format' => 'csv'], "name\nconfidential-download-row\n"];
    }

    $download = Import::create([
        'file_name' => 'import.csv',
        'file_path' => 'imports/missing-source.csv',
        'importer' => AuthorizationTestImporter::class,
        'total_rows' => 1,
        'user_id' => $owner->getKey(),
    ]);

    $download->failedRows()->create([
        'data' => ['name' => 'confidential-download-row'],
        'validation_error' => 'confidential-error',
    ]);

    // Failure downloads use persisted rows, not the original uploaded file.
    return [$download, 'filament.imports.failed-rows.download', ['import' => $download], "\xEF\xBB\xBFname,error\nconfidential-download-row,confidential-error\n"];
}

it('uses only a valid relative signature to select `authGuard` before authorization', function (string $type, string $signature, ?string $defaultUser, ?string $guardUser, int $status): void {
    [$download, $route, $parameters, $content] = createSensitiveDownload($type, $this->owner);

    if ($defaultUser !== null) {
        auth('web')->setUser($this->{$defaultUser});
    }

    if ($guardUser !== null) {
        auth('downloads')->setUser($this->{$guardUser});
    }

    // Do not use `actingAs()` here: it also changes the default guard.
    auth()->shouldUse('web');

    $parameters['authGuard'] = 'downloads';

    $url = match ($signature) {
        'valid' => URL::signedRoute($route, $parameters, absolute: false),
        'expired' => URL::temporarySignedRoute($route, now()->subMinute(), $parameters, absolute: false),
        'tampered' => URL::signedRoute($route, [...$parameters, 'authGuard' => 'web'], absolute: false),
        'unsigned' => route($route, $parameters, absolute: false),
    };

    if ($signature === 'tampered') {
        $url = str_replace('authGuard=web', 'authGuard=downloads', $url);
    }

    $this->expectOutputString('');

    $response = $this->get($url)->assertStatus($status);

    if ($status === 200) {
        expect($response->streamedContent())->toBe($content);
    } else {
        $response->assertNotStreamed()->assertDontSee('confidential-', escape: false);
    }
})->with(['export', 'import'])->with([
    'signed owner, default non-owner' => ['valid', 'nonOwner', 'owner', 200],
    'signed non-owner, default owner' => ['valid', 'owner', 'nonOwner', 403],
    'signed guest, default owner' => ['valid', 'owner', null, 401],
    'unsigned guard cannot select owner' => ['unsigned', 'nonOwner', 'owner', 403],
    'tampered guard cannot select owner' => ['tampered', 'nonOwner', 'owner', 403],
    'expired guard cannot select owner' => ['expired', 'nonOwner', 'owner', 403],
    'unsigned guard cannot authenticate guest' => ['unsigned', null, 'owner', 401],
    'tampered guard cannot authenticate guest' => ['tampered', null, 'owner', 401],
    'expired guard cannot authenticate guest' => ['expired', null, 'owner', 401],
    'unsigned default owner remains authorized' => ['unsigned', 'owner', 'nonOwner', 200],
    'tampered default owner remains authorized' => ['tampered', 'owner', 'nonOwner', 200],
    'expired default owner remains authorized' => ['expired', 'owner', 'nonOwner', 200],
]);

it('passes the signed guard user to the custom `view()` policy instead of the default user', function (string $type): void {
    [$download, $route, $parameters, $content] = createSensitiveDownload($type, $this->owner);

    Gate::policy($download::class, DownloadReviewerPolicy::class);
    auth('web')->setUser($this->owner);
    auth('downloads')->setUser($this->nonOwner);
    auth()->shouldUse('web');

    $this->expectOutputString('');

    $response = $this->get(URL::signedRoute($route, [...$parameters, 'authGuard' => 'downloads'], absolute: false))->assertOk();

    expect($response->streamedContent())->toBe($content);

    $this->get(URL::signedRoute($route, [...$parameters, 'authGuard' => 'web'], absolute: false))
        ->assertForbidden()->assertNotStreamed()->assertDontSee('confidential-', escape: false);
})->with(['export', 'import']);

it('retains owner authorization when a registered policy has no `view()` method', function (string $type): void {
    [$download, $route, $parameters, $content] = createSensitiveDownload($type, $this->owner);

    Gate::policy($download::class, DownloadPolicyWithoutView::class);
    $url = route($route, $parameters, absolute: false);

    $this->expectOutputString('');

    $this->actingAs($this->nonOwner)->get($url)->assertForbidden()->assertNotStreamed()->assertDontSee('confidential-', escape: false);

    expect($this->actingAs($this->owner)->get($url)->assertOk()->streamedContent())->toBe($content);
})->with(['export', 'import']);

it('distinguishes a custom owner model from a default user with the same ID', function (string $type, bool $polymorphic): void {
    [$download, $route, $parameters, $content] = createSensitiveDownload($type, $this->owner);

    $customOwner = DownloadCustomUser::create(['id' => $this->owner->getKey(), 'name' => 'Custom owner']);

    try {
        if ($polymorphic) {
            // Supply `user_type` at route binding without changing the shared non-polymorphic schema.
            $download->setAttribute('user_type', $customOwner->getMorphClass());
            Route::bind($type, static fn (): Export | Import => $download);
            $download::polymorphicUserRelationship();
        } else {
            app()->bind(Authenticatable::class, DownloadCustomUser::class);
        }

        config()->set('auth.providers.download_users', ['driver' => 'eloquent', 'model' => DownloadCustomUser::class]);
        config()->set('auth.guards.downloads.provider', 'download_users');

        $this->withSession([
            auth('web')->getName() => $this->owner->getKey(),
            auth('downloads')->getName() => $customOwner->getKey(),
        ]);

        auth()->forgetGuards();
        auth()->shouldUse('web');

        $this->expectOutputString('');

        $response = $this->get(URL::signedRoute($route, [...$parameters, 'authGuard' => 'downloads'], absolute: false))->assertOk();

        expect($response->streamedContent())->toBe($content);

        $this->get(URL::signedRoute($route, [...$parameters, 'authGuard' => 'web'], absolute: false))
            ->assertForbidden()->assertNotStreamed()->assertDontSee('confidential-', escape: false);
    } finally {
        $download::polymorphicUserRelationship(false);
    }
})->with(['export', 'import'])->with(['bound model' => false, 'polymorphic model' => true]);
