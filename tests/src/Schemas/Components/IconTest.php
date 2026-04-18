<?php

use Filament\Schemas\Components\Icon;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be constructed with a string icon', function (): void {
    $icon = Icon::make('heroicon-o-check');

    expect($icon->getIcon())->toBe('heroicon-o-check');
});

it('can be constructed with a `BackedEnum` icon', function (): void {
    $icon = Icon::make(Heroicon::Check);

    expect($icon->getIcon())->toBe(Heroicon::Check);
});

it('can set `icon()` with a `Closure`', function (): void {
    $icon = Icon::make('initial')
        ->icon(static fn (): string => 'heroicon-o-star');

    expect($icon->getIcon())->toBe('heroicon-o-star');
});

describe('color', function (): void {
    it('returns `null` for `getColor()` by default', function (): void {
        $icon = Icon::make(Heroicon::Check);

        expect($icon->getColor())->toBeNull();
    });

    it('can set `color()`', function (): void {
        $icon = Icon::make(Heroicon::Check)->color('success');

        expect($icon->getColor())->toBe('success');
    });

    it('can set `color()` with a `Closure`', function (): void {
        $icon = Icon::make(Heroicon::Check)
            ->color(static fn (): string => 'danger');

        expect($icon->getColor())->toBe('danger');
    });
});

describe('size', function (): void {
    it('returns `null` for `getSize()` by default', function (): void {
        $icon = Icon::make(Heroicon::Check);

        expect($icon->getSize())->toBeNull();
    });

    it('can set `size()`', function (): void {
        $icon = Icon::make(Heroicon::Check)->size(IconSize::Large);

        expect($icon->getSize())->toBe(IconSize::Large);
    });

    it('can set `size()` with a `Closure`', function (): void {
        $icon = Icon::make(Heroicon::Check)
            ->size(static fn (): IconSize => IconSize::Large);

        expect($icon->getSize())->toBe(IconSize::Large);
    });

    it('resolves a string `size()` to the matching `IconSize`', function (): void {
        $icon = Icon::make(Heroicon::Check)->size('lg');

        expect($icon->getSize())->toBe(IconSize::Large);
    });

    it('returns `null` for `getSize()` when `size()` is `"base"`', function (): void {
        $icon = Icon::make(Heroicon::Check)->size('base');

        expect($icon->getSize())->toBeNull();
    });
});

describe('tooltip', function (): void {
    it('returns `null` for `getTooltip()` by default', function (): void {
        $icon = Icon::make(Heroicon::Check);

        expect($icon->getTooltip())->toBeNull();
    });

    it('can set `tooltip()`', function (): void {
        $icon = Icon::make(Heroicon::Check)->tooltip('Verified');

        expect($icon->getTooltip())->toBe('Verified');
    });

    it('can set `tooltip()` with a `Closure`', function (): void {
        $icon = Icon::make(Heroicon::Check)
            ->tooltip(static fn (): string => 'Dynamic tip');

        expect($icon->getTooltip())->toBe('Dynamic tip');
    });
});

describe('rendering', function (): void {
    it('renders with a string icon via `toEmbeddedHtml()`', function (): void {
        $html = Icon::make('heroicon-o-check')->toEmbeddedHtml();
        expect($html)->not->toBe('');
    });

    it('renders with `icon()` set via `Closure`', function (): void {
        $html = Icon::make('initial')
            ->icon(static fn (): string => 'heroicon-o-star')
            ->toEmbeddedHtml();
        expect($html)->not->toBe('');
    });

    it('renders with `color()`', function (): void {
        $html = Icon::make(Heroicon::Check)->color('success')->toEmbeddedHtml();
        expect($html)->not->toBe('');
    });

    it('renders with `color()` set via `Closure`', function (): void {
        $html = Icon::make(Heroicon::Check)->color(static fn (): string => 'danger')->toEmbeddedHtml();
        expect($html)->not->toBe('');
    });

    it('renders with `tooltip()`', function (): void {
        $html = Icon::make(Heroicon::Check)->tooltip('Verified')->toEmbeddedHtml();
        expect($html)->not->toBe('');
    });

    it('renders with `tooltip()` set via `Closure`', function (): void {
        $html = Icon::make(Heroicon::Check)->tooltip(static fn (): string => 'Dynamic tip')->toEmbeddedHtml();
        expect($html)->not->toBe('');
    });

    it('renders with `size()`', function (): void {
        $html = Icon::make(Heroicon::Check)->size(IconSize::Large)->toEmbeddedHtml();
        expect($html)->toContain('fi-size-lg');
    });

    it('renders with `size()` set via `Closure`', function (): void {
        $html = Icon::make(Heroicon::Check)->size(static fn (): IconSize => IconSize::Small)->toEmbeddedHtml();
        expect($html)->toContain('fi-size-sm');
    });

    it('renders with default `IconSize::Medium` when no size is set', function (): void {
        $html = Icon::make(Heroicon::Check)->toEmbeddedHtml();
        expect($html)->toContain('fi-size-md');
    });
});
