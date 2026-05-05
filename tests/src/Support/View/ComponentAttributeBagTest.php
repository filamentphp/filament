<?php

use Filament\Support\View\ComponentAttributeBag;
use Filament\Support\View\Components\BadgeComponent;
use Filament\Tests\TestCase;

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
