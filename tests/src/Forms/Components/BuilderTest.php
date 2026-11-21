<?php

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Validation\ValidationException;

uses(TestCase::class);

it('validates unsafe builder item keys and removes disabled values', function (string $itemKey): void {
    Form::make(Livewire::make()->data(['items' => [
        'safe' => ['type' => 'link', 'data' => ['url' => 'https://filamentphp.com', 'is_verified' => true]],
        $itemKey => ['type' => 'link', 'data' => ['url' => 'javascript:alert(1)', 'is_verified' => true]],
    ]]))->statePath('data')->schema([
        $builder = Builder::make('items')->blocks([
            Builder\Block::make('link')->schema([
                TextInput::make('url')->url(),
                Toggle::make('is_verified')->disabled(),
            ]),
        ]),
    ])->getComponents();
    $form = $builder->getContainer();

    expect(fn () => $form->getState())->toThrow(ValidationException::class);

    $state = $builder->getState();
    $state[array_key_last($state)]['data']['url'] = 'https://example.com';
    $builder->state($state);

    expect($form->getState())->toBe(['items' => [
        ['type' => 'link', 'data' => ['url' => 'https://filamentphp.com']],
        ['type' => 'link', 'data' => ['url' => 'https://example.com']],
    ]]);
})->with(['a.b', '', '*', '\\*', '{first}', '\\{last}', 'part\\', 'a"']);

it('adds a builder block with defaults when `generateUuidUsing()` returns an unsafe key', function (): void {
    Form::make(Livewire::make()->data(['items' => []]))->statePath('data')->schema([
        $builder = Builder::make('items')->generateUuidUsing(static fn (): string => 'unsafe.key')->blocks([
            Builder\Block::make('link')->schema([TextInput::make('name')->default('Added block')]),
        ]),
    ])->getComponents();

    $builder->getAddAction()->component($builder)->call(['arguments' => ['block' => 'link']]);

    expect(array_values($builder->getState()))->toBe([
        ['type' => 'link', 'data' => ['name' => 'Added block']],
    ]);
});

it('does not overwrite future numeric builder keys with UUID generation disabled', function (): void {
    Form::make(Livewire::make()->data(['items' => [
        'a.b' => ['type' => 'link', 'data' => ['name' => 'First']],
        0 => ['type' => 'link', 'data' => ['name' => 'Second']],
        1 => ['type' => 'link', 'data' => ['name' => 'Third']],
    ]]))->statePath('data')->schema([
        $builder = Builder::make('items')->generateUuidUsing(false)->blocks([
            Builder\Block::make('link')->schema([TextInput::make('name')]),
        ]),
    ])->getComponents();

    $builder->getChildComponentContainers();

    expect(array_column(array_column($builder->getState(), 'data'), 'name'))->toBe(['First', 'Second', 'Third']);
});
