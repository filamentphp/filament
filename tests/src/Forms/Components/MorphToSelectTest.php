<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\MorphToSelect;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be instantiated', function (): void {
    $component = MorphToSelect::make('commentable');

    expect($component->getName())->toBe('commentable');
});
