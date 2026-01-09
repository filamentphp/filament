<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\LivewireField;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be instantiated', function (): void {
    $field = LivewireField::make('custom');

    expect($field->getName())->toBe('custom');
});
