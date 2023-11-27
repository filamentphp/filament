<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Field;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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

test('the `in()` rule behaves the same as Laravel\'s', function (?string $input, array | Arrayable | string | Closure $allowed) {
    $filamentFails = [];

    $laravelFails = [];

    $fieldName = Str::random();

    $component = Livewire::make()->data([$fieldName => $input]);

    try {
        ComponentContainer::make($component)
            ->statePath('data')
            ->components([
                $field = (new Field($fieldName))
                    ->in($allowed),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $filamentFails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    try {
        ComponentContainer::make($component)
            ->statePath('data')
            ->components([
                $field = (new Field($fieldName))
                    ->rule(Rule::in($allowed instanceof Closure
                        ? $allowed()
                        : $allowed)),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $laravelFails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    expect($filamentFails)
        ->toBe($laravelFails);
})->with([
    [
        'input' => 'foo',
        'allowed' => ['bar'],
    ],
    [
        'input' => 'foo',
        'allowed' => collect(['bar']),
    ],
    [
        'input' => 'foo',
        'allowed' => 'bar',
    ],
    [
        'input' => 'foo',
        'allowed' => fn () => (fn () => 'bar'),
    ],
    [
        'input' => 'foo',
        'allowed' => [],
    ],
    [
        'input' => 'foo',
        'allowed' => collect([]),
    ],
    [
        'input' => 'foo',
        'allowed' => '',
    ],
    [
        'input' => 'foo',
        'allowed' => fn () => (fn () => null),
    ],
    [
        'input' => null,
        'allowed' => [],
    ],
    [
        'input' => null,
        'allowed' => collect([]),
    ],
    [
        'input' => null,
        'allowed' => '',
    ],
    [
        'input' => null,
        'allowed' => fn () => (fn () => null),
    ],
    [
        'input' => '',
        'allowed' => [],
    ],
    [
        'input' => '',
        'allowed' => collect([]),
    ],
    [
        'input' => '',
        'allowed' => '',
    ],
    [
        'input' => '',
        'allowed' => fn () => (fn () => null),
    ],
]);

test('the `in()` rule can be conditionally validated', function () {
    $fails = [];

    $fieldName = Str::random();

    try {
        ComponentContainer::make(Livewire::make()->data([$fieldName => 'foo']))
            ->statePath('data')
            ->components([
                $field = (new Field($fieldName))
                    ->in(['bar'], false),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $fails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    expect($fails)
        ->toBeEmpty();
});

test('the `notIn()` rule behaves the same as Laravel\'s', function (?string $input, array | Arrayable | string | Closure $allowed) {
    $filamentFails = [];

    $laravelFails = [];

    $fieldName = Str::random();

    $component = Livewire::make()->data([$fieldName => $input]);

    try {
        ComponentContainer::make($component)
            ->statePath('data')
            ->components([
                $field = (new Field($fieldName))
                    ->notIn($allowed),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $filamentFails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    try {
        ComponentContainer::make($component)
            ->statePath('data')
            ->components([
                $field = (new Field($fieldName))
                    ->rule(Rule::notIn($allowed instanceof Closure
                        ? $allowed()
                        : $allowed)),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $laravelFails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    expect($filamentFails)
        ->toBe($laravelFails);
})->with([
    [
        'input' => 'foo',
        'allowed' => ['bar'],
    ],
    [
        'input' => 'foo',
        'allowed' => collect(['bar']),
    ],
    [
        'input' => 'foo',
        'allowed' => 'bar',
    ],
    [
        'input' => 'foo',
        'allowed' => fn () => (fn () => 'bar'),
    ],
    [
        'input' => 'foo',
        'allowed' => [],
    ],
    [
        'input' => 'foo',
        'allowed' => collect([]),
    ],
    [
        'input' => 'foo',
        'allowed' => '',
    ],
    [
        'input' => 'foo',
        'allowed' => fn () => (fn () => null),
    ],
    [
        'input' => null,
        'allowed' => [],
    ],
    [
        'input' => null,
        'allowed' => collect([]),
    ],
    [
        'input' => null,
        'allowed' => '',
    ],
    [
        'input' => null,
        'allowed' => fn () => (fn () => null),
    ],
    [
        'input' => '',
        'allowed' => [],
    ],
    [
        'input' => '',
        'allowed' => collect([]),
    ],
    [
        'input' => '',
        'allowed' => '',
    ],
    [
        'input' => '',
        'allowed' => fn () => (fn () => null),
    ],
]);

test('the `notIn()` rule can be conditionally validated', function () {
    $fails = [];

    $fieldName = Str::random();

    try {
        ComponentContainer::make(Livewire::make()->data([$fieldName => 'foo']))
            ->statePath('data')
            ->components([
                $field = (new Field($fieldName))
                    ->notIn(['bar'], false),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $fails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    expect($fails)
        ->toBeEmpty();
});

test('the `startsWith()` rule behaves the same as Laravel\'s', function (?string $input, array | Arrayable | string | Closure $allowed) {
    $filamentFails = [];

    $laravelFails = [];

    $fieldName = Str::random();

    $component = Livewire::make()->data([$fieldName => $input]);

    try {
        ComponentContainer::make($component)
            ->statePath('data')
            ->components([
                $field = (new Field($fieldName))
                    ->startsWith($allowed),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $filamentFails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    $field = (new Field($fieldName));

    $allowed = $field->evaluate($allowed);

    if ($allowed instanceof Arrayable) {
        $allowed = $allowed->toArray();
    }

    if (is_array($allowed)) {
        $allowed = implode(',', $allowed);
    }

    try {
        ComponentContainer::make($component)
            ->statePath('data')
            ->components([
                $field->rule('starts_with:' . $allowed),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $laravelFails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    expect($filamentFails)
        ->toBe($laravelFails);
})->with([
    [
        'input' => 'foo',
        'allowed' => ['bar'],
    ],
    [
        'input' => 'foo',
        'allowed' => collect(['bar']),
    ],
    [
        'input' => 'foo',
        'allowed' => 'bar',
    ],
    [
        'input' => 'foo',
        'allowed' => fn () => (fn () => 'bar'),
    ],
    [
        'input' => 'foo',
        'allowed' => [],
    ],
    [
        'input' => 'foo',
        'allowed' => collect([]),
    ],
    [
        'input' => 'foo',
        'allowed' => '',
    ],
    [
        'input' => 'foo',
        'allowed' => fn () => (fn () => null),
    ],
    [
        'input' => null,
        'allowed' => [],
    ],
    [
        'input' => null,
        'allowed' => collect([]),
    ],
    [
        'input' => null,
        'allowed' => '',
    ],
    [
        'input' => null,
        'allowed' => fn () => (fn () => null),
    ],
    [
        'input' => '',
        'allowed' => [],
    ],
    [
        'input' => '',
        'allowed' => collect([]),
    ],
    [
        'input' => '',
        'allowed' => '',
    ],
    [
        'input' => '',
        'allowed' => fn () => (fn () => null),
    ],
]);

test('the `startsWith()` rule can be conditionally validated', function () {
    $fails = [];

    $fieldName = Str::random();

    try {
        ComponentContainer::make(Livewire::make()->data([$fieldName => 'foo']))
            ->statePath('data')
            ->components([
                $field = (new Field($fieldName))
                    ->startsWith(['bar'], false),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $fails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    expect($fails)
        ->toBeEmpty();
});

test('the `doesntStartWith()` rule behaves the same as Laravel\'s', function (?string $input, array | Arrayable | string | Closure $allowed) {
    $filamentFails = [];

    $laravelFails = [];

    $fieldName = Str::random();

    $component = Livewire::make()->data([$fieldName => $input]);

    try {
        ComponentContainer::make($component)
            ->statePath('data')
            ->components([
                $field = (new Field($fieldName))
                    ->doesntStartWith($allowed),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $filamentFails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    $field = (new Field($fieldName));

    $allowed = $field->evaluate($allowed);

    if ($allowed instanceof Arrayable) {
        $allowed = $allowed->toArray();
    }

    if (is_array($allowed)) {
        $allowed = implode(',', $allowed);
    }

    try {
        ComponentContainer::make($component)
            ->statePath('data')
            ->components([
                $field->rule('doesnt_start_with:' . $allowed),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $laravelFails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    expect($filamentFails)
        ->toBe($laravelFails);
})->with([
    [
        'input' => 'foo',
        'allowed' => ['bar'],
    ],
    [
        'input' => 'foo',
        'allowed' => collect(['bar']),
    ],
    [
        'input' => 'foo',
        'allowed' => 'bar',
    ],
    [
        'input' => 'foo',
        'allowed' => fn () => (fn () => 'bar'),
    ],
    [
        'input' => 'foo',
        'allowed' => [],
    ],
    [
        'input' => 'foo',
        'allowed' => collect([]),
    ],
    [
        'input' => 'foo',
        'allowed' => '',
    ],
    [
        'input' => 'foo',
        'allowed' => fn () => (fn () => null),
    ],
    [
        'input' => null,
        'allowed' => [],
    ],
    [
        'input' => null,
        'allowed' => collect([]),
    ],
    [
        'input' => null,
        'allowed' => '',
    ],
    [
        'input' => null,
        'allowed' => fn () => (fn () => null),
    ],
    [
        'input' => '',
        'allowed' => [],
    ],
    [
        'input' => '',
        'allowed' => collect([]),
    ],
    [
        'input' => '',
        'allowed' => '',
    ],
    [
        'input' => '',
        'allowed' => fn () => (fn () => null),
    ],
]);

test('the `doesntStartWith()` rule can be conditionally validated', function () {
    $fails = [];

    $fieldName = Str::random();

    try {
        ComponentContainer::make(Livewire::make()->data([$fieldName => 'foo']))
            ->statePath('data')
            ->components([
                $field = (new Field($fieldName))
                    ->doesntStartWith(['bar'], false),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $fails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    expect($fails)
        ->toBeEmpty();
});

test('the `endsWith()` rule behaves the same as Laravel\'s', function (?string $input, array | Arrayable | string | Closure $allowed) {
    $filamentFails = [];

    $laravelFails = [];

    $fieldName = Str::random();

    $component = Livewire::make()->data([$fieldName => $input]);

    try {
        ComponentContainer::make($component)
            ->statePath('data')
            ->components([
                $field = (new Field($fieldName))
                    ->endsWith($allowed),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $filamentFails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    $field = (new Field($fieldName));

    $allowed = $field->evaluate($allowed);

    if ($allowed instanceof Arrayable) {
        $allowed = $allowed->toArray();
    }

    if (is_array($allowed)) {
        $allowed = implode(',', $allowed);
    }

    try {
        ComponentContainer::make($component)
            ->statePath('data')
            ->components([
                $field->rule('ends_with:' . $allowed),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $laravelFails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    expect($filamentFails)
        ->toBe($laravelFails);
})->with([
    [
        'input' => 'foo',
        'allowed' => ['bar'],
    ],
    [
        'input' => 'foo',
        'allowed' => collect(['bar']),
    ],
    [
        'input' => 'foo',
        'allowed' => 'bar',
    ],
    [
        'input' => 'foo',
        'allowed' => fn () => (fn () => 'bar'),
    ],
    [
        'input' => 'foo',
        'allowed' => [],
    ],
    [
        'input' => 'foo',
        'allowed' => collect([]),
    ],
    [
        'input' => 'foo',
        'allowed' => '',
    ],
    [
        'input' => 'foo',
        'allowed' => fn () => (fn () => null),
    ],
    [
        'input' => null,
        'allowed' => [],
    ],
    [
        'input' => null,
        'allowed' => collect([]),
    ],
    [
        'input' => null,
        'allowed' => '',
    ],
    [
        'input' => null,
        'allowed' => fn () => (fn () => null),
    ],
    [
        'input' => '',
        'allowed' => [],
    ],
    [
        'input' => '',
        'allowed' => collect([]),
    ],
    [
        'input' => '',
        'allowed' => '',
    ],
    [
        'input' => '',
        'allowed' => fn () => (fn () => null),
    ],
]);

test('the `endsWith()` rule can be conditionally validated', function () {
    $fails = [];

    $fieldName = Str::random();

    try {
        ComponentContainer::make(Livewire::make()->data([$fieldName => 'foo']))
            ->statePath('data')
            ->components([
                $field = (new Field($fieldName))
                    ->endsWith(['bar'], false),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $fails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    expect($fails)
        ->toBeEmpty();
});

test('the `doesntEndWith()` rule behaves the same as Laravel\'s', function (?string $input, array | Arrayable | string | Closure $allowed) {
    $filamentFails = [];

    $laravelFails = [];

    $fieldName = Str::random();

    $component = Livewire::make()->data([$fieldName => $input]);

    try {
        ComponentContainer::make($component)
            ->statePath('data')
            ->components([
                $field = (new Field($fieldName))
                    ->doesntEndWith($allowed),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $filamentFails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    $field = (new Field($fieldName));

    $allowed = $field->evaluate($allowed);

    if ($allowed instanceof Arrayable) {
        $allowed = $allowed->toArray();
    }

    if (is_array($allowed)) {
        $allowed = implode(',', $allowed);
    }

    try {
        ComponentContainer::make($component)
            ->statePath('data')
            ->components([
                $field->rule('doesnt_end_with:' . $allowed),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $laravelFails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    expect($filamentFails)
        ->toBe($laravelFails);
})->with([
    [
        'input' => 'foo',
        'allowed' => ['bar'],
    ],
    [
        'input' => 'foo',
        'allowed' => collect(['bar']),
    ],
    [
        'input' => 'foo',
        'allowed' => 'bar',
    ],
    [
        'input' => 'foo',
        'allowed' => fn () => (fn () => 'bar'),
    ],
    [
        'input' => 'foo',
        'allowed' => [],
    ],
    [
        'input' => 'foo',
        'allowed' => collect([]),
    ],
    [
        'input' => 'foo',
        'allowed' => '',
    ],
    [
        'input' => 'foo',
        'allowed' => fn () => (fn () => null),
    ],
    [
        'input' => null,
        'allowed' => [],
    ],
    [
        'input' => null,
        'allowed' => collect([]),
    ],
    [
        'input' => null,
        'allowed' => '',
    ],
    [
        'input' => null,
        'allowed' => fn () => (fn () => null),
    ],
    [
        'input' => '',
        'allowed' => [],
    ],
    [
        'input' => '',
        'allowed' => collect([]),
    ],
    [
        'input' => '',
        'allowed' => '',
    ],
    [
        'input' => '',
        'allowed' => fn () => (fn () => null),
    ],
]);

test('the `doesntEndWith()` rule can be conditionally validated', function () {
    $fails = [];

    $fieldName = Str::random();

    try {
        ComponentContainer::make(Livewire::make()->data([$fieldName => 'foo']))
            ->statePath('data')
            ->components([
                $field = (new Field($fieldName))
                    ->doesntEndWith(['bar'], false),
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $fails = array_keys($exception->validator->failed()[$field->getStatePath()]);
    }

    expect($fails)
        ->toBeEmpty();
});
