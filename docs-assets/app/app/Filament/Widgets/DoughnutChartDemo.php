<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class DoughnutChartDemo extends ChartWidget
{
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Traffic sources';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Visitors',
                    'data' => [42, 28, 18, 12],
                    'backgroundColor' => ['#f59e0b', '#3b82f6', '#ef4444', '#10b981'],
                ],
            ],
            'labels' => ['Organic search', 'Direct', 'Social media', 'Referral'],
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
        return 'doughnut';
    }
}
