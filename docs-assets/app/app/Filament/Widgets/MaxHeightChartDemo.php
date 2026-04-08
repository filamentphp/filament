<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class MaxHeightChartDemo extends ChartWidget
{
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Revenue';

    protected ?string $maxHeight = '150px';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => [12400, 14800, 13200, 16100, 15400, 17800, 19200, 21400, 18900, 22100, 24800, 26200],
                    'fill' => 'start',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
