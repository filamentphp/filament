<?php

use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\TestCase;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

it('can be constructed with `make()`', function (): void {
    $stat = Stat::make('Total Users', 1234);

    expect($stat)->toBeInstanceOf(Stat::class);
    expect($stat->getLabel())->toBe('Total Users');
    expect($stat->getValue())->toBe(1234);
});

it('can set `value()` with a string', function (): void {
    $stat = Stat::make('Revenue', '$10,000');

    expect($stat->getValue())->toBe('$10,000');
});

it('can set `value()` with a `Closure`', function (): void {
    $stat = Stat::make('Count', static fn (): int => 42);

    expect($stat->getValue())->toBe(42);
});

it('can set `value()` with an `Htmlable`', function (): void {
    $htmlable = new HtmlString('<strong>100</strong>');
    $stat = Stat::make('Count', $htmlable);

    expect($stat->getValue())->toBe($htmlable);
});

describe('chart', function (): void {
    it('returns `null` for `getChart()` by default', function (): void {
        $stat = Stat::make('Count', 1);

        expect($stat->getChart())->toBeNull();
    });

    it('can set `chart()` with an array', function (): void {
        $data = [1.0, 3.0, 5.0, 7.0, 4.0, 2.0];
        $stat = Stat::make('Trend', 100)
            ->chart($data);

        expect($stat->getChart())->toBe($data);
    });

    it('ignores `chart()` when `null` is passed', function (): void {
        $stat = Stat::make('Count', 1)
            ->chart(null);

        expect($stat->getChart())->toBeNull();
    });

    it('can set `chart()` with an `Arrayable`', function (): void {
        $collection = collect([1.0, 2.0, 3.0]);
        $stat = Stat::make('Trend', 100)
            ->chart($collection);

        expect($stat->getChart())->toBe([1.0, 2.0, 3.0]);
    });
});

describe('chart color', function (): void {
    it('falls back to `getColor()` for `getChartColor()` by default', function (): void {
        $stat = Stat::make('Count', 1)
            ->color('success');

        expect($stat->getChartColor())->toBe('success');
    });

    it('can set `chartColor()` independently', function (): void {
        $stat = Stat::make('Count', 1)
            ->color('success')
            ->chartColor('danger');

        expect($stat->getChartColor())->toBe('danger');
    });

    it('returns `null` for `getChartColor()` when neither color nor chart color set', function (): void {
        $stat = Stat::make('Count', 1);

        expect($stat->getChartColor())->toBeNull();
    });
});

describe('icon', function (): void {
    it('returns `null` for `getIcon()` by default', function (): void {
        $stat = Stat::make('Count', 1);

        expect($stat->getIcon())->toBeNull();
    });

    it('can set `icon()` with a string', function (): void {
        $stat = Stat::make('Users', 100)
            ->icon('heroicon-o-users');

        expect($stat->getIcon())->toBe('heroicon-o-users');
    });

    it('can set `icon()` with a `BackedEnum`', function (): void {
        $stat = Stat::make('Users', 100)
            ->icon(Heroicon::Users);

        expect($stat->getIcon())->toBe(Heroicon::Users);
    });

    it('can clear `icon()` with `null`', function (): void {
        $stat = Stat::make('Users', 100)
            ->icon('heroicon-o-users')
            ->icon(null);

        expect($stat->getIcon())->toBeNull();
    });
});

describe('description', function (): void {
    it('returns `null` for `getDescription()` by default', function (): void {
        $stat = Stat::make('Count', 1);

        expect($stat->getDescription())->toBeNull();
    });

    it('can set `description()`', function (): void {
        $stat = Stat::make('Users', 100)
            ->description('32% increase');

        expect($stat->getDescription())->toBe('32% increase');
    });
});

describe('description icon', function (): void {
    it('returns `null` for `getDescriptionIcon()` by default', function (): void {
        $stat = Stat::make('Count', 1);

        expect($stat->getDescriptionIcon())->toBeNull();
    });

    it('can set `descriptionIcon()`', function (): void {
        $stat = Stat::make('Users', 100)
            ->descriptionIcon('heroicon-m-arrow-trending-up');

        expect($stat->getDescriptionIcon())->toBe('heroicon-m-arrow-trending-up');
    });

    it('defaults `getDescriptionIconPosition()` to `After`', function (): void {
        $stat = Stat::make('Count', 1);

        expect($stat->getDescriptionIconPosition())->toBe(IconPosition::After);
    });

    it('can set description icon position', function (): void {
        $stat = Stat::make('Users', 100)
            ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before);

        expect($stat->getDescriptionIconPosition())->toBe(IconPosition::Before);
    });
});

describe('description color', function (): void {
    it('falls back to `getColor()` for `getDescriptionColor()` by default', function (): void {
        $stat = Stat::make('Count', 1)
            ->color('success');

        expect($stat->getDescriptionColor())->toBe('success');
    });

    it('can set `descriptionColor()` independently', function (): void {
        $stat = Stat::make('Count', 1)
            ->color('success')
            ->descriptionColor('danger');

        expect($stat->getDescriptionColor())->toBe('danger');
    });
});

it('generates a chart data checksum via `generateChartDataChecksum()`', function (): void {
    $stat = Stat::make('Trend', 100)
        ->chart([1.0, 2.0, 3.0]);

    $checksum = $stat->generateChartDataChecksum();

    expect($checksum)->toBeString();
    expect($checksum)->toHaveLength(32); // md5 hash
});
