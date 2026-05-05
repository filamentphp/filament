<?php

use Filament\Support\Enums\GridDirection;
use Filament\Support\View\ComponentAttributeBag;
use Filament\Support\View\Components\BadgeComponent;
use Filament\Tests\TestCase;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag as BaseComponentAttributeBag;

uses(TestCase::class);

describe('`class()`', function (): void {
    it('sets a class on an empty bag', function (): void {
        $bag = (new ComponentAttributeBag)->class('fi-foo');

        expect($bag->get('class'))->toBe('fi-foo');
    });

    it('appends to an existing class', function (): void {
        $bag = (new ComponentAttributeBag(['class' => 'fi-existing']))->class('fi-foo');

        expect($bag->get('class'))->toBe('fi-existing fi-foo');
    });

    it('accepts an array with conditional keys, dropping `false` values', function (): void {
        $bag = (new ComponentAttributeBag)->class([
            'fi-foo',
            'fi-bar' => true,
            'fi-baz' => false,
        ]);

        expect($bag->get('class'))->toBe('fi-foo fi-bar');
    });

    it('returns the same bag when given an empty class list', function (): void {
        $bag = new ComponentAttributeBag(['class' => 'fi-existing']);
        $result = $bag->class([]);

        expect($result->get('class'))->toBe('fi-existing');
    });

    it('returns a new instance, not mutating the original', function (): void {
        $bag = new ComponentAttributeBag(['class' => 'fi-existing']);
        $result = $bag->class('fi-foo');

        expect($bag->get('class'))->toBe('fi-existing');
        expect($result->get('class'))->toBe('fi-existing fi-foo');
        expect($result)->not->toBe($bag);
    });
});

describe('`style()`', function (): void {
    it('sets a style on an empty bag', function (): void {
        // Laravel's `Arr::toCssStyles()` appends a trailing `;`.
        $bag = (new ComponentAttributeBag)->style('color: red');

        expect($bag->get('style'))->toBe('color: red;');
    });

    it('appends to an existing style with a separating `; `', function (): void {
        $bag = (new ComponentAttributeBag(['style' => 'color: red']))->style('font-weight: bold');

        expect($bag->get('style'))->toBe('color: red; font-weight: bold;');
    });

    it('does not duplicate `;` when existing style already ends with one', function (): void {
        $bag = (new ComponentAttributeBag(['style' => 'color: red;']))->style('font-weight: bold');

        expect($bag->get('style'))->toBe('color: red; font-weight: bold;');
    });

    it('returns the same bag when given an empty style list', function (): void {
        $bag = new ComponentAttributeBag(['style' => 'color: red']);
        $result = $bag->style([]);

        expect($result->get('style'))->toBe('color: red');
    });
});

describe('`merge()`', function (): void {
    it('adds default attributes to an empty bag', function (): void {
        $bag = (new ComponentAttributeBag)->merge(['data-foo' => 'bar']);

        expect($bag->get('data-foo'))->toBe('bar');
    });

    it('lets existing attributes take precedence over defaults', function (): void {
        $bag = (new ComponentAttributeBag(['data-foo' => 'existing']))->merge(['data-foo' => 'default']);

        expect($bag->get('data-foo'))->toBe('existing');
    });

    it('escapes string default values by default', function (): void {
        $bag = (new ComponentAttributeBag)->merge(['data-foo' => '<script>alert(1)</script>']);

        expect($bag->get('data-foo'))->toBe('&lt;script&gt;alert(1)&lt;/script&gt;');
    });

    it('does not escape default values when `escape: false` is passed', function (): void {
        $bag = (new ComponentAttributeBag)->merge(['data-foo' => '<not escaped>'], escape: false);

        expect($bag->get('data-foo'))->toBe('<not escaped>');
    });

    it('concatenates classes from defaults and existing attributes', function (): void {
        $bag = (new ComponentAttributeBag(['class' => 'fi-existing']))->merge(['class' => 'fi-default']);

        expect($bag->get('class'))->toContain('fi-default');
        expect($bag->get('class'))->toContain('fi-existing');
    });

    it('concatenates styles with `; ` separator', function (): void {
        $bag = (new ComponentAttributeBag(['style' => 'color: red']))->merge(['style' => 'font-weight: bold']);

        expect($bag->get('style'))->toContain('color: red');
        expect($bag->get('style'))->toContain('font-weight: bold');
    });

    it('escapes the inner value of `prepends()` (`AppendableAttributeValue`) by default', function (): void {
        // This is the M7 regression: `merge()` falls back to `parent::merge(..., escape: false)`
        // when an `AppendableAttributeValue` is present, which silently disables the inner-value
        // escape that vanilla Laravel performs in `resolveAppendableAttributeDefault()`.
        $bag = new ComponentAttributeBag(['data-foo' => 'existing']);
        $result = $bag->merge([
            'data-foo' => $bag->prepends('<script>alert(1)</script>'),
        ]);

        expect($result->get('data-foo'))
            ->not->toContain('<script>')
            ->toContain('&lt;script&gt;');
    });
});

