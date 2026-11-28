<?php

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('resolves `trim()` conditions lazily and invalidates the cached result when reconfigured', function (string $fieldClass): void {
    $shouldTrim = false;
    $field = $fieldClass::make('value')->trim(static function () use (&$shouldTrim): bool {
        return $shouldTrim;
    });
    $schema = Schema::make(Livewire::make())->statePath('data')->components([$field]);
    $rawState = ($field instanceof TagsInput) ? ['  value  '] : '  value  ';
    $trimmedState = ($field instanceof TagsInput) ? ['value'] : 'value';

    // Hydrating and setting raw display values must not resolve the condition.
    $schema->fill(['value' => $rawState], shouldCallHydrationHooks: false);
    $schema->fillPartially(['value' => $rawState], ['value'], shouldCallHydrationHooks: false);
    $field->state($rawState);
    $shouldTrim = true;

    expect($field->getState())->toBe($trimmedState)
        ->and($schema->getRawState()['value'])->toBe($rawState);

    $shouldTrim = false;

    expect($field->getState())->toBe($trimmedState);

    $field->trim(false);

    expect($field->getState())->toBe($rawState);

    $field->trim();

    expect($field->getState())->toBe($trimmedState);
})->with([TextInput::class, Textarea::class, TagsInput::class]);

it('can use the field state in a `trim()` condition', function (string $conditionType): void {
    $condition = match ($conditionType) {
        'state' => static fn ($state): bool => $state === '  Alice  ',
        'self Get' => static fn (Get $get): bool => $get('value') === '  Alice  ',
        'sibling Get' => static fn (Get $get): bool => $get('enabled') === 'yes',
    };
    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            $field = TextInput::make('value')->trim($condition),
            TextInput::make('enabled')->trim(),
        ])
        ->fill(['value' => '  Alice  ', 'enabled' => ' yes ']);

    expect($field->getState())->toBe('Alice')
        ->and($schema->getRawState()['value'])->toBe('  Alice  ');
})->with(['state', 'self Get', 'sibling Get']);

it('does not retain an in-progress `trim()` condition after an exception', function (): void {
    $shouldThrow = true;
    Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            $field = TextInput::make('value')->trim(static function () use (&$shouldThrow): bool {
                if ($shouldThrow) {
                    throw new RuntimeException('Condition failed');
                }

                return true;
            }),
        ])
        ->fill(['value' => '  Alice  ']);

    expect(fn () => $field->getState())->toThrow(RuntimeException::class, 'Condition failed');

    $shouldThrow = false;

    expect($field->getState())->toBe('Alice');
});

it('preserves non-string `trim()` state', function (string $fieldClass, mixed $state): void {
    Schema::make(Livewire::make())
        ->statePath('data')
        ->components([$field = $fieldClass::make('value')->trim()])
        ->fill(['value' => $state], shouldCallHydrationHooks: false);

    expect($field->getState())->toBe($state);
})->with([
    'text input' => [TextInput::class],
    'textarea' => [Textarea::class],
])->with([[123], [false], [['  nested  ']]]);

it('does not let read-only trimming prevent hydration casts on a shared state path', function (string $fieldClass, bool $shouldTrim, bool $hasWriteCast): void {
    $first = $fieldClass::make('value')->key('hidden')->hidden()->trim($shouldTrim);

    if ($hasWriteCast) {
        $first->stripCharacters('!');
    }

    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            $first,
            TextInput::make('value')->key('visible')->stripCharacters('#'),
        ])
        ->fill(['value' => '#123#']);

    expect($schema->getRawState()['value'])->toBe($hasWriteCast ? '#123#' : '123');
})->with([
    'text input' => [TextInput::class],
    'textarea' => [Textarea::class],
])->with([false, true])->with([false, true]);

it('allows fields to opt out of globally configured `trim()`', function (string $fieldClass): void {
    $fieldClass::configureUsing(static function ($field): void {
        $field->trim();
    }, during: function () use ($fieldClass): void {
        $rawState = ($fieldClass === TagsInput::class) ? ['  Alice  '] : '  Alice  ';
        $trimmedState = ($fieldClass === TagsInput::class) ? ['Alice'] : 'Alice';
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $trimmed = $fieldClass::make('trimmed'),
                $untrimmed = $fieldClass::make('untrimmed')->trim(false),
            ])
            ->fill(['trimmed' => $rawState, 'untrimmed' => $rawState]);

        expect($trimmed->getState())->toBe($trimmedState)
            ->and($untrimmed->getState())->toBe($rawState)
            ->and($schema->getRawState())->toBe(['trimmed' => $rawState, 'untrimmed' => $rawState]);
    });
})->with([TextInput::class, Textarea::class, TagsInput::class]);
