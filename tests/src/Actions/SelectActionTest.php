<?php

use Filament\Actions\SelectAction;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
use Filament\Tests\TestCase;

uses(TestCase::class);

describe('options', function (): void {
    it('returns static array options', function (): void {
        $action = SelectAction::make('test')
            ->options(['a' => 'Option A', 'b' => 'Option B']);

        expect($action->getOptions())->toBe(['a' => 'Option A', 'b' => 'Option B']);
    });

    it('returns options from `Closure`', function (): void {
        $action = SelectAction::make('test')
            ->options(static fn (): array => ['x' => 'X', 'y' => 'Y']);

        expect($action->getOptions())->toBe(['x' => 'X', 'y' => 'Y']);
    });

    it('returns options from enum class string', function (): void {
        $action = SelectAction::make('test')
            ->options(StringBackedEnum::class);

        $options = $action->getOptions();

        expect($options)->toHaveCount(3);
        expect($options)->toHaveKey('one');
        expect($options)->toHaveKey('two');
        expect($options)->toHaveKey('three');
    });

    it('returns options from `Arrayable`', function (): void {
        $action = SelectAction::make('test')
            ->options(collect(['a' => 'Option A', 'b' => 'Option B']));

        expect($action->getOptions())->toBe(['a' => 'Option A', 'b' => 'Option B']);
    });

    it('returns empty array when no options set', function (): void {
        $action = SelectAction::make('test');

        expect($action->getOptions())->toBe([]);
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
