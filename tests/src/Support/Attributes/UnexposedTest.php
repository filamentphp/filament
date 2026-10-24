<?php

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Filament\Tables\Table;
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

it('retains native trait alias attributes without blocking direct PHP calls', function (): void {
    $component = Livewire::test(new class extends UnexposedTestBaseComponent
    {
        use HasUnexposedTestMethod {
            unexposed as aliasedUnexposed;
        }
    });

    $instance = $component->instance();

    $component->call('aliasedUnexposed', 3)->assertForbidden();

    expect(UnexposedTestComponent::$executionCount)->toBe(0)
        ->and($instance->aliasedUnexposed(5))->toBe(5);
});

it('does not inherit access attributes from a replaced trait method', function (): void {
    $component = Livewire::test(new class extends Component
    {
        use HasUnexposedTestMethod;

        public function unexposed(int $amount): int
        {
            return UnexposedTestComponent::$executionCount += $amount;
        }

        public function render(): string
        {
            return '<div></div>';
        }
    });

    $component->call('unexposed', 3)->assertReturned(3);

    expect(UnexposedTestComponent::$executionCount)->toBe(3);
});

it('rejects component-returning methods before execution without an access attribute', function (string $method): void {
    Livewire::test(ReturnTypeAccessTestComponent::class)
        ->call($method)
        ->assertForbidden();

    expect(UnexposedTestComponent::$executionCount)->toBe(0);
})->with(['schema', 'table', 'action', 'customSchema']);

it('rejects component-returning event listeners before execution', function (): void {
    Livewire::test(ReturnTypeAccessTestComponent::class)
        ->dispatch('schema-requested')
        ->assertForbidden();

    expect(UnexposedTestComponent::$executionCount)->toBe(0);
});

it('lets explicit exposure override the return-type default', function (string $componentClass): void {
    Livewire::test($componentClass)
        ->call('exposedSchema')
        ->assertSuccessful();

    expect(UnexposedTestComponent::$executionCount)->toBe(1);
})->with([
    'declared exposure' => [ReturnTypeAccessTestComponent::class],
    'inherited exposure' => [InheritedReturnTypeAccessTestComponent::class],
]);

it('does not infer component return types from mixed, array, union, or untyped declarations', function (string $method): void {
    Livewire::test(ReturnTypeAccessTestComponent::class)
        ->call($method)
        ->assertSuccessful();

    expect(UnexposedTestComponent::$executionCount)->toBe(1);
})->with(['mixedSchema', 'arraySchema', 'unionSchema', 'untypedSchema']);

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

class ReturnTypeAccessTestSchema extends Schema {}

class ReturnTypeAccessTestComponent extends UnexposedTestBaseComponent
{
    #[On('schema-requested')]
    public function schema(): Schema
    {
        static::$executionCount++;

        return Schema::make();
    }

    public function table(): ?Table
    {
        static::$executionCount++;

        return null;
    }

    public function action(): Action
    {
        static::$executionCount++;

        return Action::make('test');
    }

    public function customSchema(): ReturnTypeAccessTestSchema
    {
        static::$executionCount++;

        return ReturnTypeAccessTestSchema::make();
    }

    #[Exposed]
    public function exposedSchema(): ?Schema
    {
        static::$executionCount++;

        return null;
    }

    public function mixedSchema(): mixed
    {
        static::$executionCount++;

        return Schema::make();
    }

    public function arraySchema(): array
    {
        static::$executionCount++;

        return [Schema::make()];
    }

    public function unionSchema(): Schema | Action
    {
        static::$executionCount++;

        return Schema::make();
    }

    public function untypedSchema()
    {
        static::$executionCount++;

        return Schema::make();
    }
}

class InheritedReturnTypeAccessTestComponent extends ReturnTypeAccessTestComponent
{
    public function exposedSchema(): ?Schema
    {
        return parent::exposedSchema();
    }
}
