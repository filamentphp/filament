<?php

namespace Filament\Widgets;

/**
 * @deprecated Extend `ChartWidget` instead and define the `getType()` method.
 */
class BarChartWidget extends ChartWidget
{
    protected function getType(): string
    {
        return 'bar';
    }
}
