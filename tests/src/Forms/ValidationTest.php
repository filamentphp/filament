<?php

use Filament\Forms\Components\Field;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

uses(TestCase::class);

test('fields can be required', function (): void {
    $rules = [];

    try {
        Schema::make(Livewire::make())
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

test('fields use custom validation rules', function (): void {
    $rules = [];

    try {
        Schema::make(Livewire::make())
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

test('fields can be conditionally validated', function (): void {
    $rules = [];

    try {
        Schema::make(Livewire::make())
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

test('fields are validated if they are not dehydrated', function (): void {
    $rules = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Field(Str::random()))
                ->required()
                ->dehydrated(false),
        ])
        ->getValidationRules();

    expect($rules)
        ->not->toBeEmpty();
});

test('fields are not validated if they are not dehydrated and configured as such', function (): void {
    $rules = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Field(Str::random()))
                ->required()
                ->dehydrated(false)
                ->validatedWhenNotDehydrated(false),
        ])
        ->getValidationRules();

    expect($rules)
        ->toBeEmpty();

    $rules = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Field(Str::random()))
                ->required()
                ->dehydrated()
                ->validatedWhenNotDehydrated(false),
        ])
        ->getValidationRules();

    expect($rules)
        ->not->toBeEmpty();
});

test('fields can be required if', function (): void {
    $rules = [];
    $errors = [];

    try {
        Schema::make(Livewire::make())
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

test('fields can be required unless', function (): void {
    $rules = [];
    $errors = [];

    try {
        Schema::make(Livewire::make())
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

test('the `in()` rule behaves the same as Laravel\'s', function (?string $input, array | Arrayable | string | Closure $allowed): void {
    $filamentFails = [];

    $laravelFails = [];

    $fieldName = Str::random();

    $component = Livewire::make()->data([$fieldName => $input]);

    try {
        Schema::make($component)
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
        Schema::make($component)
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
    fn () => [
        'input' => 'foo',
        'allowed' => fn () => 'bar',
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
    fn () => [
        'input' => null,
        'allowed' => fn () => null,
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
    fn () => [
        'input' => '',
        'allowed' => fn () => null,
    ],
]);

test('the `in()` rule can be conditionally validated', function (): void {
    $fails = [];

    $fieldName = Str::random();

    try {
        Schema::make(Livewire::make()->data([$fieldName => 'foo']))
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

test('the `notIn()` rule behaves the same as Laravel\'s', function (?string $input, array | Arrayable | string | Closure $allowed): void {
    $filamentFails = [];

    $laravelFails = [];

    $fieldName = Str::random();

    $component = Livewire::make()->data([$fieldName => $input]);

    try {
        Schema::make($component)
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
        Schema::make($component)
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
    fn () => [
        'input' => 'foo',
        'allowed' => fn () => 'bar',
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
    fn () => [
        'input' => 'foo',
        'allowed' => fn () => null,
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
    fn () => [
        'input' => null,
        'allowed' => fn () => null,
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
    fn () => [
        'input' => '',
        'allowed' => fn () => null,
    ],
]);

test('the `notIn()` rule can be conditionally validated', function (): void {
    $fails = [];

    $fieldName = Str::random();

    try {
        Schema::make(Livewire::make()->data([$fieldName => 'foo']))
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

test('the `startsWith()` rule behaves the same as Laravel\'s', function (?string $input, array | Arrayable | string | Closure $allowed): void {
    $filamentFails = [];

    $laravelFails = [];

    $fieldName = Str::random();

    $component = Livewire::make()->data([$fieldName => $input]);

    try {
        Schema::make($component)
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
        Schema::make($component)
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
    fn () => [
        'input' => 'foo',
        'allowed' => fn () => 'bar',
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
    fn () => [
        'input' => 'foo',
        'allowed' => fn () => null,
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
    fn () => [
        'input' => null,
        'allowed' => fn () => null,
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
    fn () => [
        'input' => '',
        'allowed' => fn () => null,
    ],
]);

test('the `startsWith()` rule can be conditionally validated', function (): void {
    $fails = [];

    $fieldName = Str::random();

    try {
        Schema::make(Livewire::make()->data([$fieldName => 'foo']))
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

test('the `doesntStartWith()` rule behaves the same as Laravel\'s', function (?string $input, array | Arrayable | string | Closure $allowed): void {
    $filamentFails = [];

    $laravelFails = [];

    $fieldName = Str::random();

    $component = Livewire::make()->data([$fieldName => $input]);

    try {
        Schema::make($component)
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
        Schema::make($component)
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
    fn () => [
        'input' => 'foo',
        'allowed' => fn () => 'bar',
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
    fn () => [
        'input' => 'foo',
        'allowed' => fn () => null,
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
    fn () => [
        'input' => null,
        'allowed' => fn () => null,
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
    fn () => [
        'input' => '',
        'allowed' => fn () => null,
    ],
]);

test('the `doesntStartWith()` rule can be conditionally validated', function (): void {
    $fails = [];

    $fieldName = Str::random();

    try {
        Schema::make(Livewire::make()->data([$fieldName => 'foo']))
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

test('the `endsWith()` rule behaves the same as Laravel\'s', function (?string $input, array | Arrayable | string | Closure $allowed): void {
    $filamentFails = [];

    $laravelFails = [];

    $fieldName = Str::random();

    $component = Livewire::make()->data([$fieldName => $input]);

    try {
        Schema::make($component)
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
        Schema::make($component)
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
    fn () => [
        'input' => 'foo',
        'allowed' => fn () => 'bar',
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
    fn () => [
        'input' => 'foo',
        'allowed' => fn () => null,
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
    fn () => [
        'input' => null,
        'allowed' => fn () => null,
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
    fn () => [
        'input' => '',
        'allowed' => fn () => null,
    ],
]);

test('the `endsWith()` rule can be conditionally validated', function (): void {
    $fails = [];

    $fieldName = Str::random();

    try {
        Schema::make(Livewire::make()->data([$fieldName => 'foo']))
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

test('the `doesntEndWith()` rule behaves the same as Laravel\'s', function (?string $input, array | Arrayable | string | Closure $allowed): void {
    $filamentFails = [];

    $laravelFails = [];

    $fieldName = Str::random();

    $component = Livewire::make()->data([$fieldName => $input]);

    try {
        Schema::make($component)
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
        Schema::make($component)
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
    fn () => [
        'input' => 'foo',
        'allowed' => fn () => 'bar',
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
    fn () => [
        'input' => 'foo',
        'allowed' => fn () => null,
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
    fn () => [
        'input' => null,
        'allowed' => fn () => null,
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
    fn () => [
        'input' => '',
        'allowed' => fn () => null,
    ],
]);

test('the `doesntEndWith()` rule can be conditionally validated', function (): void {
    $fails = [];

    $fieldName = Str::random();

    try {
        Schema::make(Livewire::make()->data([$fieldName => 'foo']))
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
