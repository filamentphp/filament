<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class ScatterChartDemo extends ChartWidget
{
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Post length vs. engagement';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Blog posts',
                    'data' => [
                        ['x' => 200, 'y' => 45],
                        ['x' => 450, 'y' => 72],
                        ['x' => 800, 'y' => 88],
                        ['x' => 1200, 'y' => 95],
                        ['x' => 350, 'y' => 58],
                        ['x' => 600, 'y' => 78],
                        ['x' => 900, 'y' => 82],
                        ['x' => 1500, 'y' => 91],
                        ['x' => 100, 'y' => 30],
                        ['x' => 700, 'y' => 85],
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'scatter';
    }
}
