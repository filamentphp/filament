<?php

use Filament\Support\Colors\Color;
use Filament\Support\Colors\ColorManager;
use Filament\Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $this->manager = new ColorManager;
});

describe('default colors', function (): void {
    it('includes danger, gray, info, primary, success, warning by default', function (): void {
        $colors = $this->manager->getColors();

        expect($colors)->toHaveKey('danger');
        expect($colors)->toHaveKey('gray');
        expect($colors)->toHaveKey('info');
        expect($colors)->toHaveKey('primary');
        expect($colors)->toHaveKey('success');
        expect($colors)->toHaveKey('warning');
    });

    it('uses `Color::Red` for danger by default', function (): void {
        $colors = $this->manager->getColors();

        expect($colors['danger'])->toBe(Color::Red);
    });
});

describe('registering colors', function (): void {
    it('can register a color palette by name', function (): void {
        $this->manager->register([
            'brand' => Color::Indigo,
        ]);

        $colors = $this->manager->getColors();

        expect($colors)->toHaveKey('brand');
        expect($colors['brand'])->toBe(Color::Indigo);
    });

    it('can register a color from a hex string and generate a palette', function (): void {
        $this->manager->register([
            'brand' => '#3b82f6',
        ]);

        $colors = $this->manager->getColors();

        expect($colors)->toHaveKey('brand');
        expect($colors['brand'])->toHaveKey(500);
        expect($colors['brand'][500])->toStartWith('oklch(');
    });

    it('can override default colors', function (): void {
        $this->manager->register([
            'danger' => Color::Orange,
        ]);

        $colors = $this->manager->getColors();

        expect($colors['danger'])->toBe(Color::Orange);
    });

    it('can register colors with a `Closure`', function (): void {
        $this->manager->register(static fn (): array => [
            'brand' => Color::Purple,
        ]);

        $colors = $this->manager->getColors();

        expect($colors)->toHaveKey('brand');
        expect($colors['brand'])->toBe(Color::Purple);
    });

    it('converts non-oklch values in arrays to oklch', function (): void {
        $this->manager->register([
            'custom' => [
                500 => '#ff0000',
            ],
        ]);

        $colors = $this->manager->getColors();

        expect($colors['custom'][500])->toStartWith('oklch(');
    });
});

describe('`getColor()`', function (): void {
    it('returns a color palette by name', function (): void {
        $color = $this->manager->getColor('danger');

        expect($color)->toBe(Color::Red);
    });

    it('returns `null` for non-existent color', function (): void {
        expect($this->manager->getColor('nonexistent'))->toBeNull();
    });
});

describe('shade management', function (): void {
    it('returns `null` for `getOverridingShades()` by default', function (): void {
        expect($this->manager->getOverridingShades('bg'))->toBeNull();
    });

    it('can set `overrideShades()`', function (): void {
        $this->manager->overrideShades('bg', [50, 100, 200]);

        expect($this->manager->getOverridingShades('bg'))->toBe([50, 100, 200]);
    });

    it('returns `null` for `getAddedShades()` by default', function (): void {
        expect($this->manager->getAddedShades('text'))->toBeNull();
    });

    it('can set `addShades()`', function (): void {
        $this->manager->addShades('text', [950]);

        expect($this->manager->getAddedShades('text'))->toBe([950]);
    });

    it('returns `null` for `getRemovedShades()` by default', function (): void {
        expect($this->manager->getRemovedShades('border'))->toBeNull();
    });

    it('can set `removeShades()`', function (): void {
        $this->manager->removeShades('border', [50, 950]);

        expect($this->manager->getRemovedShades('border'))->toBe([50, 950]);
    });
});

describe('caching', function (): void {
    it('caches colors after first `getColors()` call', function (): void {
        $first = $this->manager->getColors();
        $second = $this->manager->getColors();

        expect($first)->toBe($second);
    });
});
