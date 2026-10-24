<?php

use Filament\Support\Attributes\Unexposed;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Tests\TestCase;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Livewire;

uses(TestCase::class);

beforeEach(function (): void {
    UnexposedTestComponent::$executionCount = 0;
});

it('rejects client dispatches to a `#[Unexposed]` listener before executing it', function (string $event): void {
    Livewire::test(UnexposedTestComponent::class)
        ->dispatch($event, amount: 3)
        ->assertForbidden();

    expect(UnexposedTestComponent::$executionCount)->toBe(0);
})->with([
    'attribute listener' => ['unexposed'],
    'dynamic listener' => ['unexposed.7'],
]);

it('allows a child to expose an inherited `#[Unexposed]` method', function (string $componentClass): void {
    Livewire::test($componentClass)
        ->call('unexposed', 3)
        ->assertReturned(3)
        ->dispatch('alias', amount: 5);

    expect(UnexposedTestComponent::$executionCount)->toBe(8);
})->with([
    'explicitly exposed override' => [ExposedUnexposedTestComponent::class],
    'inherited exposed override' => [InheritedExposedTestComponent::class],
]);

it('inherits method access through unannotated overrides and lets `#[Unexposed]` win over `#[Exposed]`', function (string $componentClass): void {
    Livewire::test($componentClass)
        ->call('unexposed', 3)
        ->assertForbidden();

    Livewire::test($componentClass)
        ->dispatch('alias', amount: 5)
        ->assertForbidden();

    expect(UnexposedTestComponent::$executionCount)->toBe(0);
})->with([
    'unannotated grandchild override' => [InheritedUnexposedTestComponent::class],
    'explicitly blocked exposed parent' => [BlockedExposedTestComponent::class],
    'conflicting attributes on child' => [ConflictingMethodAccessTestComponent::class],
]);

trait HasUnexposedTestMethod
{
    #[Unexposed]
    #[On('unexposed')]
    public function unexposed(int $amount): int
    {
        return static::$executionCount += $amount;
    }
}

class UnexposedTestBaseComponent extends Component
{
    use HasUnexposedTestMethod;

    public static int $executionCount = 0;

    public int $identifier = 7;

    protected $listeners = [
        'alias' => 'unexposed',
        'unexposed.{identifier}' => 'unexposed',
    ];

    public function render(): string
    {
        return '<div></div>';
    }
}

class UnexposedTestComponent extends UnexposedTestBaseComponent
{
    #[On('unexposed')]
    public function unexposed(int $amount): int
    {
        return parent::unexposed($amount);
    }
}

class InheritedUnexposedTestComponent extends UnexposedTestComponent
{
    public function unexposed(int $amount): int
    {
        return parent::unexposed($amount);
    }
}

class ExposedUnexposedTestComponent extends InheritedUnexposedTestComponent
{
    #[Exposed]
    public function unexposed(int $amount): int
    {
        return parent::unexposed($amount);
    }
}

class InheritedExposedTestComponent extends ExposedUnexposedTestComponent
{
    public function unexposed(int $amount): int
    {
        return parent::unexposed($amount);
    }
}

class BlockedExposedTestComponent extends ExposedUnexposedTestComponent
{
    #[Unexposed]
    public function unexposed(int $amount): int
    {
        return parent::unexposed($amount);
    }
}

class ConflictingMethodAccessTestComponent extends ExposedUnexposedTestComponent
{
    #[Exposed]
    #[Unexposed]
    public function unexposed(int $amount): int
    {
        return parent::unexposed($amount);
    }
}
