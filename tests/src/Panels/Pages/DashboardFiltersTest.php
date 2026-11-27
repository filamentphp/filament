<?php

use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Http\Request;
use Livewire\Livewire;

use function Filament\Tests\livewire;

uses(TestCase::class);

dataset('dashboard filter components', [
    'form' => [DashboardFiltersFormTestComponent::class],
    'action' => [DashboardFiltersActionTestComponent::class],
]);
dataset('malformed dashboard filters', [
    'nested value' => [['nested' => ['term' => "bad\xFF"]]],
    'nested key' => [['nested' => ["bad\xFF" => 'valid']]],
]);

it('rejects malformed UTF-8 dashboard filters from the URL', function (string $componentClass, array $filters): void {
    $sessionKey = livewire($componentClass)->instance()->getFiltersSessionKey();
    $emptyState = $componentClass === DashboardFiltersFormTestComponent::class ? ['search' => null] : null;
    $request = Request::create('/?' . http_build_query(['filters' => $filters]));

    Livewire::withQueryParams($request->query())
        ->test($componentClass)
        ->assertSet('filters', $emptyState)
        ->call('$refresh')
        ->assertSuccessful();

    expect(session()->get($sessionKey))->toBe($emptyState);

    Livewire::withQueryParams([])->test($componentClass)
        ->assertSet('filters', $emptyState)
        ->call('$refresh')
        ->assertSuccessful();
})->with('dashboard filter components')->with('malformed dashboard filters');

it('recovers malformed UTF-8 persisted dashboard filters on a clean URL', function (string $componentClass, array $filters): void {
    $sessionKey = livewire($componentClass)->instance()->getFiltersSessionKey();
    $emptyState = $componentClass === DashboardFiltersFormTestComponent::class ? ['search' => null] : null;
    session()->put($sessionKey, $filters);
    session()->save();
    session()->flush();
    session()->start();

    expect(session()->get($sessionKey))->toBe($filters);

    livewire($componentClass)
        ->assertSet('filters', $emptyState)
        ->call('$refresh')
        ->assertSuccessful();

    expect(session()->get($sessionKey))->toBe($emptyState);

    livewire($componentClass)
        ->assertSet('filters', $emptyState)
        ->call('$refresh')
        ->assertSuccessful();
})->with('dashboard filter components')->with('malformed dashboard filters');

it('rejects malformed UTF-8 dashboard filters from Livewire requests', function (string $componentClass, array $filters): void {
    $component = livewire($componentClass);
    $sessionKey = $component->instance()->getFiltersSessionKey();
    $payload = [
        'components' => [[
            'snapshot' => json_encode($component->snapshot, JSON_THROW_ON_ERROR),
            'updates' => ['filters' => $filters],
            'calls' => [['method' => 'setSearch', 'params' => ['healthy']]],
        ]],
    ];

    $response = $this->postJson(Livewire::getUpdateUri() . '?' . http_build_query($payload), [], ['X-Livewire' => 'true']);

    expect(session()->get($sessionKey))->toBeNull();

    $response->assertSuccessful();
    $snapshot = json_decode($response->json('components.0.snapshot'), true, flags: JSON_THROW_ON_ERROR);

    expect($snapshot['data']['filters'][0])->toBe(['search' => 'healthy']);

    Livewire::withQueryParams([])->test($componentClass)
        ->call('$refresh')
        ->assertSuccessful();
})->with('dashboard filter components')->with('malformed dashboard filters');

