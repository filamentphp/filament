<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class PieChartDemo extends ChartWidget
{
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Posts by category';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Posts',
                    'data' => [35, 25, 20, 12, 8],
                    'backgroundColor' => ['#f59e0b', '#ef4444', '#3b82f6', '#10b981', '#8b5cf6'],
                ],
            ],
            'labels' => ['Technology', 'Design', 'Marketing', 'Business', 'Other'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'aspectRatio' => 2,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
