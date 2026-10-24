<?php

use Filament\Actions\Imports\Events\ImportChunkProcessed;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\ImportDispatcher;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Jobs\ImportCsv;
use Filament\Actions\Imports\Models\Import;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\PanelRegistry;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Tenancy\TenantScopedUsers\TenantScopedUserResource;
use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;

uses(TestCase::class);
/** @param array<array<string, mixed>> $rows */
$tenantImportJob = function (array $rows): string {
    $import = Import::create([
        'user_id' => auth()->id(),
        'file_name' => 'users.csv',
        'file_path' => 'users.csv',
        'importer' => TenantUserImporter::class,
        'total_rows' => count($rows),
    ]);
    $dispatcher = Bus::getFacadeRoot();
    Bus::fake();

    try {
        app(ImportDispatcher::class)->dispatch($import, [$rows], ['name' => 'name', 'email' => 'email'], [], ImportCsv::class, 'web');

        return serialize(Bus::batched(static fn (): bool => true)->sole()->jobs->sole());
    } finally {
        Bus::swap($dispatcher);
    }
};

beforeEach(function (): void {
    app()->bind(Authenticatable::class, TenantImportUser::class);
    $this->importingUser = TenantImportUser::query()->create(User::factory()->raw());
    $this->actingAs($this->importingUser);
    Filament::setCurrentPanel(Filament::getPanel('tenancy'));
    Filament::setTenant(null);
});

it('preserves tenant scoping and association across serialized import chunks without leaking context', function () use ($tenantImportJob): void {
    $firstTenant = Team::factory()->create();
    $secondTenant = Team::factory()->create();
    $this->importingUser->teams()->attach([$firstTenant->getKey(), $secondTenant->getKey()]);
    $firstRecord = User::factory()->create(['name' => 'First tenant original']);
    $firstRecord->teams()->attach($firstTenant);
    $secondRecord = User::factory()->create(['name' => 'Second tenant original']);
    $secondRecord->teams()->attach($secondTenant);

    Filament::setTenant($firstTenant);
    $firstJob = $tenantImportJob([
        ['id' => $firstRecord->getKey(), 'name' => 'First tenant updated'],
        ['id' => $secondRecord->getKey(), 'name' => 'Cross-tenant overwrite'],
        ['name' => 'First tenant created', 'email' => 'first-import@example.com'],
    ]);
    config()->set('auth.guards.imports', config('auth.guards.web'));
    $secondPanel = Panel::make()
        ->id('import-tenancy')
        ->tenant(Team::class)
        ->authGuard('imports')
        ->resources([TenantScopedUserResource::class]);
    app(PanelRegistry::class)->register($secondPanel);
    auth('imports')->setUser($this->importingUser);
    Filament::setCurrentPanel($secondPanel);
    Filament::setTenant($secondTenant);
    $secondJob = $tenantImportJob([
        ['id' => $secondRecord->getKey(), 'name' => 'Second tenant updated'],
        ['name' => 'Second tenant created', 'email' => 'second-import@example.com'],
    ]);

    $previousPanel = Filament::getPanel('admin');
    $previousTenant = Team::factory()->create();
    $previousUser = User::factory()->create();
    Filament::setCurrentPanel($previousPanel);
    Filament::setTenant($previousTenant);
    $this->actingAs($previousUser);
    auth('imports')->forgetUser();
    $defaultGuard = auth()->guard();
    $panelGuard = auth('imports');
    $contexts = [];
    Event::listen(ImportChunkProcessed::class, function () use (&$contexts): void {
        $contexts[] = [Filament::getCurrentPanel()->getId(), Filament::getTenant()->getKey(), Filament::auth()->id()];
    });

    Bus::dispatchSync(unserialize($firstJob));
    expect($firstRecord->refresh()->name)->toBe('First tenant updated')
        ->and($secondRecord->refresh()->name)->toBe('Second tenant original')
        ->and(User::query()->where('email', 'first-import@example.com')->sole()->teams->modelKeys())->toBe([$firstTenant->getKey()])
        ->and(Filament::getCurrentPanel())->toBe($previousPanel)
        ->and(Filament::getTenant())->toBe($previousTenant)
        ->and(auth()->id())->toBe($previousUser->getKey());

    Bus::dispatchSync(unserialize($secondJob));
    expect($secondRecord->refresh()->name)->toBe('Second tenant updated')
        ->and(User::query()->where('email', 'second-import@example.com')->sole()->teams->modelKeys())->toBe([$secondTenant->getKey()])
        ->and(Filament::getCurrentPanel())->toBe($previousPanel)
        ->and(Filament::getTenant())->toBe($previousTenant)
        ->and(auth()->id())->toBe($previousUser->getKey())
        ->and(auth('imports')->user())->toBeNull()
        ->and($defaultGuard->id())->toBe($previousUser->getKey())
        ->and($panelGuard->user())->toBeNull()
        ->and($contexts)->toBe([
            ['tenancy', $firstTenant->getKey(), $this->importingUser->getKey()],
            ['import-tenancy', $secondTenant->getKey(), $this->importingUser->getKey()],
        ]);
});