describe('`color()`', function (): void {
    it('applies color classes for a string color', function (): void {
        $bag = (new ComponentAttributeBag)->color(BadgeComponent::class, 'primary');

        expect($bag->get('class'))->toContain('fi-color-primary');
        expect($bag->get('style'))->toBeNull();
    });

    it('applies an inline custom-color style and `fi-color` class for an array color', function (): void {
        $bag = (new ComponentAttributeBag)->color(BadgeComponent::class, [
            50 => 'oklch(0.97 0 0)',
            500 => 'oklch(0.62 0 0)',
            950 => 'oklch(0.28 0 0)',
        ]);

        expect($bag->get('class'))->toBe('fi-color');
        expect($bag->get('style'))->toContain('--color-50: oklch(0.97 0 0)');
    });

    it('returns the bag unchanged for a null color', function (): void {
        $bag = (new ComponentAttributeBag)->color(BadgeComponent::class, null);

        expect($bag->get('class'))->toBeNull();
        expect($bag->get('style'))->toBeNull();
    });
});

describe('`grid()`', function (): void {
    it('emits a `fi-grid` class with a `--cols-default` row variable for a single int', function (): void {
        $bag = (new ComponentAttributeBag)->grid(3);

        // A bare int is interpreted as the `lg` breakpoint with `default` filled to `1`.
        expect($bag->get('class'))
            ->toContain('fi-grid')
            ->toContain('lg:fi-grid-cols');
        expect($bag->get('style'))
            ->toContain('--cols-default: repeat(1, minmax(0, 1fr))')
            ->toContain('--cols-lg: repeat(3, minmax(0, 1fr))');
    });

    it('emits per-breakpoint `fi-grid-cols` classes when given a breakpoint map', function (): void {
        $bag = (new ComponentAttributeBag)->grid([
            'sm' => 2,
            'md' => 3,
            'lg' => 4,
        ]);

        expect($bag->get('class'))
            ->toContain('sm:fi-grid-cols')
            ->toContain('md:fi-grid-cols')
            ->toContain('lg:fi-grid-cols');
        expect($bag->get('style'))
            ->toContain('--cols-sm: repeat(2, minmax(0, 1fr))')
            ->toContain('--cols-md: repeat(3, minmax(0, 1fr))')
            ->toContain('--cols-lg: repeat(4, minmax(0, 1fr))');
    });

    it('emits the `fi-grid-cols` (no-prefix) class only when `default > 1`', function (): void {
        // The `default` breakpoint is the unprefixed mobile-first value. A grid that defaults
        // to 1 column on mobile (and 3 on `lg`) should NOT emit the bare `fi-grid-cols`.
        $bag = (new ComponentAttributeBag)->grid(['default' => 1, 'lg' => 3]);
        expect($bag->get('class'))->not->toMatch('/(?<![:-])fi-grid-cols/');

        $bag = (new ComponentAttributeBag)->grid(['default' => 2]);
        expect($bag->get('class'))->toMatch('/(?<![:-])fi-grid-cols/');
    });

    it('emits the `fi-grid-direction-col` modifier when given `GridDirection::Column`', function (): void {
        $bag = (new ComponentAttributeBag)->grid(['default' => 4], GridDirection::Column);

        expect($bag->get('class'))->toContain('fi-grid-direction-col');
        // Column-direction CSS variables are bare integers, not `repeat()`.
        expect($bag->get('style'))->toContain('--cols-default: 4');
        expect($bag->get('style'))->not->toContain('repeat(');
    });

    it('substitutes `!` and `@` in breakpoint names so they are valid CSS identifiers', function (): void {
        // The `!` -> `n`, `@` -> `c` substitution lets users write breakpoint queries like
        // `'@md'` (container query) or `'!sm'` (negated) without breaking custom-property names.
        $bag = (new ComponentAttributeBag)->grid([
            '@md' => 2,
            '!sm' => 3,
        ]);

        expect($bag->get('style'))
            ->toContain('--cols-cmd: ')
            ->toContain('--cols-nsm: ')
            ->not->toContain('--cols-@md')
            ->not->toContain('--cols-!sm');
    });
});

describe('`gridColumn()`', function (): void {
    it('emits per-breakpoint span classes and `--col-span-*` styles for an int span', function (): void {
        $bag = (new ComponentAttributeBag)->gridColumn(2);

        expect($bag->get('class'))
            ->toContain('fi-grid-col')
            ->toContain('lg:fi-grid-col-span');
        expect($bag->get('style'))->toContain('--col-span-lg: span 2 / span 2');
    });

    it('renders a `full` span as a `1 / -1` CSS value', function (): void {
        $bag = (new ComponentAttributeBag)->gridColumn('full');

        expect($bag->get('style'))->toContain('--col-span-lg: 1 / -1');
        // Should not also emit the `span N / span N` form.
        expect($bag->get('style'))->not->toContain('span full');
    });

    it('adds `fi-hidden` when `isHidden: true` is passed', function (): void {
        $bag = (new ComponentAttributeBag)->gridColumn(2, isHidden: true);

        expect($bag->get('class'))->toContain('fi-hidden');
    });

    it('adds `fi-hidden` when the default-breakpoint span is the literal `"hidden"`', function (): void {
        $bag = (new ComponentAttributeBag)->gridColumn(['default' => 'hidden']);

        expect($bag->get('class'))->toContain('fi-hidden');
    });

    it('emits `--col-start-*` and `--col-order-*` for `start` and `order` breakpoint maps', function (): void {
        $bag = (new ComponentAttributeBag)->gridColumn(
            span: ['lg' => 4],
            start: ['md' => 2],
            order: ['sm' => 1],
        );

        expect($bag->get('class'))
            ->toContain('md:fi-grid-col-start')
            ->toContain('sm:fi-grid-col-order');
        expect($bag->get('style'))
            ->toContain('--col-start-md: 2')
            ->toContain('--col-order-sm: 1');
    });
});

