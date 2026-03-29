<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class RadarChartDemo extends ChartWidget
{
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Skills assessment';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Developer A',
                    'data' => [85, 72, 90, 65, 78, 88],
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                    'borderColor' => '#f59e0b',
                ],
                [
                    'label' => 'Developer B',
                    'data' => [70, 88, 60, 90, 82, 75],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => '#3b82f6',
                ],
            ],
            'labels' => ['PHP', 'JavaScript', 'CSS', 'DevOps', 'Testing', 'Architecture'],
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
        return 'radar';
    }
}
