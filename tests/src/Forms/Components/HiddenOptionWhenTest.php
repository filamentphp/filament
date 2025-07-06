<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Validation\Rules\In;

uses(TestCase::class);

it('can hide options in Radio component using boolean callback', function () {
    $component = Radio::make('status')
        ->options([
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
        ])
        ->hiddenOptionWhen(fn (string $value): bool => $value === 'archived')
        ->container(ComponentContainer::make(Livewire::make()));

    $visibleOptions = $component->getVisibleOptions();

    expect($visibleOptions)
        ->toHaveCount(2)
        ->toHaveKey('draft')
        ->toHaveKey('published')
        ->not->toHaveKey('archived');
});

it('can hide options in Radio component using closure with value and label', function () {
    $component = Radio::make('status')
        ->options([
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
        ])
        ->hiddenOptionWhen(fn (string $value, string $label): bool => $label === 'Archived')
        ->container(ComponentContainer::make(Livewire::make()));

    $visibleOptions = $component->getVisibleOptions();

    expect($visibleOptions)
        ->toHaveCount(2)
        ->toHaveKey('draft')
        ->toHaveKey('published')
        ->not->toHaveKey('archived');
});

it('can hide multiple options in Radio component using merge', function () {
    $component = Radio::make('status')
        ->options([
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
            'deleted' => 'Deleted',
        ])
        ->hiddenOptionWhen(fn (string $value): bool => $value === 'archived')
        ->hiddenOptionWhen(fn (string $value): bool => $value === 'deleted', merge: true)
        ->container(ComponentContainer::make(Livewire::make()));

    $visibleOptions = $component->getVisibleOptions();

    expect($visibleOptions)
        ->toHaveCount(2)
        ->toHaveKey('draft')
        ->toHaveKey('published')
        ->not->toHaveKey('archived')
        ->not->toHaveKey('deleted');
});

it('correctly identifies when option is hidden in Radio component', function () {
    $component = Radio::make('status')
        ->options([
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
        ])
        ->hiddenOptionWhen(fn (string $value): bool => $value === 'archived')
        ->container(ComponentContainer::make(Livewire::make()));

    expect($component->isOptionHidden('draft', 'Draft'))->toBeFalse();
    expect($component->isOptionHidden('published', 'Published'))->toBeFalse();
    expect($component->isOptionHidden('archived', 'Archived'))->toBeTrue();
});

it('can detect dynamic hidden options in Radio component', function () {
    $component = Radio::make('status')
        ->options([
            'draft' => 'Draft',
            'published' => 'Published',
        ])
        ->hiddenOptionWhen(fn (string $value): bool => $value === 'draft')
        ->container(ComponentContainer::make(Livewire::make()));

    expect($component->hasDynamicHiddenOptions())->toBeTrue();
});

it('can hide options in CheckboxList component', function () {
    $component = CheckboxList::make('tags')
        ->options([
            'red' => 'Red',
            'green' => 'Green',
            'blue' => 'Blue',
            'yellow' => 'Yellow',
        ])
        ->hiddenOptionWhen(fn (string $value): bool => in_array($value, ['blue', 'yellow']))
        ->container(ComponentContainer::make(Livewire::make()));

    $visibleOptions = $component->getVisibleOptions();

    expect($visibleOptions)
        ->toHaveCount(2)
        ->toHaveKey('red')
        ->toHaveKey('green')
        ->not->toHaveKey('blue')
        ->not->toHaveKey('yellow');
});

it('can hide options in ToggleButtons component', function () {
    $component = ToggleButtons::make('choice')
        ->options([
            'option1' => 'Option 1',
            'option2' => 'Option 2',
            'option3' => 'Option 3',
        ])
        ->hiddenOptionWhen(fn (string $value): bool => $value === 'option2')
        ->container(ComponentContainer::make(Livewire::make()));

    $visibleOptions = $component->getVisibleOptions();

    expect($visibleOptions)
        ->toHaveCount(2)
        ->toHaveKey('option1')
        ->toHaveKey('option3')
        ->not->toHaveKey('option2');
});

it('can hide options in Select component', function () {
    $component = Select::make('country')
        ->options([
            'us' => 'United States',
            'ca' => 'Canada',
            'mx' => 'Mexico',
            'uk' => 'United Kingdom',
        ])
        ->hiddenOptionWhen(fn (string $value): bool => in_array($value, ['mx', 'uk']))
        ->container(ComponentContainer::make(Livewire::make()));

    $visibleOptions = $component->getVisibleOptions();

    expect($visibleOptions)
        ->toHaveCount(2)
        ->toHaveKey('us')
        ->toHaveKey('ca')
        ->not->toHaveKey('mx')
        ->not->toHaveKey('uk');
});

