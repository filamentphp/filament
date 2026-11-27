<?php

use Filament\Support\Colors\Color;
use Filament\Support\Colors\ColorManager;
use Filament\Support\View\Components\BadgeComponent;
use Filament\Support\View\Components\ButtonComponent;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

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
    it('returns the same `ColorManager` instance from `register()` for fluent chaining', function (): void {
        expect($this->manager->register(['brand' => Color::Green]))->toBe($this->manager);
    });

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

describe('`getComponentClasses()`', function (): void {
    it('returns the same result for a class-string and an equivalent instance', function (): void {
        $fromString = $this->manager->getComponentClasses(BadgeComponent::class, 'danger');
        $fromInstance = $this->manager->getComponentClasses(new BadgeComponent, 'danger');

        expect($fromString)->toBe($fromInstance);
        expect($fromString)->toContain('fi-color', 'fi-color-danger');
    });

    it('returns `[]` for a blank color', function (): void {
        expect($this->manager->getComponentClasses(BadgeComponent::class, null))->toBe([]);
        expect($this->manager->getComponentClasses(BadgeComponent::class, ''))->toBe([]);
    });

    it('returns and caches `[]` for the default `gray` of a `HasDefaultGrayColor` component', function (): void {
        // The `gray` short-circuit is now cached, where previously it re-resolved the
        // component on every call. Both calls must return the same empty result.
        expect($this->manager->getComponentClasses(BadgeComponent::class, 'gray'))->toBe([]);
        expect($this->manager->getComponentClasses(BadgeComponent::class, 'gray'))->toBe([]);
    });

    it('returns only the base classes for an unregistered color', function (): void {
        expect($this->manager->getComponentClasses(BadgeComponent::class, 'nonexistent'))
            ->toBe(['fi-color', 'fi-color-nonexistent']);
    });

    it('returns an identical cached result on repeated calls', function (): void {
        $first = $this->manager->getComponentClasses(BadgeComponent::class, 'success');
        $second = $this->manager->getComponentClasses(BadgeComponent::class, 'success');

        expect($second)->toBe($first);
        expect($first)->not->toBe([]);
    });

    it('differentiates stateful components by instance state', function (): void {
        // `ButtonComponent` branches on `$isOutlined` in `getColorMap()`, so outlined and
        // solid buttons must remain distinct cache entries (object path keeps using `serialize()`).
        $outlined = $this->manager->getComponentClasses(new ButtonComponent(isOutlined: true), 'primary');
        $solid = $this->manager->getComponentClasses(new ButtonComponent(isOutlined: false), 'primary');

        expect($outlined)->not->toBe($solid);
    });
});

describe('`getComponentCustomStyles()`', function (): void {
    $palette = [
        50 => 'oklch(0.97 0.02 250)',
        500 => 'oklch(0.62 0.2 250)',
        950 => 'oklch(0.28 0.09 250)',
    ];

    it('returns the same result for a class-string and an equivalent instance', function () use ($palette): void {
        $fromString = $this->manager->getComponentCustomStyles(BadgeComponent::class, $palette);
        $fromInstance = $this->manager->getComponentCustomStyles(new BadgeComponent, $palette);

        expect($fromString)->toBe($fromInstance);
    });

    it('returns an identical cached result on repeated calls', function () use ($palette): void {
        $first = $this->manager->getComponentCustomStyles(BadgeComponent::class, $palette);
        $second = $this->manager->getComponentCustomStyles(BadgeComponent::class, $palette);

        expect($second)->toBe($first);
    });

    it('keeps palette declarations unchanged across HTML and JSON boundaries', function (string $color): void {
        expect(Str::sanitizeCssColor($color))->toBe($color);

        $styles = $this->manager->getComponentCustomStyles(BadgeComponent::class, [500 => $color] + Color::Blue);
        $style = implode('; ', $styles);
        $document = new DOMDocument;
        $document->loadHTML('<div style="' . $style . '"></div>', LIBXML_NOERROR | LIBXML_NOWARNING);

        expect($document->getElementsByTagName('div')->item(0)->getAttribute('style'))->toBe($style)
            ->and(json_decode(json_encode($styles, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR))->toBe($styles)
            ->and($styles)->toContain('--color-50: ' . Color::Blue[50]);
    })->with([
        'decimal entities without semicolons' => 'rgb(0&#41&#59position&#58fixed&#59--tail&#58rgb&#40 0)',
        'hex entities without semicolons' => 'rgb(0&#x29&#x3bposition&#x3a fixed&#x3b--tail&#x3a rgb&#x28 0)',
        'attribute delimiter' => 'rgb(0&#34 data-palette=changed &#34)',
        'comment opener' => 'rgb(0&#41&#59&#47&#42)',
    ]);

    it('preserves compatible custom palette values and variable mappings', function (): void {
        $palette = [
            50 => 'white',
            100 => '#abc',
            200 => '#abcdef80',
            300 => 'rgb(12, 34, 56)',
            400 => 'hsl(120 50% 40% / 75%)',
            500 => 'oklch(0.62 0.2 250)',
            600 => 'color(display-p3 0.1 0.2 0.3)',
        ];

        app()->instance(BadgeComponent::class, new class extends BadgeComponent
        {
            public function getColorMap(array $color): array
            {
                return ['text' => 300, 'dark:text' => 500];
            }
        });

        $styles = $this->manager->getComponentCustomStyles(BadgeComponent::class, $palette);

        expect(array_slice($styles, 0, count($palette)))->toBe([
            '--color-50: white',
            '--color-100: #abc',
            '--color-200: #abcdef80',
            '--color-300: rgb(12, 34, 56)',
            '--color-400: hsl(120 50% 40% / 75%)',
            '--color-500: oklch(0.62 0.2 250)',
            '--color-600: color(display-p3 0.1 0.2 0.3)',
        ])->and($styles)->toContain('--text: var(--color-300)', '--dark-text: var(--color-500)');
    });

    it('differentiates stateful components by instance state', function () use ($palette): void {
        $outlined = $this->manager->getComponentCustomStyles(new ButtonComponent(isOutlined: true), $palette);
        $solid = $this->manager->getComponentCustomStyles(new ButtonComponent(isOutlined: false), $palette);

        expect($outlined)->not->toBe($solid);
    });
});
