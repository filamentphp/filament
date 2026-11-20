<?php

namespace Filament\Tests\Panels\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Http\Middleware\IdentifyTenant;
use Filament\Panel;
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

it('boots each panel once in a bundled Livewire request', function (): void {
    BundledPanelContextComponent::$bootedPanelIds = [];
    BundledPanelContextComponent::$handledPanelIds = [];
    Livewire::component('bundled-panel-context', BundledPanelContextComponent::class);

    Route::get('/bundled-panel-context/admin', fn (): string => Blade::render('@livewire($component)', [
        'component' => 'bundled-panel-context',
    ]))->middleware('panel:admin');

    Route::get('/bundled-panel-context/tenancy', fn (): string => Blade::render('@livewire($component)', [
        'component' => 'bundled-panel-context',
    ]))->middleware('panel:tenant-menu-grouping');

    foreach (['admin', 'tenant-menu-grouping'] as $panelId) {
        Filament::getPanel($panelId)->bootUsing(static function (Panel $panel): void {
            BundledPanelContextComponent::$bootedPanelIds[] = $panel->getId();
        });
    }

    $adminSnapshot = getPanelContextSnapshot($this, '/bundled-panel-context/admin');
    $tenancySnapshot = getPanelContextSnapshot($this, '/bundled-panel-context/tenancy');
    $finalAdminSnapshot = getPanelContextSnapshot($this, '/bundled-panel-context/admin');

    BundledPanelContextComponent::$bootedPanelIds = [];
    app()->forgetScopedInstances();
    Filament::clearResolvedInstance('filament');

    postPanelContextSnapshots($this, [
        $adminSnapshot,
        $tenancySnapshot,
        $finalAdminSnapshot,
    ]);

    expect(BundledPanelContextComponent::$bootedPanelIds)->toBe([
        'admin',
        'tenant-menu-grouping',
    ])->and(BundledPanelContextComponent::$handledPanelIds)->toBe([
        'admin',
        'tenant-menu-grouping',
        'admin',
    ]);
});

it('clears stale context before restoring the next component context', function (): void {
    $tenant = Team::factory()->create();

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

    Route::get('/bundled-panel-context/plain', fn (): string => Blade::render('@livewire($component)', [
        'component' => 'bundled-panel-context',
    ]))->middleware('panel:admin');

    $configuredSnapshot = getPanelContextSnapshot($this, "/bundled-panel-context/configured/{$tenant->getRouteKey()}");
    $plainSnapshot = getPanelContextSnapshot($this, '/bundled-panel-context/plain');

    app()->forgetScopedInstances();
    Filament::clearResolvedInstance('filament');

    postPanelContextSnapshots($this, [
        $configuredSnapshot,
        $plainSnapshot,
    ], method: 'captureContext');

    expect(BundledPanelContextComponent::$capturedContexts)->toBe([
        [
            'panel' => 'tenant-menu-grouping',
            'tenant' => $tenant->getKey(),
            'resourceConfiguration' => 'bundled-resource',
            'pageConfiguration' => 'bundled-page',
        ],
        [
            'panel' => 'admin',
            'tenant' => null,
            'resourceConfiguration' => null,
            'pageConfiguration' => null,
        ],
    ]);
});

it('enforces tenant scope when another panel is first in a bundled Livewire request', function (): void {
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

    User::clearBootedModels();
    invade(Filament::getPanel('tenancy'))->isTenancyBooted = false;
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
        ->assertSuccessful();

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

/** @param array<array<string, mixed>> $snapshots */
function postPanelContextSnapshots(TestCase $testCase, array $snapshots, string $method = 'capturePanel'): void
{
    $testCase
        ->withHeaders(['X-Livewire' => true])
        ->postJson(app('livewire')->getUpdateUri(), [
            'components' => array_map(static fn (array $snapshot): array => [
                'snapshot' => json_encode($snapshot, JSON_THROW_ON_ERROR),
                'updates' => [],
                'calls' => [
                    ['method' => $method, 'params' => [], 'path' => ''],
                ],
            ], $snapshots),
        ])
        ->assertSuccessful();
}

class BundledPanelContextComponent extends Component
{
    /** @var array<string> */
    public static array $bootedPanelIds = [];

    /** @var array<string> */
    public static array $handledPanelIds = [];

    /** @var array<array{panel: string, tenant: int | string | null, resourceConfiguration: ?string, pageConfiguration: ?string}> */
    public static array $capturedContexts = [];

    public function capturePanel(): void
    {
        static::$handledPanelIds[] = Filament::getCurrentPanel()->getId();
    }

    public function captureContext(): void
    {
        static::$capturedContexts[] = [
            'panel' => Filament::getCurrentPanel()->getId(),
            'tenant' => Filament::getTenant()?->getKey(),
            'resourceConfiguration' => Filament::getCurrentResourceConfigurationKey(),
            'pageConfiguration' => Filament::getCurrentPageConfigurationKey(),
        ];
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
