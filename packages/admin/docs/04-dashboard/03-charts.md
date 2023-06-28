---
title: Charts
---

## Getting started

Filament comes with many "chart" widget template, which you can use to display real-time, interactive charts.

Start by creating a widget with the command:

```bash
php artisan make:filament-widget BlogPostsChart --chart
```

There are several chart classes available, but we'll use the `LineChartWidget` class for this example.

The `getHeading()` method is used to return a heading that describes the chart.

The `getData()` method is used to return an array of datasets and labels. Each dataset is a labelled array of points to plot on the chart, and each label is a string. This structure is identical with the [Chart.js](https://www.chartjs.org/docs) library, which Filament uses to render charts. You may use the [Chart.js documentation](https://www.chartjs.org/docs) to fully understand the possibilities to return from `getData()`, based on the chart type.

```php
<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;

class BlogPostsChart extends LineChartWidget
{
    protected function getHeading(): string
    {
        return 'Blog posts';
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Blog posts created',
                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }
}
```

Now, check out your widget in the dashboard.

## Available chart types

Below is a list of available chart widget classes which you may extend, and their corresponding [Chart.js](https://www.chartjs.org/docs) documentation page, for inspiration what to return from `getData()`:

- `Filament\Widgets\BarChartWidget` - [Chart.js documentation](https://www.chartjs.org/docs/latest/charts/bar)
- `Filament\Widgets\BubbleChartWidget` - [Chart.js documentation](https://www.chartjs.org/docs/latest/charts/bubble)
- `Filament\Widgets\DoughnutChartWidget` - [Chart.js documentation](https://www.chartjs.org/docs/latest/charts/doughnut)
- `Filament\Widgets\LineChartWidget` - [Chart.js documentation](https://www.chartjs.org/docs/latest/charts/line)
- `Filament\Widgets\PieChartWidget` - [Chart.js documentation](https://www.chartjs.org/docs/latest/charts/doughnut)
- `Filament\Widgets\PolarAreaChartWidget` - [Chart.js documentation](https://www.chartjs.org/docs/latest/charts/polar)
- `Filament\Widgets\RadarChartWidget` - [Chart.js documentation](https://www.chartjs.org/docs/latest/charts/radar)
- `Filament\Widgets\ScatterChartWidget` - [Chart.js documentation](https://www.chartjs.org/docs/latest/charts/scatter)

## Generating chart data from an Eloquent model

To generate chart data from an Eloquent model, Filament recommends that you install the `flowframe/laravel-trend` package. You can view the [documentation](https://github.com/Flowframe/laravel-trend).

Here is an example of generating chart data from a model using the `laravel-trend` package:

```php
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

protected function getData(): array
{
    $data = Trend::model(BlogPost::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();

    return [
        'datasets' => [
            [
                'label' => 'Blog posts',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];
}
```

## Filtering chart data

You can set up chart filters to change the data shown on chart. Commonly, this is used to change the time period that chart data is rendered for.

To set a default filter value, set the `$filter` property:

```php
public ?string $filter = 'today';
```

Then, define the `getFilters()` method to return an array of values and labels for your filter:

```php
protected function getFilters(): ?array
{
    return [
        'today' => 'Today',
        'week' => 'Last week',
        'month' => 'Last month',
        'year' => 'This year',
    ];
}
```

You can use the active filter value within your `getData()` method:

```php
protected function getData(): array
{
    $activeFilter = $this->filter;
    
    // ...
}
```

## Live updating (polling)

By default, chart widgets refresh their data every 5 seconds.

To customize this, you may override the `$pollingInterval` property on the class to a new interval:

```php
protected static ?string $pollingInterval = '10s';
```

Alternatively, you may disable polling altogether:

```php
protected static ?string $pollingInterval = null;
```

## Setting a maximum chart height

You may place a maximum height on the chart to ensure that it doesn't get too big, using the `$maxHeight` property:

```php
protected static ?string $maxHeight = '300px';
```

## Setting chart configuration options

You may specify an `$options` variable on the chart class to control the many configuration options that the Chart.js library provides. For instance, you could turn off the [legend](https://www.chartjs.org/docs/latest/configuration/legend.html) for `LineChartWidget` class:

```php
protected static ?array $options = [
    'plugins' => [
        'legend' => [
            'display' => false,
        ],
    ],
];
```
