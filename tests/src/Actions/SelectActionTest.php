<?php

use Filament\Actions\SelectAction;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
use Filament\Tests\TestCase;

uses(TestCase::class);

describe('options', function (): void {
    it('returns an empty array from `getOptions()` by default', function (): void {
        $action = SelectAction::make('test');

        expect($action->getOptions())->toBe([]);
    });

    it('can set `options()` with an array', function (): void {
        $action = SelectAction::make('test')
            ->options(['a' => 'Option A', 'b' => 'Option B']);

        expect($action->getOptions())->toBe(['a' => 'Option A', 'b' => 'Option B']);
    });

    it('can set `options()` with a `Closure`', function (): void {
        $action = SelectAction::make('test')
            ->options(static fn (): array => ['x' => 'X', 'y' => 'Y']);

        expect($action->getOptions())->toBe(['x' => 'X', 'y' => 'Y']);
    });

    it('can set `options()` with an enum class string', function (): void {
        $action = SelectAction::make('test')
            ->options(StringBackedEnum::class);

        $options = $action->getOptions();

        expect($options)->toHaveCount(3);
        expect($options)->toHaveKey('one');
        expect($options)->toHaveKey('two');
        expect($options)->toHaveKey('three');
    });

    it('can set `options()` with an `Arrayable`', function (): void {
        $action = SelectAction::make('test')
            ->options(collect(['a' => 'Option A', 'b' => 'Option B']));

        expect($action->getOptions())->toBe(['a' => 'Option A', 'b' => 'Option B']);
    });
});

describe('placeholder', function (): void {
    it('returns `null` for `getPlaceholder()` by default', function (): void {
        $action = SelectAction::make('test');

        expect($action->getPlaceholder())->toBeNull();
    });

    it('can set `placeholder()`', function (): void {
        $action = SelectAction::make('test')
            ->placeholder('Select...');

        expect($action->getPlaceholder())->toBe('Select...');
    });
});
