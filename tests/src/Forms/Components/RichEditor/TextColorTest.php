<?php

use Filament\Forms\Components\RichEditor\TextColor;
use Filament\Tests\TestCase;

uses(TestCase::class);

describe('construction', function (): void {
    it('can be constructed with label and color', function (): void {
        $tc = TextColor::make('Red', '#ff0000');

        expect($tc->getLabel())->toBe('Red');
        expect($tc->getColor())->toBe('#ff0000');
    });

    it('can be constructed with label, color, and dark color', function (): void {
        $tc = TextColor::make('Red', '#ff0000', '#cc0000');

        expect($tc->getLabel())->toBe('Red');
        expect($tc->getColor())->toBe('#ff0000');
        expect($tc->getDarkColor())->toBe('#cc0000');
    });
});

describe('`getDarkColor()` fallback logic', function (): void {
    it('returns dark color when explicitly set', function (): void {
        $tc = TextColor::make('Red', '#ff0000', '#cc0000');

        expect($tc->getDarkColor())->toBe('#cc0000');
    });

    it('falls back to `getColor()` when dark color is `null`', function (): void {
        $tc = TextColor::make('Red', '#ff0000');

        expect($tc->getDarkColor())->toBe('#ff0000');
    });

    it('returns `null` when both color and dark color are `null`', function (): void {
        $tc = TextColor::make('Label');

        expect($tc->getDarkColor())->toBeNull();
    });
});

describe('`getSafeLabelHtml()`', function (): void {
    it('returns escaped label', function (): void {
        $tc = TextColor::make('<script>alert("xss")</script>', '#000');

        expect($tc->getSafeLabelHtml())->toBe('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;');
    });

    it('returns plain label unchanged', function (): void {
        $tc = TextColor::make('Red', '#ff0000');

        expect($tc->getSafeLabelHtml())->toBe('Red');
    });
});

describe('`getDefaults()` logic', function (): void {
    it('returns an array of `TextColor` objects', function (): void {
        $defaults = TextColor::getDefaults();

        expect($defaults)->toBeArray();
        expect($defaults)->not->toBeEmpty();

        foreach ($defaults as $name => $color) {
            expect($name)->toBeString();
            expect($color)->toBeInstanceOf(TextColor::class);
        }
    });

    it('returns colors with non-empty labels', function (): void {
        $defaults = TextColor::getDefaults();

        foreach ($defaults as $color) {
            expect($color->getLabel())->toBeString();
            expect($color->getLabel())->not->toBeEmpty();
        }
    });

    it('returns colors with non-null `getColor()` values', function (): void {
        $defaults = TextColor::getDefaults();

        foreach ($defaults as $color) {
            expect($color->getColor())->toBeString();
            expect($color->getColor())->not->toBeEmpty();
        }
    });
});
