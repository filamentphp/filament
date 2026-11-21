<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TagsInput;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Validation\ValidationException;

uses(TestCase::class);

it('accepts arrays and optional empty state', function (mixed $state) {
    $form = ComponentContainer::make(Livewire::make()->data(['tags' => $state]))
        ->statePath('data')
        ->components([
            TagsInput::make('tags')->nestedRecursiveRules(['string', 'in:one,two']),
        ]);

    expect($form->validate())->toBe(['data' => ['tags' => $state]]);
})->with([[['one', 'two']], [[]], [null], ['']]);

it('validates each tag with `nestedRecursiveRules()`', function (array $state, string $invalidStatePath) {
    $form = ComponentContainer::make(Livewire::make()->data(['tags' => $state]))
        ->statePath('data')
        ->components([
            TagsInput::make('tags')->nestedRecursiveRules(['in:one,two']),
        ]);

    try {
        $form->validate();
    } catch (ValidationException $exception) {
        expect($exception->validator->failed())->toBe([$invalidStatePath => ['In' => ['one', 'two']]]);

        return;
    }

    $this->fail('Invalid tag passed nested validation.');
})->with([
    [['invalid', 'two'], 'data.tags.0'],
    [['one', 'invalid'], 'data.tags.1'],
]);

it('rejects non-array state', function (mixed $state, ?string $separator) {
    $form = ComponentContainer::make(Livewire::make()->data(['tags' => $state]))
        ->statePath('data')
        ->components([
            TagsInput::make('tags')->separator($separator),
        ]);

    try {
        $form->validate();
    } catch (ValidationException $exception) {
        expect($exception->validator->failed()['data.tags'])->toHaveKey('Array');

        return;
    }

    $this->fail('Non-array tags input state passed validation.');
})->with(['one', '["one"]', 7, true, false])
    ->with([null, ',']);

it('validates hydrated arrays before dehydrating with `separator()`', function (?string $state, string $separator, array $hydratedState, string $dehydratedState) {
    $form = ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            TagsInput::make('tags')
                ->separator($separator)
                ->nestedRecursiveRules(['in:one,two']),
        ])
        ->fill(['tags' => $state]);

    expect($livewire->getData())->toBe(['tags' => $hydratedState])
        ->and($form->validate())->toBe(['data' => ['tags' => $hydratedState]])
        ->and($form->getState())->toBe(['tags' => $dehydratedState]);
})->with([
    ['one,two', ',', ['one', 'two'], 'one,two'],
    ['two | one', ' | ', ['two', 'one'], 'two | one'],
    ['', ',', [], ''],
    [null, ',', [], ''],
]);
