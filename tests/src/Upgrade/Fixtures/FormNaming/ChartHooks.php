<?php

namespace Filament\Tests\Upgrade\Fixtures\FormNaming;

use Filament\Schemas\Schema;

trait ChartGetter
{
    public function getFiltersSchema(): Schema
    {
        return parent::getFiltersSchema()->columns(4);
    }

    public function inspectFilters(): Schema
    {
        return $this->getFiltersSchema();
    }
}

class GetterChart extends ChildChart
{
    use ChartGetter {
        getFiltersSchema as inspectAlias;
    }

    public function restore(): void
    {
        $this->resetFiltersForm();
    }
}

function chartSchema(GetterChart $chart): Schema
{
    return $chart->getFiltersSchema();
}
