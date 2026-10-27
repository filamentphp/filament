<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedSortHook;

use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Livewire\Component;

abstract class TableComponent extends Component implements HasTable
{
    use InteractsWithTable;
}

function migrate(TableComponent $component, ?TableComponent $nullable, array $arguments): void
{
    $component->updatedTableSortDirection();
    $nullable?->updatedTableSortDirection();
    $component->updatedTableSortDirection(...$arguments);
    $callback = $component->updatedTableSortDirection(...);
    $component->updatedTableSort();
}

abstract class CustomLegacyHook extends TableComponent
{
    public function updatedTableSortDirection(): void
    {
        parent::updatedTableSortDirection();
    }
}

abstract class CustomCanonicalHook extends TableComponent
{
    public function updatedTableSort(): void {}
}

abstract class InheritedCustomHook extends CustomCanonicalHook {}

function manual(CustomLegacyHook $legacy, InheritedCustomHook $canonical, HasTable $unknownImplementation, object $unrelated, TableComponent $component): void
{
    $legacy->updatedTableSortDirection();
    $canonical->updatedTableSortDirection();
    $unknownImplementation->updatedTableSortDirection();
    $unrelated->updatedTableSortDirection();
    $component->{'updatedTableSortDirection'}();
    $callback = [$component, 'updatedTableSortDirection'];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedSortHook;

use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Livewire\Component;

abstract class TableComponent extends Component implements HasTable
{
    use InteractsWithTable;
}

function migrate(TableComponent $component, ?TableComponent $nullable, array $arguments): void
{
    $component->updatedTableSort();
    $nullable?->updatedTableSort();
    $component->updatedTableSort(...$arguments);
    $callback = $component->updatedTableSort(...);
    $component->updatedTableSort();
}

abstract class CustomLegacyHook extends TableComponent
{
    public function updatedTableSortDirection(): void
    {
        parent::updatedTableSortDirection();
    }
}

abstract class CustomCanonicalHook extends TableComponent
{
    public function updatedTableSort(): void {}
}

abstract class InheritedCustomHook extends CustomCanonicalHook {}

function manual(CustomLegacyHook $legacy, InheritedCustomHook $canonical, HasTable $unknownImplementation, object $unrelated, TableComponent $component): void
{
    $legacy->updatedTableSortDirection();
    $canonical->updatedTableSortDirection();
    $unknownImplementation->updatedTableSortDirection();
    $unrelated->updatedTableSortDirection();
    $component->{'updatedTableSortDirection'}();
    $callback = [$component, 'updatedTableSortDirection'];
}
