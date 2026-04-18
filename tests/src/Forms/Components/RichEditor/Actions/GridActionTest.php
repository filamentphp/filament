<?php

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\Actions\GridAction;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('creates an `Action` with name `grid`', function (): void {
    $action = GridAction::make();

    expect($action)->toBeInstanceOf(Action::class);
    expect($action->getName())->toBe('grid');
});

it('has a label', function (): void {
    $action = GridAction::make();

    expect($action->getLabel())->toBeString();
    expect($action->getLabel())->not->toBeEmpty();
});

it('has a modal heading', function (): void {
    $action = GridAction::make();

    expect($action->getModalHeading())->toBeString();
    expect($action->getModalHeading())->not->toBeEmpty();
});
