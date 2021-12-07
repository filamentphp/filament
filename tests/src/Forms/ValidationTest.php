<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Field;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

uses(TestCase::class);

test('fields can be required', function () {
    $rules = [];

    try {
        ComponentContainer::make(Livewire::make())
            ->statePath('data')
            ->components([
                $field = (new Field(Str::random()))
                    ->required(),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $rules = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    expect($rules)
        ->toContain('Required');
});

test('fields use custom validation rules', function () {
    $rules = [];

    try {
        ComponentContainer::make(Livewire::make())
            ->statePath('data')
            ->components([
                $field = (new Field(Str::random()))
                    ->rule('email')
                    ->default(Str::random()),
            ])
            ->fill()
            ->validate();
    } catch (ValidationException $exception) {
        $rules = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    expect($rules)
        ->toContain('Email');
});

test('fields can be conditionally validated', function () {
    $rules = [];

    try {
        ComponentContainer::make(Livewire::make())
            ->statePath('data')
            ->components([
                $field = (new Field(Str::random()))
                    ->required($isRequired = rand(0, 1)),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $rules = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    if ($isRequired) {
        expect($rules)
            ->toContain('Required');
    } else {
        expect($rules)
            ->not->toContain('Required');
    }
});

test('first invalid field can be focused', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Field(Str::random())),
            $fieldToFocus = (new Field($firstInvalidFieldName = Str::random())),
            (new Field($secondInvalidFieldName = Str::random())),
        ]);

    expect($container)
        ->getInvalidComponentToFocus([
            "data.{$firstInvalidFieldName}",
            "data.{$secondInvalidFieldName}",
        ])->toBe($fieldToFocus);
});
