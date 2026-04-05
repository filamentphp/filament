<?php

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\Actions\LinkAction;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('creates an `Action` with name `link`', function (): void {
    $action = LinkAction::make();

    expect($action)->toBeInstanceOf(Action::class);
    expect($action->getName())->toBe('link');
});

it('has a label', function (): void {
    $action = LinkAction::make();

    expect($action->getLabel())->toBeString();
    expect($action->getLabel())->not->toBeEmpty();
});

it('has a modal heading', function (): void {
    $action = LinkAction::make();

    expect($action->getModalHeading())->toBeString();
    expect($action->getModalHeading())->not->toBeEmpty();
});
