<?php

namespace Filament\Tests\PHPStan;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersForm;

class FormFilteredChart extends ChartWidget
{
    use HasFiltersForm;

    protected function getType(): string
    {
        return 'line';
    }
}
