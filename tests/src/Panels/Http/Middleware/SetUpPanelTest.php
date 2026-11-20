<?php

namespace Filament\Tests\Panels\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Http\Middleware\IdentifyTenant;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Livewire\Drawer\Utils;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleComponents\Checksum;

uses(TestCase::class);

it('clears stale context before restoring the next component context', function (): void {
    $tenant = Team::factory()->create();
    $replacementTenant = Team::factory()->create();

    BundledPanelContextComponent::$capturedContexts = [];
    Livewire::component('bundled-panel-context', BundledPanelContextComponent::class);

    Route::get('/bundled-panel-context/configured/{tenant}', fn (): string => Blade::render('@livewire($component)', [
        'component' => 'bundled-panel-context',
    ]))->middleware([
        'panel:tenant-menu-grouping',
        IdentifyTenant::class,
        'resource-configuration:bundled-resource',
        'page-configuration:bundled-page',
    ]);

    Route::get('/bundled-panel-context/plain/{tenant}', fn (): string => Blade::render('@livewire($component)', [
        'component' => 'bundled-panel-context',
    ]))->middleware([
        'panel:tenant-menu-grouping',
        IdentifyTenant::class,
    ]);

    $configuredSnapshot = getPanelContextSnapshot($this, "/bundled-panel-context/configured/{$tenant->getRouteKey()}");
    $plainSnapshot = getPanelContextSnapshot($this, "/bundled-panel-context/plain/{$tenant->getRouteKey()}");

    app()->forgetScopedInstances();
    Filament::clearResolvedInstance('filament');

    $this
        ->withHeaders(['X-Livewire' => true])
        ->postJson(app('livewire')->getUpdateUri(), [
            'components' => [
                [
                    'snapshot' => json_encode($configuredSnapshot, JSON_THROW_ON_ERROR),
                    'updates' => [],
                    'calls' => [
                        [
                            'method' => 'replaceContext',
                            'params' => [$replacementTenant->getKey()],
                            'path' => '',
                        ],
                    ],
                ],
                [
                    'snapshot' => json_encode($plainSnapshot, JSON_THROW_ON_ERROR),
                    'updates' => [],
                    'calls' => [
                        ['method' => 'captureContext', 'params' => [], 'path' => ''],
                    ],
                ],
            ],
        ])
        ->assertSuccessful();

    expect(BundledPanelContextComponent::$capturedContexts)->toBe([
        [
            'panel' => 'tenant-menu-grouping',
            'tenant' => $tenant->getKey(),
            'domain' => 'request.example.test',
            'resourceConfiguration' => 'bundled-resource',
            'pageConfiguration' => 'bundled-page',
        ],
        [
            'panel' => 'tenant-menu-grouping',
            'tenant' => $tenant->getKey(),
            'domain' => 'request.example.test',
            'resourceConfiguration' => null,
            'pageConfiguration' => null,
        ],
    ]);
});

it('rejects an update from another panel in a bundled Livewire request', function (): void {
    $accessibleTenant = Team::factory()->create();
    $inaccessibleTenant = Team::factory()->create();
    $user = BundledPanelTenantUser::query()->create([
        'email' => 'bundled-panel-tenant-user@example.com',
        'name' => 'Bundled panel tenant user',
        'password' => 'password',
    ]);
    $record = User::factory()->create(['name' => 'Original name']);

    $accessibleTenant->users()->attach($user);
    $inaccessibleTenant->users()->attach($record);
    BundledPanelTenantUser::$accessibleTenantKey = $accessibleTenant->getKey();
    $this->actingAs($user);

    Livewire::component('bundled-panel-context', BundledPanelContextComponent::class);

    Route::get('/bundled-panel-context/admin', fn (): string => Blade::render('@livewire($component)', [
        'component' => 'bundled-panel-context',
    ]))->middleware('panel:admin');

    Route::get('/bundled-panel-context/tenancy/{tenant}', fn (): string => Blade::render('@livewire($component)', [
        'component' => 'bundled-panel-context',
    ]))->middleware([
        'panel:tenancy',
        IdentifyTenant::class,
    ]);

    $adminSnapshot = getPanelContextSnapshot($this, '/bundled-panel-context/admin');
    $tenancySnapshot = getPanelContextSnapshot($this, "/bundled-panel-context/tenancy/{$accessibleTenant->getRouteKey()}");

    $this
        ->get("/bundled-panel-context/tenancy/{$inaccessibleTenant->getRouteKey()}")
        ->assertNotFound();

    app()->forgetScopedInstances();
    Filament::clearResolvedInstance('filament');

    $this
        ->withHeaders(['X-Livewire' => true])
        ->postJson(app('livewire')->getUpdateUri(), [
            'components' => [
                [
                    'snapshot' => json_encode($adminSnapshot, JSON_THROW_ON_ERROR),
                    'updates' => [],
                    'calls' => [],
                ],
                [
                    'snapshot' => json_encode($tenancySnapshot, JSON_THROW_ON_ERROR),
                    'updates' => [],
                    'calls' => [
                        [
                            'method' => 'renameRecord',
                            'params' => [$record->getKey(), 'Modified name'],
                            'path' => '',
                        ],
                    ],
                ],
            ],
        ])
        ->assertNotFound();

    expect(User::withoutGlobalScopes()->findOrFail($record->getKey())->name)->toBe('Original name');
});

function getPanelContextSnapshot(TestCase $testCase, string $path): array
{
    $response = $testCase
        ->get($path)
        ->assertSuccessful();

    $snapshot = Utils::extractAttributeDataFromHtml($response->getContent(), 'wire:snapshot');

    Checksum::verify($snapshot);

    return $snapshot;
}

class BundledPanelContextComponent extends Component
{
    /** @var array<array{panel: string, tenant: int | string | null, domain: string, resourceConfiguration: ?string, pageConfiguration: ?string}> */
    public static array $capturedContexts = [];

    public function captureContext(): void
    {
        static::$capturedContexts[] = [
            'panel' => Filament::getCurrentPanel()->getId(),
            'tenant' => Filament::getTenant()?->getKey(),
            'domain' => Filament::getCurrentDomain(testingDomain: 'request.example.test'),
            'resourceConfiguration' => Filament::getCurrentResourceConfigurationKey(),
            'pageConfiguration' => Filament::getCurrentPageConfigurationKey(),
        ];
    }

    public function replaceContext(int | string $tenantKey): void
    {
        $this->captureContext();

        Filament::setTenant(Team::query()->findOrFail($tenantKey), isQuiet: true);
        Filament::setCurrentDomain('replaced.example.test');
        Filament::setCurrentResourceConfigurationKey('replaced-resource');
        Filament::setCurrentPageConfigurationKey('replaced-page');
    }

    public function renameRecord(int $recordId, string $name): void
    {
        User::find($recordId)?->update(['name' => $name]);
    }

    public function render(): string
    {
        return '<div></div>';
    }
}

class BundledPanelTenantUser extends User
{
    protected $table = 'users';

    public static int | string | null $accessibleTenantKey = null;

    public function canAccessTenant(Model $tenant): bool
    {
        return $tenant->getKey() === static::$accessibleTenantKey;
    }
}
