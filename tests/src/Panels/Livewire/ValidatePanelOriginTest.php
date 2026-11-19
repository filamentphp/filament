<?php

namespace Filament\Tests\Panels\Livewire;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\IdentifyTenant;
use Filament\Panel;
use Filament\Tests\Fixtures\Models\DomainTeam;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;
use Livewire\Component;
use Livewire\Drawer\Utils;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleComponents\Checksum;

uses(TestCase::class);

beforeEach(function (): void {
    Livewire::component('panel-route-probe', PanelRouteProbe::class);

    Route::domain('app.example.test')
        ->middleware([
            'panel:tenant-menu-grouping',
            IdentifyTenant::class,
        ])
        ->get('/secure/{tenant}', fn (): string => Blade::render('<livewire:panel-route-probe />'));

    PanelRouteProbe::$calls = 0;
    PanelRouteProbe::$tenantKeys = [];
});

function panelRouteSnapshot(TestCase $test, Team $tenant): array
{
    $response = $test
        ->get("https://app.example.test/secure/{$tenant->getRouteKey()}")
        ->assertSuccessful();

    return Utils::extractAttributeDataFromHtml($response->getContent(), 'wire:snapshot');
}

function updatePanelRoute(TestCase $test, array $snapshot, string $host, ?array $calls = null): TestResponse
{
    return $test
        ->withHeaders(['X-Livewire' => true])
        ->postJson("https://{$host}" . app('livewire')->getUpdateUri(), [
            'components' => [[
                'snapshot' => json_encode($snapshot, JSON_THROW_ON_ERROR),
                'updates' => [],
                'calls' => $calls ?? [['method' => 'record', 'params' => [], 'path' => '']],
            ]],
        ]);
}

it('rejects replay of a panel snapshot on an unmatched host', function (): void {
    $snapshot = panelRouteSnapshot($this, Team::factory()->create());

    updatePanelRoute($this, $snapshot, 'other.example.test')->assertNotFound();

    expect(PanelRouteProbe::$calls)->toBe(0);
});

it('allows the original panel route and restores its tenant on an update', function (): void {
    $tenant = Team::factory()->create();
    $snapshot = panelRouteSnapshot($this, $tenant);

    $response = updatePanelRoute($this, $snapshot, 'app.example.test')->assertSuccessful();

    expect(PanelRouteProbe::$calls)->toBe(1)
        ->and(PanelRouteProbe::$tenantKeys)->toBe([$tenant->getKey()]);

    $updatedSnapshot = json_decode($response->json('components.0.snapshot'), true, flags: JSON_THROW_ON_ERROR);

    expect($updatedSnapshot['memo']['filamentOrigin'])->toBe($snapshot['memo']['filamentOrigin']);
});

it('rejects replay when an unprotected route matches on another host', function (): void {
    Route::domain('other.example.test')
        ->get('/secure/{tenant}', fn (): string => 'Unprotected route');

    $snapshot = panelRouteSnapshot($this, Team::factory()->create());

    updatePanelRoute($this, $snapshot, 'other.example.test')->assertNotFound();

    expect(PanelRouteProbe::$calls)->toBe(0);
});

it('rejects replay on another host even when the same panel route pattern matches', function (): void {
    Route::domain('{subdomain}.example.test')
        ->middleware([
            'panel:tenant-menu-grouping',
            IdentifyTenant::class,
        ])
        ->get('/shared/{tenant}', fn (): string => Blade::render('<livewire:panel-route-probe />'));

    $tenant = Team::factory()->create();

    $response = $this
        ->get("https://one.example.test/shared/{$tenant->getRouteKey()}")
        ->assertSuccessful();

    $snapshot = Utils::extractAttributeDataFromHtml($response->getContent(), 'wire:snapshot');

    updatePanelRoute($this, $snapshot, 'two.example.test')->assertNotFound();

    expect(PanelRouteProbe::$calls)->toBe(0);
});

it('rejects replay when tenant access is revoked', function (): void {
    $user = new RevocableTenantUser;
    $user->email = 'revocable@example.com';
    $user->name = 'Revocable';
    $user->password = 'password';
    $user->save();

    $this->actingAs($user);

    RevocableTenantUser::$canAccessTenant = true;

    $snapshot = panelRouteSnapshot($this, Team::factory()->create());

    RevocableTenantUser::$canAccessTenant = false;

    updatePanelRoute($this, $snapshot, 'app.example.test')->assertNotFound();

    expect(PanelRouteProbe::$calls)->toBe(0);
});

