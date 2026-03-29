<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class BubbleChartDemo extends ChartWidget
{
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Category analysis';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Categories',
                    'data' => [
                        ['x' => 20, 'y' => 30, 'r' => 15],
                        ['x' => 40, 'y' => 10, 'r' => 10],
                        ['x' => 15, 'y' => 45, 'r' => 20],
                        ['x' => 35, 'y' => 25, 'r' => 12],
                        ['x' => 50, 'y' => 40, 'r' => 18],
                        ['x' => 25, 'y' => 15, 'r' => 8],
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bubble';
    }
}
