<?php

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\View\ComponentAttributeBag;

uses(TestCase::class);

// Field is abstract (no view), so we test via TextInput which extends it directly.

describe('construction', function (): void {
    it('can be constructed with a name', function (): void {
        $field = TextInput::make('email');

        expect($field->getName())->toBe('email');
    });

    it('throws `InvalidArgumentException` when name is `null`', function (): void {
        TextInput::make(null);
    })->throws(InvalidArgumentException::class);

    it('sets `statePath` from name on construction', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $field = TextInput::make('full_name'),
            ])
            ->fill();

        expect($field->getStatePath())->toBe('data.full_name');
    });
});

describe('`getLabel()` logic', function (): void {
    it('auto-generates label from kebab name', function (): void {
        $field = TextInput::make('first-name');

        expect($field->getLabel())->toBe('First name');
    });

    it('auto-generates label from underscored name', function (): void {
        $field = TextInput::make('last_name');

        expect($field->getLabel())->toBe('Last name');
    });

    it('auto-generates label from dotted name using last segment', function (): void {
        $field = TextInput::make('address.zip-code');

        expect($field->getLabel())->toBe('Zip code');
    });

    it('uses custom label over auto-generated', function (): void {
        $field = TextInput::make('email')
            ->label('E-mail Address');

        expect($field->getLabel())->toBe('E-mail Address');
    });

    it('uses `Closure` label', function (): void {
        $field = TextInput::make('email')
            ->label(static fn (): string => 'Dynamic Label');

        expect($field->getLabel())->toBe('Dynamic Label');
    });

    it('falls back to `getDefaultLabel()` when custom label is `null`', function (): void {
        $field = TextInput::make('user-email')
            ->label(null);

        expect($field->getLabel())->toBe('User email');
    });
});

describe('autofocus', function (): void {
    it('defaults `isAutofocused()` to `false`', function (): void {
        $field = TextInput::make('name');

        expect($field->isAutofocused())->toBeFalse();
    });

    it('can set `autofocus()`', function (): void {
        $field = TextInput::make('name')->autofocus();

        expect($field->isAutofocused())->toBeTrue();
    });

    it('can set `autofocus()` with a `Closure`', function (): void {
        $field = TextInput::make('name')
            ->autofocus(static fn (): bool => true);

        expect($field->isAutofocused())->toBeTrue();
    });
});

describe('hint', function (): void {
    it('returns `false` for `hasHint()` by default', function (): void {
        $field = TextInput::make('name');

        expect($field->hasHint())->toBeFalse();
    });

    it('can set `hint()`', function (): void {
        $field = TextInput::make('name')
            ->hint('Max 255 characters');

        expect($field->hasHint())->toBeTrue();
        expect($field->getHint())->toBe('Max 255 characters');
    });

    it('can set `hint()` with a `Closure`', function (): void {
        $field = TextInput::make('name')
            ->hint(static fn (): string => 'Dynamic hint');

        expect($field->getHint())->toBe('Dynamic hint');
    });

    it('returns `null` for `getHintColor()` by default', function (): void {
        $field = TextInput::make('name');

        expect($field->getHintColor())->toBeNull();
    });

    it('can set `hintColor()`', function (): void {
        $field = TextInput::make('name')
            ->hintColor('warning');

        expect($field->getHintColor())->toBe('warning');
    });

    it('returns `false` for `hasHintIcon()` by default', function (): void {
        $field = TextInput::make('name');

        expect($field->hasHintIcon())->toBeFalse();
    });

    it('can set `hintIcon()`', function (): void {
        $field = TextInput::make('name')
            ->hintIcon('heroicon-o-information-circle');

        expect($field->hasHintIcon())->toBeTrue();
        expect($field->getHintIcon())->toBe('heroicon-o-information-circle');
    });

    it('can set `hintIcon()` with tooltip', function (): void {
        $field = TextInput::make('name')
            ->hintIcon('heroicon-o-information-circle', tooltip: 'More info');

        expect($field->getHintIconTooltip())->toBe('More info');
    });

    it('can set `hintIconTooltip()` separately', function (): void {
        $field = TextInput::make('name')
            ->hintIcon('heroicon-o-information-circle')
            ->hintIconTooltip('Tooltip text');

        expect($field->getHintIconTooltip())->toBe('Tooltip text');
    });

    it('returns `null` for `getHintIconTooltip()` by default', function (): void {
        $field = TextInput::make('name');

        expect($field->getHintIconTooltip())->toBeNull();
    });
});

