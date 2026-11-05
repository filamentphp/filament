<?php

namespace Filament\Tests\Upgrade\Fixtures\ChartHookBoundaries;

use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersForm;

abstract class ConflictingChart extends ChartWidget
{
    use HasFiltersForm;

    public function filtersSchema(Schema $schema): Schema { return $schema->columns(2); }
    public function filtersForm(Schema $schema): Schema { return $schema->columns(3); }
}

trait SharedGetter
{
    public function getFiltersSchema(): Schema { return $this->buildSchema(); }
}

abstract class SharedChart extends ChartWidget
{
    use HasFiltersForm;
    use SharedGetter;
}

class Unrelated
{
    use SharedGetter;

    public function filtersSchema(Schema $schema): Schema { return $schema; }
}

function inspect(ConflictingChart $chart, SharedChart $shared, Unrelated $unrelated, Schema $schema): void
{
    $chart->filtersSchema($schema);
    $shared->getFiltersSchema();
    $unrelated->filtersSchema($schema);
    $unrelated->getFiltersSchema();
}
