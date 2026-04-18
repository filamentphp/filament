<?php

use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('is a string-backed enum', function (): void {
    expect(Heroicon::Check->value)->toBe('check');
});

it('has outlined icon variants with `o-` prefix', function (): void {
    expect(Heroicon::OutlinedCheck->value)->toBe('o-check');
});

describe('`getIconForSize()`', function (): void {
    it('returns outlined icon format when value starts with `o-`', function (): void {
        $icon = Heroicon::OutlinedCheck;

        expect($icon->getIconForSize(IconSize::Small))->toBe('heroicon-o-check');
        expect($icon->getIconForSize(IconSize::Medium))->toBe('heroicon-o-check');
        expect($icon->getIconForSize(IconSize::Large))->toBe('heroicon-o-check');
    });

    it('returns compact icon for `ExtraSmall` size', function (): void {
        $icon = Heroicon::Check;

        expect($icon->getIconForSize(IconSize::ExtraSmall))->toBe('heroicon-c-check');
    });

    it('returns compact icon for `Small` size', function (): void {
        $icon = Heroicon::Check;

        expect($icon->getIconForSize(IconSize::Small))->toBe('heroicon-c-check');
    });

    it('returns mini icon for `Medium` size', function (): void {
        $icon = Heroicon::Check;

        expect($icon->getIconForSize(IconSize::Medium))->toBe('heroicon-m-check');
    });

    it('returns solid icon for `Large` size', function (): void {
        $icon = Heroicon::Check;

        expect($icon->getIconForSize(IconSize::Large))->toBe('heroicon-s-check');
    });

    it('returns solid icon for `ExtraLarge` size', function (): void {
        $icon = Heroicon::Check;

        expect($icon->getIconForSize(IconSize::ExtraLarge))->toBe('heroicon-s-check');
    });

    it('returns solid icon for `TwoExtraLarge` size', function (): void {
        $icon = Heroicon::Check;

        expect($icon->getIconForSize(IconSize::TwoExtraLarge))->toBe('heroicon-s-check');
    });

    it('ignores size for outlined variants', function (): void {
        $icon = Heroicon::OutlinedStar;

        // All sizes return the same outlined format
        expect($icon->getIconForSize(IconSize::ExtraSmall))->toBe('heroicon-o-star');
        expect($icon->getIconForSize(IconSize::Large))->toBe('heroicon-o-star');
    });
});
