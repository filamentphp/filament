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

test('fields can be required if', function () {
    $rules = [];
    $errors = [];

    try {
        ComponentContainer::make(Livewire::make())
            ->statePath('data')
            ->components([
                $field1 = (new Field('one'))
                    ->default('foo'),
                $field2 = (new Field('two'))
                    ->requiredIf('one', 'foo'),
            ])
            ->fill()
            ->validate();
    } catch (ValidationException $exception) {
        $rules = array_keys($exception->validator->failed()[$field2->getStatePath()]);
        $errors = $exception->validator->errors()->get($field2->getStatePath());
    }

    expect($rules)
        ->toContain('RequiredIf');

    expect($errors)
        ->toContain('The two field is required when one is foo.');
});

test('fields can be required unless', function () {
    $rules = [];
    $errors = [];

    try {
        ComponentContainer::make(Livewire::make())
            ->statePath('data')
            ->components([
                $field1 = (new Field('one'))
                    ->default('bar'),
                $field2 = (new Field('two'))
                    ->requiredUnless('one', 'foo'),
            ])
            ->fill()
            ->validate();
    } catch (ValidationException $exception) {
        $rules = array_keys($exception->validator->failed()[$field2->getStatePath()]);
        $errors = $exception->validator->errors()->get($field2->getStatePath());
    }

    expect($rules)
        ->toContain('RequiredUnless');

    expect($errors)
        ->toContain('The two field is required unless one is in foo.');
});
