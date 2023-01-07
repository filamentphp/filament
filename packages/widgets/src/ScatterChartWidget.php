<?php

namespace Filament\Widgets;

/**
 * @deprecated Extend `ChartWidget` instead and define the `getType()` method.
 */
class ScatterChartWidget extends ChartWidget
{
    protected function getType(): string
    {
        return 'scatter';
    }
}
