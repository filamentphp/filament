<?php

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Tables\Components\TableContent;
use Filament\Tables\Components\TablePart;
use Filament\Tables\Components\TableStack;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

class FirstStubTablePart extends TablePart
{
    protected function getPartView(): string
    {
        return 'stub';
    }

    public function toEmbeddedHtml(): string
    {
        return '<p>first</p>';
    }
}

class SecondStubTablePart extends TablePart
{
    protected function getPartView(): string
    {
        return 'stub';
    }

    public function toEmbeddedHtml(): string
    {
        return '<p>second</p>';
    }
}

function makeTableForLayout(): Table
{
    return livewire(PostsTable::class)->instance()->getTable();
}

it('has no custom layout by default', function (): void {
    $table = makeTableForLayout();

    expect($table->hasCustomLayout())->toBeFalse()
        ->and($table->isContained())->toBeTrue()
        ->and($table->getLayout())->toBe($table->getDefaultLayout());
});

it('returns a complete default layout from `getDefaultLayout()`', function (): void {
    $table = makeTableForLayout();

    $layout = $table->getDefaultLayout();

    $table->assertLayoutIsComplete($layout);

    expect($layout->getComponents())->toHaveCount(3)
        ->and($layout->getComponents()[1])->toBeInstanceOf(TableStack::class);
});

it('can set `layout()` with a `Closure`', function (): void {
    $table = makeTableForLayout()
        ->layout(fn (Schema $schema): Schema => $schema->components([TableContent::make()]));

    expect($table->hasCustomLayout())->toBeTrue()
        ->and($table->getLayout())->toBeInstanceOf(Schema::class)
        ->and($table->getLayout()->getComponents())->toHaveCount(1)
        ->and($table->getLayout()->getLivewire())->toBe($table->getLivewire());
});

it('can set `layout()` with a `Schema`', function (): void {
    $table = makeTableForLayout();

    $layout = Schema::make($table->getLivewire())->components([TableContent::make()]);

    $table->layout($layout);

    expect($table->getLayout())->toBe($layout);
});

it('can set `contained()`', function (): void {
    $table = makeTableForLayout()->contained(false);

    expect($table->isContained())->toBeFalse();
});

it('throws when `assertLayoutIsComplete()` finds no `TableContent`', function (): void {
    $table = makeTableForLayout();

    $layout = Schema::make($table->getLivewire())->components([FirstStubTablePart::make()]);

    $table->assertLayoutIsComplete($layout);
})->throws(LogicException::class, 'does not contain a [Filament\Tables\Components\TableContent] part');

it('throws when `assertLayoutIsComplete()` finds a duplicate part', function (): void {
    $table = makeTableForLayout();

    $layout = Schema::make($table->getLivewire())->components([
        TableContent::make(),
        Group::make([TableContent::make()]),
    ]);

    $table->assertLayoutIsComplete($layout);
})->throws(LogicException::class, 'contains the [Filament\Tables\Components\TableContent] part more than once');

it('passes `assertLayoutIsComplete()` for parts nested in `Group` and `Grid`', function (): void {
    $table = makeTableForLayout();

    $layout = Schema::make($table->getLivewire())->components([
        FirstStubTablePart::make(),
        Group::make([
            Grid::make(2)->schema([
                TableContent::make(),
                SecondStubTablePart::make(),
            ]),
        ]),
    ]);

    $table->assertLayoutIsComplete($layout);

    expect(true)->toBeTrue();
});

it('renders the layout parts with `renderLayout()` and no wrapper element', function (): void {
    $table = makeTableForLayout();

    $layout = Schema::make($table->getLivewire())->components([
        FirstStubTablePart::make(),
        SecondStubTablePart::make()->hidden(),
        SecondStubTablePart::make(),
    ]);

    expect($table->renderLayout($layout))->toBe('<p>first</p><p>second</p>');
});

it('renders `TableStack` as a bare `div` with its attributes', function (): void {
    $table = makeTableForLayout();

    $layout = Schema::make($table->getLivewire())->components([
        TableStack::make([
            FirstStubTablePart::make(),
            SecondStubTablePart::make(),
        ])->extraAttributes([
            'class' => 'a',
            'x-cloak' => true,
            'x-show' => false,
            'wire:key' => 'k',
        ]),
    ]);

    expect($table->renderLayout($layout))->toBe('<div class="a" x-cloak wire:key="k"><p>first</p><p>second</p></div>');
});

it('throws when a part is used outside a table Livewire component', function (): void {
    $layout = Schema::make(Livewire::make())
        ->components([FirstStubTablePart::make()]);

    $layout->getComponents()[0]->getTable();
})->throws(LogicException::class, 'can only be used inside a Livewire component that implements [Filament\Tables\Contracts\HasTable]');
