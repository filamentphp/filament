<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class ChartWithFilterDemo extends ChartWidget
{
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Blog posts';

    public ?string $filter = 'year';

    protected function getData(): array
    {
        return match ($this->filter) {
            'today' => [
                'datasets' => [
                    [
                        'label' => 'Blog posts created',
                        'data' => [3, 1, 4, 2],
                    ],
                ],
                'labels' => ['6am', '9am', '12pm', '3pm'],
            ],
            'week' => [
                'datasets' => [
                    [
                        'label' => 'Blog posts created',
                        'data' => [4, 8, 3, 7, 5, 2, 6],
                    ],
                ],
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            ],
            'month' => [
                'datasets' => [
                    [
                        'label' => 'Blog posts created',
                        'data' => [12, 8, 15, 22],
                    ],
                ],
                'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            ],
            default => [
                'datasets' => [
                    [
                        'label' => 'Blog posts created',
                        'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                    ],
                ],
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            ],
        };
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