describe('enum', function (): void {
    it('returns `null` for `getEnum()` by default', function (): void {
        $field = TextInput::make('status');

        expect($field->getEnum())->toBeNull();
    });

    it('can set `enum()`', function (): void {
        $field = TextInput::make('status')
            ->enum(StringBackedEnum::class);

        expect($field->getEnum())->toBe(StringBackedEnum::class);
    });

    it('can set `enum()` with a `Closure`', function (): void {
        $field = TextInput::make('status')
            ->enum(static fn (): string => StringBackedEnum::class);

        expect($field->getEnum())->toBe(StringBackedEnum::class);
    });

    it('can clear `enum()` with `null`', function (): void {
        $field = TextInput::make('status')
            ->enum(StringBackedEnum::class)
            ->enum(null);

        expect($field->getEnum())->toBeNull();
    });

    it('returns `null` from `getEnumDefaultStateCast()` when no enum set', function (): void {
        $field = TextInput::make('status');

        expect($field->getEnumDefaultStateCast())->toBeNull();
    });

    it('returns `EnumStateCast` from `getEnumDefaultStateCast()` when enum is set', function (): void {
        $field = TextInput::make('status')
            ->enum(StringBackedEnum::class);

        $cast = $field->getEnumDefaultStateCast();

        expect($cast)->toBeInstanceOf(StateCast::class);
    });
});

describe('extra field wrapper attributes', function (): void {
    it('can set `extraFieldWrapperAttributes()`', function (): void {
        $field = TextInput::make('name')
            ->extraFieldWrapperAttributes(['data-test' => 'wrapper']);

        expect($field->getExtraFieldWrapperAttributes())->toBe(['data-test' => 'wrapper']);
    });

    it('can merge `extraFieldWrapperAttributes()`', function (): void {
        $field = TextInput::make('name')
            ->extraFieldWrapperAttributes(['data-a' => '1'])
            ->extraFieldWrapperAttributes(['data-b' => '2'], merge: true);

        $attributes = $field->getExtraFieldWrapperAttributes();

        expect($attributes)->toHaveKey('data-a', '1');
        expect($attributes)->toHaveKey('data-b', '2');
    });

    it('replaces `extraFieldWrapperAttributes()` without merge', function (): void {
        $field = TextInput::make('name')
            ->extraFieldWrapperAttributes(['data-a' => '1'])
            ->extraFieldWrapperAttributes(['data-b' => '2']);

        $attributes = $field->getExtraFieldWrapperAttributes();

        expect($attributes)->not->toHaveKey('data-a');
        expect($attributes)->toHaveKey('data-b', '2');
    });

    it('can set `extraFieldWrapperAttributes()` with a `Closure`', function (): void {
        $field = TextInput::make('name')
            ->extraFieldWrapperAttributes(static fn (): array => ['data-dynamic' => 'yes']);

        expect($field->getExtraFieldWrapperAttributes())->toBe(['data-dynamic' => 'yes']);
    });

    it('returns `ComponentAttributeBag` from `getExtraFieldWrapperAttributesBag()`', function (): void {
        $field = TextInput::make('name')
            ->extraFieldWrapperAttributes(['data-test' => 'bag']);

        $bag = $field->getExtraFieldWrapperAttributesBag();

        expect($bag)->toBeInstanceOf(ComponentAttributeBag::class);
        expect($bag->get('data-test'))->toBe('bag');
    });
});

describe('slot methods return fluent `$this`', function (): void {
    it('returns fluent `$this` from all slot methods', function (): void {
        $field = TextInput::make('name');

        expect($field->aboveLabel([]))->toBe($field);
        expect($field->belowLabel([]))->toBe($field);
        expect($field->beforeLabel([]))->toBe($field);
        expect($field->afterLabel([]))->toBe($field);
        expect($field->aboveContent([]))->toBe($field);
        expect($field->belowContent([]))->toBe($field);
        expect($field->beforeContent([]))->toBe($field);
        expect($field->afterContent([]))->toBe($field);
        expect($field->aboveErrorMessage([]))->toBe($field);
        expect($field->belowErrorMessage([]))->toBe($field);
    });
});

it('returns `false` for `hasNullableBooleanState()`', function (): void {
    $field = TextInput::make('name');

    expect($field->hasNullableBooleanState())->toBeFalse();
});

describe('label visibility', function (): void {
    it('defaults `isLabelHidden()` to `false`', function (): void {
        $field = TextInput::make('name');

        expect($field->isLabelHidden())->toBeFalse();
    });

    it('can set `hiddenLabel()`', function (): void {
        $field = TextInput::make('name')->hiddenLabel();

        expect($field->isLabelHidden())->toBeTrue();
    });

    it('can set `hiddenLabel()` with a `Closure`', function (): void {
        $field = TextInput::make('name')
            ->hiddenLabel(static fn (): bool => true);

        expect($field->isLabelHidden())->toBeTrue();
    });
});
