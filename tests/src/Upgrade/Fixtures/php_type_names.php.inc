<?php

namespace Filament\Tests\Upgrade\Fixtures\PhpTypeNames;

use Filament\Forms\Components\Concerns\CanLimitItemsLength as ItemLimits;
use Filament\Forms\Components\Field;
use Filament\Schemas\View\SchemaIconAlias as SchemaIcons;
use Filament\Tables\Actions\HeaderActionsPosition as Position;
use Filament\Tables\Columns\Concerns\HasRowLoopObject as RowLoop;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\CanDeferLoading as LivewireLoading;
use Filament\Tables\Table;
use Filament\Tables\Table\Concerns\CanDeferLoading as TableLoading;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Widgets\View\Components\StatsOverviewWidgetComponent\StatComponent\StatsOverviewWidgetStatChartComponent as ChartColors;

class LimitedField extends Field
{
    use ItemLimits {
        ItemLimits::getMaxItems as maximum;
    }
}

class LoopColumn extends TextColumn
{
    use RowLoop {
        RowLoop::getRowLoop as currentRow;
    }
}

class LoadingTable extends Table
{
    use TableLoading;
}

class LoadingComponent extends PostsTable
{
    use LivewireLoading;
}

class CustomChartColors extends ChartColors
{
    public function getColorMap(array $color): array
    {
        return ['bg' => 300] + parent::getColorMap($color);
    }
}

function position(): Position
{
    return Position::Bottom;
}

function registerChartColors(): void
{
    app()->bind(ChartColors::class, CustomChartColors::class);
}

return [
    SchemaIcons::COMPONENTS_CALLOUT_DANGER,
    Position::Adaptive,
    ChartColors::class,
    'Filament\Schemas\View\SchemaIconAlias',
    'Filament\Tables\Actions\HeaderActionsPosition',
];
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\PhpTypeNames;

use Filament\Forms\Components\Concerns\CanLimitItemsCount;
use Filament\Tables\Columns\Concerns\HasRowLoop;
use Filament\Tables\Table\Concerns\CanBeLazyLoaded;
use Filament\Widgets\View\Components\StatsOverviewWidgetComponent\StatComponent\ChartComponent;
use Filament\Tables\Enums\HeaderActionsPosition;
use Filament\Schemas\View\SchemasIconAlias;
use Filament\Forms\Components\Field;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\PostsTable;

class LimitedField extends Field
{
    use CanLimitItemsCount {
        CanLimitItemsCount::getMaxItems as maximum;
    }
}

class LoopColumn extends TextColumn
{
    use HasRowLoop {
        HasRowLoop::getRowLoop as currentRow;
    }
}

class LoadingTable extends Table
{
    use CanBeLazyLoaded;
}

class LoadingComponent extends PostsTable
{
    use \Filament\Tables\Concerns\CanBeLazyLoaded;
}

class CustomChartColors extends ChartComponent
{
    public function getColorMap(array $color): array
    {
        return ['bg' => 300] + parent::getColorMap($color);
    }
}

function position(): HeaderActionsPosition
{
    return HeaderActionsPosition::Bottom;
}

function registerChartColors(): void
{
    app()->bind(ChartComponent::class, CustomChartColors::class);
}

return [
    SchemasIconAlias::COMPONENTS_CALLOUT_DANGER,
    HeaderActionsPosition::Adaptive,
    ChartComponent::class,
    'Filament\Schemas\View\SchemaIconAlias',
    'Filament\Tables\Actions\HeaderActionsPosition',
];
