<?php

namespace Filament\Tests\Panels\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Tests\Models\Team;
use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Livewire\Drawer\Utils;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleComponents\Checksum;

uses(TestCase::class);

it('clears stale context before restoring the next component context', function (): void {
    $replacementTenant = Team::factory()->create();

    BundledPanelContextComponent::$capturedContexts = [];
    Livewire::component('bundled-panel-context', BundledPanelContextComponent::class);

    Route::get('/bundled-panel-context/first', fn (): string => Blade::render('<livewire:bundled-panel-context />'))
        ->middleware('panel:admin');

    Route::get('/bundled-panel-context/second', fn (): string => Blade::render('<livewire:bundled-panel-context />'))
        ->middleware('panel:admin');

    $firstSnapshot = getPanelContextSnapshot($this, '/bundled-panel-context/first');
    $secondSnapshot = getPanelContextSnapshot($this, '/bundled-panel-context/second');

    app()->forgetScopedInstances();
    Filament::clearResolvedInstance('filament');

    $this
        ->withHeaders(['X-Livewire' => true])
        ->postJson(app('livewire')->getUpdateUri(), [
            'components' => [
                [
                    'snapshot' => json_encode($firstSnapshot, JSON_THROW_ON_ERROR),
                    'updates' => [],
                    'calls' => [[
                        'method' => 'replaceContext',
                        'params' => [$replacementTenant->getKey()],
                        'path' => '',
                    ]],
                ],
                [
                    'snapshot' => json_encode($secondSnapshot, JSON_THROW_ON_ERROR),
                    'updates' => [],
                    'calls' => [[
                        'method' => 'captureContext',
                        'params' => [],
                        'path' => '',
                    ]],
                ],
            ],
        ])
        ->assertSuccessful();

    expect(BundledPanelContextComponent::$capturedContexts)->toBe([[
        'panel' => 'admin',
        'tenant' => null,
        'domain' => 'request.example.test',
    ]]);
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
    /** @var array<array{panel: string, tenant: int | string | null, domain: string}> */
    public static array $capturedContexts = [];

    public function captureContext(): void
    {
        static::$capturedContexts[] = [
            'panel' => Filament::getCurrentPanel()->getId(),
            'tenant' => Filament::getTenant()?->getKey(),
            'domain' => Filament::getCurrentDomain(testingDomain: 'request.example.test'),
        ];
    }

    public function replaceContext(int | string $tenantKey): void
    {
        Filament::setTenant(Team::query()->findOrFail($tenantKey), isQuiet: true);
        Filament::currentDomain('replaced.example.test');
    }

    public function render(): string
    {
        return '<div></div>';
    }
}
