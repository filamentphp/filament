<?php

namespace Filament\Tests\Schemas\Components;

use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\Exposed;
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
    expect($component->getExposedMethodNames())->toEqualCanonicalizing(['inherited', 'fluent']);
    $methods = $manager->extractPublicMethods($component);
    expect($methods['inherited']())->toBe('Inherited')
        ->and($methods)->not->toHaveKey('fluent')
        ->and($component->getExposedMethodNames())->toEqualCanonicalizing(['inherited', 'fluent']);
    $other = new ExposedMethodsParent;
    expect($other->getExposedMethodNames())->toEqualCanonicalizing(['inherited', 'overridden']);
})->with([false, true]);

it('discovers and calls annotated methods while rejecting unannotated schema methods', function (): void {
    $component = new class extends Component
    {
        use ExposedTestMethods;
    };
    $livewire = Mockery::mock(Livewire::class)->makePartial();
    $livewire->shouldReceive('getSchemaComponent')->with('counter')->andReturn($component);
    $component->container(Schema::make($livewire));

    expect($component->getExposedMethodNames())->toEqualCanonicalizing(['current', 'additional']);

    foreach (['current', 'additional'] as $method) {
        expect($livewire->callSchemaComponentMethod('counter', $method, ['value' => 7]))->toBe(14);
        expect((new ReflectionMethod($component, $method))->getAttributes()[0]->newInstance())->toBeInstanceOf(Exposed::class);
    }

    expect($livewire->callSchemaComponentMethod('counter', 'unannotated', ['value' => 7]))->toBeNull();
});

it('calls annotated methods while rejecting unannotated table column methods', function (): void {
    $column = new class('counter') extends Column
    {
        use ExposedTestMethods;
    };
    $table = Mockery::mock(Table::class);
    $table->shouldReceive('getColumn')->with('counter')->andReturn($column);
    $livewire = Mockery::mock(HasColumns::class)->makePartial();
    $livewire->shouldReceive('getTable')->andReturn($table);
    $livewire->shouldReceive('getTableRecord')->with('42')->andReturn(new User);

    foreach (['current', 'additional'] as $method) {
        expect($livewire->callTableColumnMethod('counter', '42', $method, ['value' => 7]))->toBe(14);
    }

    expect($livewire->callTableColumnMethod('counter', '42', 'unannotated', ['value' => 7]))->toBeNull();
});

it('scopes exposed methods and embedded `$get()` and `$set()` in lists, tables and liberated layouts', function (): void {
    $this->actingAs(User::factory()->create());

    $page = visit('/schema-component-scope-browser-test');
    $state = "JSON.parse(document.querySelector('[data-testid=server-state]').textContent)";

    $page->click('[data-testid=list-select] button[aria-haspopup=listbox]')
        ->fill('[data-testid=list-select] input[role=combobox]', 'Café')
        ->click('[data-testid=list-select] [role=option]:has-text("Match Café")')
        ->assertScript("{$state}.choice", 'chosen');

    foreach (['list', 'liberated'] as $name) {
        $page->click("[data-blade-method={$name}] [data-testid=named-call]")
            ->assertSeeIn("[data-blade-method={$name}] output", "data.{$name}: Named café")
            ->assertScript("{$state}.{$name}", 'Named café')
            ->click("[data-blade-method={$name}] [data-testid=general-call]")
            ->assertSeeIn("[data-blade-method={$name}] output", "data.{$name}: General café")
            ->assertScript("{$state}.{$name}", 'General café');
    }

    $page->click('[data-blade-method=field] [data-testid=named-call] >> nth=1')
        ->assertSeeIn('[data-blade-method=field] output >> nth=1', 'data.items.1.field: Named café')
        ->assertScript("{$state}.items[1].field", 'Named café')
        ->assertScript("{$state}.items[0].field", 'First field')
        ->click('[data-blade-method=caption] [data-testid=named-call] >> nth=1')
        ->assertSeeIn('[data-blade-method=caption] output >> nth=1', 'data.items.1.caption: Second caption: Named café')
        ->click('[data-blade-method=caption] [data-testid=general-call] >> nth=0')
        ->assertSeeIn('[data-blade-method=caption] output >> nth=0', 'data.items.0.caption: First caption: General café')
        ->click('[data-testid=remove-first]')
        ->assertCount('[data-blade-method=field]', 1)
        ->click('[data-blade-method=field] [data-testid=general-call]')
        ->assertSeeIn('[data-blade-method=field] output', 'data.items.1.field: General café')
        ->assertScript("{$state}.items[1].field", 'General café')
        ->click('[data-blade-method=caption] [data-testid=general-call]')
        ->assertSeeIn('[data-blade-method=caption] output', 'data.items.1.caption: Second caption: General café');

    $rootContent = '[data-field-wrapper]:has([data-blade-method=liberated]) [data-embedded-scope]';
    $rowContent = 'td:has([data-blade-method=field]) [data-embedded-scope]';

    $page->assertSeeIn($rootContent . ' span', 'Root "caption" <em>literal</em>')
        ->assertSeeIn($rowContent . ' span', 'Second caption')
        ->click($rootContent . ' button')
        ->assertScript("{$state}.caption", 'Embedded café')
        ->assertScript("{$state}.items[1].caption", 'Second caption')
        ->assertSeeIn($rootContent . ' span', 'Embedded café')
        ->assertSeeIn($rowContent . ' span', 'Second caption')
        ->click($rowContent . ' button')
        ->assertScript("{$state}.items[1].caption", 'Embedded café')
        ->assertScript("{$state}.caption", 'Embedded café')
        ->assertSeeIn($rowContent . ' span', 'Embedded café')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    $page->inDarkMode()->assertNoAccessibilityIssues();
});

trait ExposedTestMethods
{
    #[Exposed]
    public function current(int $value): int
    {
        return $value * 2;
    }

    #[Exposed]
    public function additional(int $value): int
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
