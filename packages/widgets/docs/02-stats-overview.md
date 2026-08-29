---
title: Stats overview widgets
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

Filament comes with a "stats overview" widget template, which you can use to display a number of different stats in a single widget, without needing to write a custom view.

Start by creating a widget with the command:

```bash
php artisan make:filament-widget StatsOverview --stats-overview
```

This command will create a new `StatsOverview.php` file. Open it, and return `Stat` instances from the `getStats()` method:

```php
<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Unique views', '192.1k'),
            Stat::make('Bounce rate', '21%'),
            Stat::make('Average time on page', '3:12'),
        ];
    }
}
```

Now, check out your widget in the dashboard.

<AutoScreenshot name="widgets/stats-overview/simple" alt="Stats overview" version="5.x" />

## Adding a description and icon to a stat

You may add a `description()` to provide additional information, along with a `descriptionIcon()`:

```php
use Filament\Widgets\StatsOverviewWidget\Stat;

protected function getStats(): array
{
    return [
        Stat::make('Unique views', '192.1k')
            ->description('32k increase')
            ->descriptionIcon('heroicon-m-arrow-trending-up'),
        Stat::make('Bounce rate', '21%')
            ->description('7% decrease')
            ->descriptionIcon('heroicon-m-arrow-trending-down'),
        Stat::make('Average time on page', '3:12')
            ->description('3% increase')
            ->descriptionIcon('heroicon-m-arrow-trending-up'),
    ];
}
```

The `descriptionIcon()` method also accepts a second parameter to put the icon before the description instead of after it:

```php
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;

Stat::make('Unique views', '192.1k')
    ->description('32k increase')
    ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
```

<AutoScreenshot name="widgets/stats-overview/description" alt="Stats overview with descriptions" version="5.x" />

## Changing the color of the stat

You may also give stats a [color](../styling/colors):

```php
use Filament\Widgets\StatsOverviewWidget\Stat;

protected function getStats(): array
{
    return [
        Stat::make('Unique views', '192.1k')
            ->description('32k increase')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success'),
        Stat::make('Bounce rate', '21%')
            ->description('7% increase')
            ->descriptionIcon('heroicon-m-arrow-trending-down')
            ->color('danger'),
        Stat::make('Average time on page', '3:12')
            ->description('3% increase')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success'),
    ];
}
```

<AutoScreenshot name="widgets/stats-overview/color" alt="Stats overview with colors" version="5.x" />

## Adding extra HTML attributes to a stat

You may also pass extra HTML attributes to stats using `extraAttributes()`:

```php
use Filament\Widgets\StatsOverviewWidget\Stat;

protected function getStats(): array
{
    return [
        Stat::make('Processed', '192.1k')
            ->color('success')
            ->extraAttributes([
                'class' => 'cursor-pointer',
                'wire:click' => "\$dispatch('setStatusFilter', { filter: 'processed' })",
            ]),
        // ...
    ];
}
```

In this example, we are deliberately escaping the `$` in `$dispatch()` since this needs to be passed directly to the HTML, it is not a PHP variable.

## Adding a chart to a stat

You may also add or chain a `chart()` to each stat to provide historical data. The `chart()` method accepts an array of data points to plot:

```php
use Filament\Widgets\StatsOverviewWidget\Stat;

protected function getStats(): array
{
    return [
        Stat::make('Unique views', '192.1k')
            ->description('32k increase')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),
        // ...
    ];
}
```

<AutoScreenshot name="widgets/stats-overview/chart" alt="Stats overview with charts" version="5.x" />

## Styling stat charts in a theme

Chart.js paints a stat's chart onto a `<canvas>`, so its line cannot be reached from a stylesheet. A [custom theme](../styling/overview) is CSS only, so Filament exposes the shape of that line as CSS custom properties, which you may set on `.fi-wi-stats-overview-stat`, or on any element above it to cover every stat in the panel at once:

```css
.fi-wi-stats-overview-stat {
    --stat-chart-border-width: 1;
    --stat-chart-line-tension: 0;
    --stat-chart-fill: none;
}
```

`--stat-chart-border-width` thickens the line, `--stat-chart-line-tension` curves it, from `0` for straight segments up to `1`, and `--stat-chart-fill` shades the area beneath it, accepting `start`, `end`, `origin` or `stack`, as well as `none` to leave the line bare.

These values are handed to Chart.js rather than used by the browser, so they are plain numbers and keywords, without units. If you set one to something Chart.js cannot use, it is ignored and the chart keeps its default. They are also read again whenever the color scheme changes, so you may give light and dark mode different values.

The chart takes its colors from the [color of the stat](#changing-the-color-of-the-stat). To change them in a theme, style the `.fi-wi-stats-overview-stat-chart-bg-color` and `.fi-wi-stats-overview-stat-chart-border-color` elements with an ordinary `color` declaration:

```css
.fi-wi-stats-overview-stat {
    & .fi-wi-stats-overview-stat-chart-border-color {
        @apply text-gray-400 dark:text-gray-500;
    }
}
```

<Aside variant="info">
    These properties only affect the charts inside stats. [Chart widgets](charts#styling-charts-in-a-theme) are styled with their own set, prefixed `--chart-`.
</Aside>

## Live updating stats (polling)

By default, stats overview widgets refresh their data every 5 seconds.

To customize this, you may override the `$pollingInterval` property on the class to a new interval:

```php
protected ?string $pollingInterval = '10s';
```

Alternatively, you may disable polling altogether:

```php
protected ?string $pollingInterval = null;
```

## Disabling lazy loading

By default, widgets are lazy-loaded. This means that they will only be loaded when they are visible on the page.

To disable this behavior, you may override the `$isLazy` property on the widget class:

```php
protected static bool $isLazy = false;
```

## Adding a heading and description

You may also add heading and description text above the widget by overriding the `$heading` and `$description` properties:

```php
protected ?string $heading = 'Analytics';

protected ?string $description = 'An overview of some analytics.';
```

If you need to dynamically generate the heading or description text, you can instead override the `getHeading()` and `getDescription()` methods:

```php
protected function getHeading(): ?string
{
    return 'Analytics';
}

protected function getDescription(): ?string
{
    return 'An overview of some analytics.';
}
```

<AutoScreenshot name="widgets/stats-overview/heading" alt="Stats overview with heading and description" version="5.x" />
