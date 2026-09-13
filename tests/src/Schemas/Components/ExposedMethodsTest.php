<?php

namespace Filament\Tests\Schemas\Components;

use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Filament\Support\Components\ComponentManager;
use Filament\Tables\Columns\Column;
use Filament\Tables\Concerns\HasColumns;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Mockery;
use ReflectionMethod;

uses(TestCase::class);

it('discovers only public instance methods with `Exposed` independently of Blade extraction order', function (bool $bladeFirst): void {
    // Use distinct concrete classes so both orders start with cold caches.
    $component = $bladeFirst
        ? new class extends ExposedMethodsChild {}
    : new class extends ExposedMethodsChild {};
    $manager = app(ComponentManager::class);
    if ($bladeFirst) {
        $manager->extractPublicMethods($component);
    }
    expect($component->getExposedLivewireMethodNames())->toEqualCanonicalizing(['inherited', 'fluent']);
    $methods = $manager->extractPublicMethods($component);
    expect($methods['inherited']())->toBe('Inherited')
        ->and($methods)->not->toHaveKey('fluent')
        ->and($component->getExposedLivewireMethodNames())->toEqualCanonicalizing(['inherited', 'fluent']);
    $other = new ExposedMethodsParent;
    expect($other->getExposedLivewireMethodNames())->toEqualCanonicalizing(['inherited', 'overridden']);
})->with([false, true]);

it('discovers and calls both attribute names while rejecting unannotated schema methods', function (): void {
    $component = new class extends Component
    {
        use ExposedCompatibilityMethods;
    };
    $livewire = Mockery::mock(Livewire::class)->makePartial();
    $livewire->shouldReceive('getSchemaComponent')->with('counter')->andReturn($component);
    $component->container(Schema::make($livewire));

    expect($component->getExposedLivewireMethodNames())->toEqualCanonicalizing(['current', 'legacy']);

    foreach (['current', 'legacy'] as $method) {
        expect($livewire->callSchemaComponentMethod('counter', $method, ['value' => 7]))->toBe(14);
        expect((new ReflectionMethod($component, $method))->getAttributes()[0]->newInstance())->toBeInstanceOf(Exposed::class);
    }

    expect($livewire->callSchemaComponentMethod('counter', 'unannotated', ['value' => 7]))->toBeNull();
});

it('calls both attribute names while rejecting unannotated table column methods', function (): void {
    $column = new class('counter') extends Column
    {
        use ExposedCompatibilityMethods;
    };
    $table = Mockery::mock(Table::class);
    $table->shouldReceive('getColumn')->with('counter')->andReturn($column);
    $livewire = Mockery::mock(HasColumns::class)->makePartial();
    $livewire->shouldReceive('getTable')->andReturn($table);
    $livewire->shouldReceive('getTableRecord')->with('42')->andReturn(new User);

    foreach (['current', 'legacy'] as $method) {
        expect($livewire->callTableColumnMethod('counter', '42', $method, ['value' => 7]))->toBe(14);
    }

    expect($livewire->callTableColumnMethod('counter', '42', 'unannotated', ['value' => 7]))->toBeNull();
});

trait ExposedCompatibilityMethods
{
    #[Exposed]
    public function current(int $value): int
    {
        return $value * 2;
    }

    #[ExposedLivewireMethod]
    public function legacy(int $value): int
    {
        return $value * 2;
    }

    public function unannotated(int $value): int
    {
        return $value * 3;
    }
}

class ExposedMethodsParent extends Component
{
    #[Exposed]
    public function inherited(): string
    {
        return 'Inherited';
    }

    #[Exposed]
    public function overridden(): string
    {
        return 'Parent';
    }

    public function ordinary(): void {}
}

class ExposedMethodsChild extends ExposedMethodsParent
{
    public function overridden(): string
    {
        return 'Not exposed';
    }

    #[Exposed]
    public function fluent(): static
    {
        return $this;
    }

    #[Exposed]
    protected function protectedMethod(): void {}

    #[Exposed]
    private function privateMethod(): void {}

    #[Exposed]
    public static function staticMethod(): void {}
}
