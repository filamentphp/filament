<?php

use Filament\Support\Attributes\Exposed;
use Filament\Support\Attributes\ServerOnly;
use Filament\Tests\TestCase;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Exceptions\EventHandlerDoesNotExist;
use Livewire\Exceptions\MethodNotFoundException;
use Livewire\Livewire;

uses(TestCase::class);

beforeEach(function (): void {
    ServerOnlyTestComponent::$executionCount = 0;
});

it('allows direct PHP calls to a `#[ServerOnly]` method', function (): void {
    $component = new ServerOnlyTestComponent;

    expect($component->serverOnly(3))->toBe(3)
        ->and(ServerOnlyTestComponent::$executionCount)->toBe(3);
});

it('allows server-side calls to a `#[ServerOnly]` method from a Livewire action or listener', function (): void {
    Livewire::test(ServerOnlyTestComponent::class)
        ->call('callServerOnly', 3)
        ->assertReturned(3)
        ->dispatch('allowed', amount: 5);

    expect(ServerOnlyTestComponent::$executionCount)->toBe(8);
});

it('rejects client calls to a `#[ServerOnly]` method before executing it', function (string $method, array $parameters): void {
    $component = Livewire::test(ServerOnlyTestComponent::class);

    expect(fn () => $component->call($method, ...$parameters))
        ->toThrow(MethodNotFoundException::class);

    expect(ServerOnlyTestComponent::$executionCount)->toBe(0);
})->with([
    'method call' => ['serverOnly', [3]],
    'attribute listener' => ['__dispatch', ['server-only', ['amount' => 3]]],
    'aliased listener' => ['__dispatch', ['alias', ['amount' => 3]]],
    'dynamic listener' => ['__dispatch', ['server-only.7', ['amount' => 3]]],
]);

it('preserves Livewire rejection of unregistered events', function (): void {
    $component = Livewire::test(ServerOnlyTestComponent::class);

    expect(fn () => $component->dispatch('unregistered', amount: 3))
        ->toThrow(EventHandlerDoesNotExist::class);

    expect(ServerOnlyTestComponent::$executionCount)->toBe(0);
});

it('allows a child to expose an inherited `#[ServerOnly]` method', function (string $componentClass): void {
    Livewire::test($componentClass)
        ->call('serverOnly', 3)
        ->assertReturned(3)
        ->dispatch('alias', amount: 5);

    expect(ServerOnlyTestComponent::$executionCount)->toBe(8);
})->with([
    'explicitly exposed override' => [ExposedServerOnlyTestComponent::class],
    'inherited exposed override' => [InheritedExposedTestComponent::class],
]);

it('inherits method access through unannotated overrides and lets `#[ServerOnly]` win over `#[Exposed]`', function (string $componentClass): void {
    $component = Livewire::test($componentClass);

    expect(fn () => $component->call('serverOnly', 3))
        ->toThrow(MethodNotFoundException::class);

    $component = Livewire::test($componentClass);

    expect(fn () => $component->dispatch('alias', amount: 5))
        ->toThrow(MethodNotFoundException::class);

    expect(ServerOnlyTestComponent::$executionCount)->toBe(0);
})->with([
    'unannotated grandchild override' => [InheritedServerOnlyTestComponent::class],
    'explicitly blocked exposed parent' => [BlockedExposedTestComponent::class],
    'conflicting attributes on child' => [ConflictingMethodAccessTestComponent::class],
]);

trait HasServerOnlyTestMethod
{
    #[ServerOnly]
    #[On('server-only')]
    public function serverOnly(int $amount): int
    {
        return static::$executionCount += $amount;
    }
}

class ServerOnlyTestBaseComponent extends Component
{
    use HasServerOnlyTestMethod;

    public static int $executionCount = 0;

    public int $identifier = 7;

    protected $listeners = [
        'alias' => 'serverOnly',
        'server-only.{identifier}' => 'serverOnly',
        'allowed' => 'callServerOnly',
    ];

    public function callServerOnly(int $amount): int
    {
        return $this->serverOnly($amount);
    }

    public function render(): string
    {
        return '<div></div>';
    }
}

class ServerOnlyTestComponent extends ServerOnlyTestBaseComponent
{
    #[On('server-only')]
    public function serverOnly(int $amount): int
    {
        return parent::serverOnly($amount);
    }
}

class InheritedServerOnlyTestComponent extends ServerOnlyTestComponent
{
    public function serverOnly(int $amount): int
    {
        return parent::serverOnly($amount);
    }
}

class ExposedServerOnlyTestComponent extends InheritedServerOnlyTestComponent
{
    #[Exposed]
    public function serverOnly(int $amount): int
    {
        return parent::serverOnly($amount);
    }
}

class InheritedExposedTestComponent extends ExposedServerOnlyTestComponent
{
    public function serverOnly(int $amount): int
    {
        return parent::serverOnly($amount);
    }
}

class BlockedExposedTestComponent extends ExposedServerOnlyTestComponent
{
    #[ServerOnly]
    public function serverOnly(int $amount): int
    {
        return parent::serverOnly($amount);
    }
}

class ConflictingMethodAccessTestComponent extends ExposedServerOnlyTestComponent
{
    #[Exposed]
    #[ServerOnly]
    public function serverOnly(int $amount): int
    {
        return parent::serverOnly($amount);
    }
}
