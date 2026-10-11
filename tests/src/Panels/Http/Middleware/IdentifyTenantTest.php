<?php

namespace Filament\Tests\Panels\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Http\Middleware\IdentifyTenant;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Livewire\Drawer\Utils;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleComponents\Checksum;

uses(TestCase::class);

beforeEach(function (): void {
    Route::get('/identify-tenant/{tenant}', fn (): string => (string) Filament::getTenant()->getKey())
        ->middleware([
            'panel:tenant-menu-grouping',
            IdentifyTenant::class,
        ]);

    Route::get('/identify-tenant-without-tenant-parameter', fn (): string => 'continued')
        ->middleware([
            'panel:tenant-menu-grouping',
            IdentifyTenant::class,
        ]);

    Route::get('/identify-tenant-without-tenancy', fn (): string => 'continued')
        ->middleware([
            'panel:admin',
            IdentifyTenant::class,
        ]);
});

it('continues without identifying a tenant when the panel does not have tenancy', function (): void {
    $this
        ->get('/identify-tenant-without-tenancy')
        ->assertSuccessful()
        ->assertSeeText('continued');
});

it('continues without identifying a tenant when the route does not have a `tenant` parameter', function (): void {
    $this
        ->get('/identify-tenant-without-tenant-parameter')
        ->assertSuccessful()
        ->assertSeeText('continued');
});

it('aborts when the authenticated user does not implement `HasTenants`', function (): void {
    $user = UserWithoutTenants::query()->create([
        'email' => 'user-without-tenants@example.com',
        'name' => 'User without tenants',
        'password' => 'password',
    ]);

    $tenant = Team::factory()->create();

    $this
        ->actingAs($user)
        ->get("/identify-tenant/{$tenant->getRouteKey()}")
        ->assertNotFound();
});

it('aborts when the authenticated user cannot access the tenant', function (): void {
    $user = InaccessibleTenantUser::query()->create([
        'email' => 'user-without-tenant-access@example.com',
        'name' => 'User without tenant access',
        'password' => 'password',
    ]);

    $tenant = Team::factory()->create();

    $this
        ->actingAs($user)
        ->get("/identify-tenant/{$tenant->getRouteKey()}")
        ->assertNotFound();
});

it('identifies an accessible tenant before continuing', function (): void {
    $tenant = Team::factory()->create();

    $this
        ->get("/identify-tenant/{$tenant->getRouteKey()}")
        ->assertSuccessful()
        ->assertSeeText((string) $tenant->getKey());
});

it('restores the tenant context for each component in a bundled Livewire request', function (): void {
    $tenantA = Team::factory()->create();
    $tenantB = Team::factory()->create();

    BundledTenantContextComponent::$capturedTenantKeys = [];
    Livewire::component('bundled-tenant-context', BundledTenantContextComponent::class);

    Route::get('/bundled-tenant-context/{tenant}', fn (): string => Blade::render('@livewire($component)', [
        'component' => 'bundled-tenant-context',
    ]))->middleware([
        'panel:tenant-menu-grouping',
        IdentifyTenant::class,
    ]);

    $getSnapshot = function (Team $tenant): array {
        $response = $this
            ->get("/bundled-tenant-context/{$tenant->getRouteKey()}")
            ->assertSuccessful();

        $snapshot = Utils::extractAttributeDataFromHtml($response->getContent(), 'wire:snapshot');

        Checksum::verify($snapshot);

        return $snapshot;
    };

    $tenantASnapshot = $getSnapshot($tenantA);
    $tenantBSnapshot = $getSnapshot($tenantB);
    $finalTenantASnapshot = $getSnapshot($tenantA);

    $this
        ->withHeaders(['X-Livewire' => true])
        ->postJson(app('livewire')->getUpdateUri(), [
            'components' => [
                [
                    'snapshot' => json_encode($tenantASnapshot, JSON_THROW_ON_ERROR),
                    'updates' => [],
                    'calls' => [
                        ['method' => 'captureTenant', 'params' => [], 'path' => ''],
                    ],
                ],
                [
                    'snapshot' => json_encode($tenantBSnapshot, JSON_THROW_ON_ERROR),
                    'updates' => [],
                    'calls' => [
                        ['method' => 'captureTenant', 'params' => [], 'path' => ''],
                    ],
                ],
                [
                    'snapshot' => json_encode($finalTenantASnapshot, JSON_THROW_ON_ERROR),
                    'updates' => [],
                    'calls' => [
                        ['method' => 'captureTenant', 'params' => [], 'path' => ''],
                    ],
                ],
            ],
        ])
        ->assertSuccessful();

    expect(BundledTenantContextComponent::$capturedTenantKeys)->toBe([
        $tenantA->getKey(),
        $tenantB->getKey(),
        $tenantA->getKey(),
    ]);
})->skip('Waiting for the Livewire persistent middleware fix: https://github.com/livewire/livewire/pull/10790');

class BundledTenantContextComponent extends Component
{
    /** @var array<int | string> */
    public static array $capturedTenantKeys = [];

    public function captureTenant(): void
    {
        static::$capturedTenantKeys[] = Filament::getTenant()->getKey();
    }

    public function render(): string
    {
        return '<div></div>';
    }
}

class UserWithoutTenants extends Authenticatable
{
    protected $table = 'users';

    protected $guarded = [];
}

class InaccessibleTenantUser extends User
{
    protected $table = 'users';

    public function canAccessTenant(Model $tenant): bool
    {
        return false;
    }
}