it('rechecks panel authorization before an update action', function (): void {
    Route::domain('admin.example.test')
        ->middleware([
            'panel:admin',
            Authenticate::class,
        ])
        ->get('/protected', fn (): string => Blade::render('<livewire:panel-route-probe />'));

    $user = new RevocablePanelUser;
    $user->email = 'revocable-panel@example.com';
    $user->name = 'Revocable panel';
    $user->password = 'password';
    $user->save();

    $this->actingAs($user);

    RevocablePanelUser::$canAccessPanel = true;

    $response = $this
        ->get('https://admin.example.test/protected')
        ->assertSuccessful();

    $snapshot = Utils::extractAttributeDataFromHtml($response->getContent(), 'wire:snapshot');

    RevocablePanelUser::$canAccessPanel = false;

    updatePanelRoute($this, $snapshot, 'admin.example.test')->assertForbidden();

    expect(PanelRouteProbe::$calls)->toBe(0);
});

it('binds the registration page of a domain-bound panel to its original host', function (): void {
    auth()->logout();

    $response = $this
        ->get('https://example3.com/register')
        ->assertSuccessful();

    $snapshot = Utils::extractAttributeDataFromHtml($response->getContent(), 'wire:snapshot');

    expect($snapshot['memo']['filamentOrigin'])->toBe('https://example3.com');

    updatePanelRoute($this, $snapshot, 'example3.com', [])->assertSuccessful();
    updatePanelRoute($this, $snapshot, 'other.example.test', [])->assertNotFound();
});

it('binds a `tenantDomain()` page to its tenant host', function (): void {
    $tenant = DomainTeam::factory()->create(['domain' => 'acme.example.test']);

    Filament::getPanel('domain-tenancy')->resolveTenantUsing(
        static fn (string $key): DomainTeam => DomainTeam::query()->where('domain', $key)->firstOrFail(),
    );

    $user = new RevocablePanelUser;
    $user->email = 'domain-tenant@example.com';
    $user->name = 'Domain tenant';
    $user->password = 'password';
    $user->email_verified_at = now();
    $user->save();

    $this->actingAs($user);

    RevocablePanelUser::$canAccessPanel = true;

    $response = $this
        ->get("https://{$tenant->domain}/domain-tenancy")
        ->assertSuccessful();

    $snapshot = Utils::extractAttributeDataFromHtml($response->getContent(), 'wire:snapshot');

    expect($snapshot['memo']['filamentOrigin'])->toBe("https://{$tenant->domain}");

    updatePanelRoute($this, $snapshot, $tenant->domain, [])->assertSuccessful();
    updatePanelRoute($this, $snapshot, 'example.test', [])->assertNotFound();
});

it('leaves path-only panel updates working across hosts', function (): void {
    Route::middleware([
        'panel:tenant-menu-grouping',
        IdentifyTenant::class,
    ])->get('/path-only/{tenant}', fn (): string => Blade::render('<livewire:panel-route-probe />'));

    $tenant = Team::factory()->create();

    $response = $this
        ->get("https://app.example.test/path-only/{$tenant->getRouteKey()}")
        ->assertSuccessful();

    $snapshot = Utils::extractAttributeDataFromHtml($response->getContent(), 'wire:snapshot');

    expect($snapshot['memo']['filamentOrigin'])->toBeFalse();

    updatePanelRoute($this, $snapshot, 'other.example.test')->assertSuccessful();

    expect(PanelRouteProbe::$tenantKeys)->toBe([$tenant->getKey()]);
});

it('rejects a signed snapshot from before the route guard was added', function (): void {
    $snapshot = panelRouteSnapshot($this, Team::factory()->create());

    unset($snapshot['memo']['filamentOrigin'], $snapshot['checksum']);

    $snapshot['checksum'] = Checksum::generate($snapshot);

    updatePanelRoute($this, $snapshot, 'other.example.test')->assertNotFound();

    expect(PanelRouteProbe::$calls)->toBe(0);
});

it('does not restrict non-panel Livewire routes', function (): void {
    Route::get('/public-probe', fn (): string => Blade::render('<livewire:panel-route-probe />'));

    $response = $this
        ->get('https://app.example.test/public-probe')
        ->assertSuccessful();

    $snapshot = Utils::extractAttributeDataFromHtml($response->getContent(), 'wire:snapshot');

    expect($snapshot['memo']['filamentOrigin'])->toBeFalse();

    updatePanelRoute($this, $snapshot, 'other.example.test')->assertSuccessful();

    expect(PanelRouteProbe::$calls)->toBe(1);
});

class PanelRouteProbe extends Component
{
    public static int $calls = 0;

    /** @var array<int | string> */
    public static array $tenantKeys = [];

    public function record(): void
    {
        static::$calls++;
        static::$tenantKeys[] = Filament::getTenant()?->getKey();
    }

    public function render(): string
    {
        return '<div>Panel route probe</div>';
    }
}

class RevocableTenantUser extends User
{
    protected $table = 'users';

    public static bool $canAccessTenant = true;

    public function canAccessTenant(Model $tenant): bool
    {
        return static::$canAccessTenant;
    }
}

class RevocablePanelUser extends User
{
    protected $table = 'users';

    public static bool $canAccessPanel = true;

    public function canAccessPanel(Panel $panel): bool
    {
        return static::$canAccessPanel;
    }
}
