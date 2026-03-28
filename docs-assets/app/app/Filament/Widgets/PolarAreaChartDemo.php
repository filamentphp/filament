<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class PolarAreaChartDemo extends ChartWidget
{
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Content performance';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Engagement',
                    'data' => [85, 62, 74, 48, 91],
                    'backgroundColor' => ['rgba(245, 158, 11, 0.6)', 'rgba(59, 130, 246, 0.6)', 'rgba(239, 68, 68, 0.6)', 'rgba(16, 185, 129, 0.6)', 'rgba(139, 92, 246, 0.6)'],
                ],
            ],
            'labels' => ['Blog posts', 'Tutorials', 'Videos', 'Podcasts', 'Newsletters'],
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
        return 'polarArea';
    }
}
