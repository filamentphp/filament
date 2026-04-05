<?php

use Filament\Schemas\Components\Icon;
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
});