it('fails closed and restores context when an import tenant is deleted or access is revoked', function (bool $deleteTenant) use ($tenantImportJob): void {
    $tenant = Team::factory()->create();
    $this->importingUser->teams()->attach($tenant);
    $record = User::factory()->create(['name' => 'Original']);
    $record->teams()->attach($tenant);
    Filament::setTenant($tenant);
    $job = $tenantImportJob([['id' => $record->getKey(), 'name' => 'Unauthorized']]);

    if ($deleteTenant) {
        $this->importingUser->teams()->detach($tenant);
        $record->teams()->detach($tenant);
        $tenant->delete();
    } else {
        $this->importingUser->teams()->detach($tenant);
    }

    Filament::setCurrentPanel(null);
    Filament::setTenant(null);
    auth()->forgetGuards();

    expect(fn () => Bus::dispatchSync(unserialize($job)))->toThrow($deleteTenant ? ModelNotFoundException::class : AuthorizationException::class);
    expect($record->refresh()->name)->toBe('Original')
        ->and(Filament::getCurrentPanel())->toBeNull()
        ->and(Filament::getTenant())->toBeNull()
        ->and(auth()->user())->toBeNull();
})->with(['deleted tenant' => true, 'revoked access' => false]);

it('restores context when import processing or an auth restoration listener throws', function (bool $throwsDuringRestoration) use ($tenantImportJob): void {
    config()->set('auth.guards.imports', config('auth.guards.web'));
    Filament::getCurrentPanel()->authGuard('imports');
    auth('imports')->setUser($this->importingUser);
    $tenant = Team::factory()->create();
    $this->importingUser->teams()->attach($tenant);
    Filament::setTenant($tenant);
    $job = $tenantImportJob([['name' => 'Created', 'email' => 'failure-import@example.com']]);
    Filament::setCurrentPanel(null);
    Filament::setTenant(null);
    auth()->forgetGuards();
    $guard = auth()->guard();
    $panelGuard = auth('imports');
    $previousUser = $throwsDuringRestoration ? User::factory()->create() : null;

    if ($previousUser) {
        $guard->setUser($previousUser);
        Event::listen(Authenticated::class, static function (Authenticated $event) use ($previousUser): void {
            if ($event->user->is($previousUser)) {
                throw new RuntimeException('Auth restoration listener failed.');
            }
        });
    }

    Event::listen(ImportChunkProcessed::class, static fn () => throw new RuntimeException('Import listener failed.'));

    expect(fn () => Bus::dispatchSync(unserialize($job)))->toThrow(RuntimeException::class, $throwsDuringRestoration ? 'Auth restoration listener failed.' : 'Import listener failed.');
    expect(Filament::getCurrentPanel())->toBeNull()
        ->and(Filament::getTenant())->toBeNull()
        ->and(auth()->id())->toBe($previousUser?->getKey())
        ->and($guard->id())->toBe($previousUser?->getKey())
        ->and($panelGuard->user())->toBeNull();
})->with(['processing failure' => false, 'auth restoration failure' => true]);

class TenantUserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [ImportColumn::make('name'), ImportColumn::make('email')];
    }

    public function resolveRecord(): ?Model
    {
        return isset($this->data['id']) ? parent::resolveRecord() : new User(['password' => 'unused']);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return '';
    }
}

class TenantImportUser extends User
{
    protected $table = 'users';

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user', 'user_id');
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams()->whereKey($tenant)->exists();
    }
}
