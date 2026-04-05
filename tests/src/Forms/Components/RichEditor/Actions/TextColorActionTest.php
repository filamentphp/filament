<?php

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\Actions\TextColorAction;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('creates an `Action` with name `textColor`', function (): void {
    $action = TextColorAction::make();

    expect($action)->toBeInstanceOf(Action::class);
    expect($action->getName())->toBe('textColor');
});

it('has a label', function (): void {
    $action = TextColorAction::make();

    expect($action->getLabel())->toBeString();
    expect($action->getLabel())->not->toBeEmpty();
});