it('filters hidden options in Select component JavaScript transformation', function () {
    $component = Select::make('country')
        ->options([
            'us' => 'United States',
            'ca' => 'Canada',
            'mx' => 'Mexico',
        ])
        ->hiddenOptionWhen(fn (string $value): bool => $value === 'mx')
        ->container(ComponentContainer::make(Livewire::make()));

    $jsOptions = $component->getOptionsForJs();

    expect($jsOptions)
        ->toHaveCount(2)
        ->each(fn ($option) => $option->toHaveKeys(['label', 'value', 'disabled']));

    $values = collect($jsOptions)->pluck('value')->all();
    expect($values)
        ->toContain('us', 'ca')
        ->not->toContain('mx');
});

it('considers dynamic hidden options in Select component hasDynamicOptions', function () {
    $component = Select::make('status')
        ->options([
            'draft' => 'Draft',
            'published' => 'Published',
        ])
        ->hiddenOptionWhen(fn (string $value): bool => $value === 'draft')
        ->container(ComponentContainer::make(Livewire::make()));

    expect($component->hasDynamicOptions())->toBeTrue();
});

it('can hide options in SelectColumn component', function () {
    $column = SelectColumn::make('status')
        ->options([
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
        ])
        ->hiddenOptionWhen(fn (string $value): bool => $value === 'suspended');

    $visibleOptions = $column->getVisibleOptions();

    expect($visibleOptions)
        ->toHaveCount(2)
        ->toHaveKey('active')
        ->toHaveKey('inactive')
        ->not->toHaveKey('suspended');
});

it('uses visible options for validation rules in SelectColumn', function () {
    $column = SelectColumn::make('status')
        ->options([
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
        ])
        ->hiddenOptionWhen(fn (string $value): bool => $value === 'suspended');

    $rules = $column->getRules();

    // Find the validation rule that contains allowed values
    $validationRule = collect($rules)
        ->first(fn ($rule) => $rule instanceof In);

    expect($validationRule)->not->toBeNull();

    // The In rule should only allow visible options
    $reflection = new ReflectionClass($validationRule);
    $valuesProperty = $reflection->getProperty('values');
    $valuesProperty->setAccessible(true);
    $allowedValues = $valuesProperty->getValue($validationRule);

    expect($allowedValues)
        ->toContain('active', 'inactive')
        ->not->toContain('suspended');
});

it('handles boolean hidden option callbacks correctly', function () {
    $component = Radio::make('test')
        ->options(['a' => 'A', 'b' => 'B'])
        ->hiddenOptionWhen(true)
        ->container(ComponentContainer::make(Livewire::make()));

    expect($component->getVisibleOptions())->toBeEmpty();
});

it('handles false hidden option callbacks correctly', function () {
    $component = Radio::make('test')
        ->options(['a' => 'A', 'b' => 'B'])
        ->hiddenOptionWhen(false)
        ->container(ComponentContainer::make(Livewire::make()));

    expect($component->getVisibleOptions())
        ->toHaveCount(2)
        ->toHaveKey('a')
        ->toHaveKey('b');
});

it('handles grouped options correctly when some groups are hidden', function () {
    $component = Select::make('choice')
        ->options([
            'Group 1' => [
                'a' => 'Option A',
                'b' => 'Option B',
            ],
            'Group 2' => [
                'c' => 'Option C',
                'd' => 'Option D',
            ],
        ])
        ->hiddenOptionWhen(fn (string $value): bool => in_array($value, ['a', 'c']))
        ->container(ComponentContainer::make(Livewire::make()));

    $visibleOptions = $component->getVisibleOptions();

    expect($visibleOptions)
        ->toHaveCount(2)
        ->toHaveKey('b')
        ->toHaveKey('d')
        ->not->toHaveKey('a')
        ->not->toHaveKey('c');
});

it('preserves disabled option functionality when using hidden options', function () {
    $component = Radio::make('status')
        ->options([
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
        ])
        ->disableOptionWhen(fn (string $value): bool => $value === 'published')
        ->hiddenOptionWhen(fn (string $value): bool => $value === 'archived')
        ->container(ComponentContainer::make(Livewire::make()));

    $visibleOptions = $component->getVisibleOptions();

    expect($visibleOptions)
        ->toHaveCount(2)
        ->toHaveKey('draft')
        ->toHaveKey('published')
        ->not->toHaveKey('archived');

    expect($component->isOptionDisabled('published', 'Published'))->toBeTrue();
    expect($component->isOptionDisabled('draft', 'Draft'))->toBeFalse();
});
