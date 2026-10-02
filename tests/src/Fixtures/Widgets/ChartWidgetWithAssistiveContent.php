<?php

namespace Filament\Tests\Fixtures\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\View\View;

class ChartWidgetWithAssistiveContent extends ChartWidget
{
    protected static bool $isLazy = false;

    protected ?string $heading = 'Sales';

    protected ?string $pollingInterval = null;

    public ?string $filter = '2024';

    public function getDescription(): string
    {
        return "Sales for {$this->filter}";
    }

    public function getChartAssistiveContent(): View
    {
        return view()->file(dirname(__DIR__, 3) . '/resources/views/widgets/chart-assistive-content.blade.php', [
            'year' => $this->filter,
        ]);
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => [10, 20, 30],
                ],
            ],
            'labels' => ['January', 'February', 'March'],
        ];
    }

    protected function getFilters(): array
    {
        return [
            '2024' => '2024',
            '2023' => '2023',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