it('preserves Unicode, nested dashboard filter state, URL precedence, and null resets', function (string $componentClass): void {
    $filters = ['search' => "  café e\u{0301} 中文 العربية 👩🏽‍💻 \u{FFFD} %FF +  ", 'nested' => ['中文' => [null, true, false, 42]]];
    $component = livewire($componentClass)->set('filters', $filters)->assertSuccessful();
    $sessionKey = $component->instance()->getFiltersSessionKey();

    expect(session()->get($sessionKey))->toBe($filters);

    livewire($componentClass)->assertSet('filters', $filters, strict: true)
        ->call('$refresh')->assertSuccessful();

    session()->put($sessionKey, ['stale' => "bad\xFF"]);

    Livewire::withQueryParams(['filters' => $filters])->test($componentClass)
        ->assertSet('filters', $filters, strict: true)
        ->call('$refresh')->assertSuccessful();

    expect(session()->get($sessionKey))->toBe($filters);

    Livewire::withQueryParams(['filters' => []])->test($componentClass)
        ->assertSet('filters', $filters, strict: true)
        ->set('filters', null)->assertSuccessful();

    expect(session()->get($sessionKey))->toBeNull();
})->with('dashboard filter components');

it('does not consume or change dashboard filters when persistence is disabled', function (string $componentClass): void {
    $componentClass = $componentClass === DashboardFiltersFormTestComponent::class ? NonPersistedDashboardFiltersFormTestComponent::class : NonPersistedDashboardFiltersActionTestComponent::class;
    $sessionKey = livewire($componentClass)->instance()->getFiltersSessionKey();
    $staleFilters = ['nested' => ["bad\xFF" => 'valid']];
    session()->put($sessionKey, $staleFilters);
    $filters = ['search' => '中文 café', 'nested' => ['term' => '👩🏽‍💻']];

    livewire($componentClass)
        ->set('filters', $filters)
        ->call('$refresh')->assertSuccessful();

    expect(session()->get($sessionKey))->toBe($staleFilters);

    Livewire::withQueryParams(['filters' => $filters])->test($componentClass)
        ->assertSet('filters', $filters, strict: true)
        ->call('$refresh')->assertSuccessful();

    expect(session()->get($sessionKey))->toBe($staleFilters);
})->with('dashboard filter components');

it('preserves supported date objects in persisted dashboard filters', function (string $componentClass): void {
    $sessionKey = livewire($componentClass)->instance()->getFiltersSessionKey();
    $date = new DateTimeImmutable('2026-11-27T12:34:56+00:00');
    session()->put($sessionKey, ['nested' => ['date' => $date]]);

    livewire($componentClass)->assertSet('filters.nested.date', $date)
        ->call('$refresh')->assertSuccessful();

    expect(session()->get($sessionKey)['nested']['date'])->toBe($date);
})->with('dashboard filter components');

it('keeps persisted dashboard filters isolated by tenant', function (): void {
    $firstTenant = Team::factory()->create();
    $secondTenant = Team::factory()->create();

    Filament::setTenant($firstTenant, isQuiet: true);
    $firstComponent = livewire(DashboardFiltersActionTestComponent::class)->set('filters', ['search' => '中文']);
    $firstSessionKey = $firstComponent->instance()->getFiltersSessionKey();

    Filament::setTenant($secondTenant, isQuiet: true);
    $secondComponent = livewire(DashboardFiltersActionTestComponent::class)->assertSet('filters', null);

    expect($secondComponent->instance()->getFiltersSessionKey())->not->toBe($firstSessionKey);

    Filament::setTenant($firstTenant, isQuiet: true);
    livewire(DashboardFiltersActionTestComponent::class)->assertSet('filters', ['search' => '中文']);
    Filament::setTenant(null);
});

class DashboardFiltersTestComponent extends Dashboard
{
    #[Exposed]
    public function setSearch(string $search): void
    {
        $this->filters['search'] = $search;
    }
}

class DashboardFiltersFormTestComponent extends DashboardFiltersTestComponent
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('search')]);
    }
}

class DashboardFiltersActionTestComponent extends DashboardFiltersTestComponent
{
    use HasFiltersAction;
}

class NonPersistedDashboardFiltersFormTestComponent extends DashboardFiltersFormTestComponent
{
    protected bool $persistsFiltersInSession = false;
}

class NonPersistedDashboardFiltersActionTestComponent extends DashboardFiltersActionTestComponent
{
    protected bool $persistsFiltersInSession = false;
}