describe('macros via `__call` / `hasMacro`', function (): void {
    it('routes a macro registered on Laravel\'s base bag through the Filament subclass', function (): void {
        // The override exists so user-registered macros on the Laravel base class still resolve
        // when called on the Filament subclass — this is the fallback path in `ComponentAttributeBag::__call`.
        try {
            BaseComponentAttributeBag::macro('filamentTestMacro', function (string $value): string {
                return "macro-said:{$value}";
            });

            expect(ComponentAttributeBag::hasMacro('filamentTestMacro'))->toBeTrue();
            expect((new ComponentAttributeBag)->filamentTestMacro('hi'))->toBe('macro-said:hi');
        } finally {
            // Reset the static `$macros` array on the base class to avoid bleeding into other tests.
            (function (): void {
                unset(static::$macros['filamentTestMacro']);
            })->bindTo(null, BaseComponentAttributeBag::class)();
        }
    });

    it('binds `$this` inside a Closure macro to the Filament subclass instance', function (): void {
        try {
            BaseComponentAttributeBag::macro('filamentClassNameMacro', function (): string {
                return static::class;
            });

            expect((new ComponentAttributeBag)->filamentClassNameMacro())->toBe(ComponentAttributeBag::class);
        } finally {
            (function (): void {
                unset(static::$macros['filamentClassNameMacro']);
            })->bindTo(null, BaseComponentAttributeBag::class)();
        }
    });
});

describe('`merge()` AppendableAttributeValue interactions', function (): void {
    it('does not escape the inner value of `prepends()` when `escape: false` is passed', function (): void {
        // Inverse of the M7 regression test: with `escape: false`, the inner appendable value
        // must be passed through verbatim (the merge path falls back to parent with `escape: false`).
        $bag = new ComponentAttributeBag(['data-foo' => 'existing']);
        $result = $bag->merge([
            'data-foo' => $bag->prepends('<not escaped>'),
        ], escape: false);

        expect($result->get('data-foo'))
            ->toContain('<not escaped>')
            ->not->toContain('&lt;');
    });

    it('escapes inner values across multiple AppendableAttributeValue keys', function (): void {
        $bag = new ComponentAttributeBag(['data-foo' => 'a', 'data-bar' => 'b']);
        $result = $bag->merge([
            'data-foo' => $bag->prepends('<x>'),
            'data-bar' => $bag->prepends('<y>'),
        ]);

        expect($result->get('data-foo'))->toContain('&lt;x&gt;')->not->toContain('<x>');
        expect($result->get('data-bar'))->toContain('&lt;y&gt;')->not->toContain('<y>');
    });
});

describe('`merge()` non-class/style attributes', function (): void {
    it('preserves a `false` boolean default value through to `__toString()` (drops the attribute)', function (): void {
        // Inline-rendered components emit `'wire:target' => false` to skip the directive entirely
        // (e.g. CanGenerateBadgeHtml.php). `__toString()` must omit attributes whose final value
        // is `false`. This is a hot-path used on every action-button render.
        $bag = (new ComponentAttributeBag)->merge([
            'wire:target' => false,
            'data-keep' => 'yes',
        ], escape: false);

        $html = (string) $bag;

        expect($html)
            ->toContain('data-keep="yes"')
            ->not->toContain('wire:target');
    });
});

describe('`class()` array semantics', function (): void {
    it('drops `null`-valued conditional class entries', function (): void {
        // `class()` builds on `Arr::toCssClasses()` which drops both `false` and `null` values.
        // The existing test only covers `false`; this pins the `null` branch too.
        $bag = (new ComponentAttributeBag)->class([
            'fi-keep',
            'fi-conditional' => null,
            'fi-also-keep' => true,
        ]);

        expect($bag->get('class'))
            ->toContain('fi-keep')
            ->toContain('fi-also-keep')
            ->not->toContain('fi-conditional');
    });

    it('returns the same bag instance when given an `HtmlString` of empty content', function (): void {
        // Defensive: passing an `HtmlString` whose value is empty should not mutate the bag.
        $bag = new ComponentAttributeBag(['class' => 'fi-existing']);
        $result = $bag->class(new HtmlString(''));

        // Empty class list short-circuits to the same instance.
        expect($result->get('class'))->toBe('fi-existing');
    });
});
