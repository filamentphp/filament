<?php

namespace Filament\Widgets;

// Original implementations from `3f0fa1263989c514dc1cee7b88353706bbbdc357`.
// Kept outside production autoloading to test upgrades after removal.
class BarChartWidget extends ChartWidget
{
    protected function getType(): string
    {
        return 'bar';
    }
}

class BubbleChartWidget extends ChartWidget
{
    protected function getType(): string
    {
        return 'bubble';
    }
}

class DoughnutChartWidget extends ChartWidget
{
    protected function getType(): string
    {
        return 'doughnut';
    }
}

class LineChartWidget extends ChartWidget
{
    protected function getType(): string
    {
        return 'line';
    }
}

class PieChartWidget extends ChartWidget
{
    protected function getType(): string
    {
        return 'pie';
    }
}

class PolarAreaChartWidget extends ChartWidget
{
    protected function getType(): string
    {
        return 'polarArea';
    }
}

class RadarChartWidget extends ChartWidget
{
    protected function getType(): string
    {
        return 'radar';
    }
}

class ScatterChartWidget extends ChartWidget
{
    protected function getType(): string
    {
        return 'scatter';
    }
}
