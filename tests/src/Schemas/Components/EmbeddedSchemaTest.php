<?php

use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be constructed with a name', function (): void {
    $component = EmbeddedSchema::make('sidebar');

    expect($component->getName())->toBe('sidebar');
});

it('can set `name()` with a `Closure`', function (): void {
    $component = EmbeddedSchema::make('initial')
        ->name(static fn (): string => 'dynamic');

    expect($component->getName())->toBe('dynamic');
});

it('returns fluent `$this` from `name()`', function (): void {
    $component = EmbeddedSchema::make('test');

    expect($component->name('other'))->toBe($component);
});
