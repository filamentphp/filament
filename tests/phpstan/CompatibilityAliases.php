<?php

namespace Filament\Tests\PHPStan;

use Filament\Actions\Action;
use Filament\Actions\Concerns\CanBeLabeledFrom;
use Filament\Forms\Components\Concerns\CanLimitItemsCount;
use Filament\Forms\Components\Select;
use Filament\Support\Concerns\CanBeLazy;
use Filament\Tables\Columns\Concerns\HasRowLoop;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\CanBeLazyLoaded as LivewireCanBeLazyLoaded;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Concerns\HasSchema;
use Filament\Tables\Table;
use Filament\Tables\Table\Concerns\CanBeLazyLoaded;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersForm;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Filament\Widgets\Widget;

class LegacyResponsiveLabelAction extends Action
{
    use CanBeLabeledFrom;
}

class LegacyLazyWidget extends Widget
{
    use CanBeLazy;
}

class ItemCountSelect extends Select
{
    use CanLimitItemsCount;
}

class RowLoopTextColumn extends TextColumn
{
    use HasRowLoop;
}

class LazyLoadedTable extends Table
{
    use CanBeLazyLoaded;
}

class LazyLoadedTableComponent extends PostsTable
{
    use LivewireCanBeLazyLoaded;
}

class LegacyFormFilter extends BaseFilter
{
    use HasSchema;
}

class LegacyFilteredChart extends ChartWidget
{
    use HasFiltersSchema;

    protected function getType(): string
    {
        return 'line';
    }
}

class FormFilteredChart extends ChartWidget
{
    use HasFiltersForm;

    protected function getType(): string
    {
        return 'line';
    }
}
