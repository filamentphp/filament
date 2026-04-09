<?php

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\Actions\CustomBlockAction;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('creates an `Action` with name `customBlock`', function (): void {
    $action = CustomBlockAction::make();

    expect($action)->toBeInstanceOf(Action::class);
    expect($action->getName())->toBe('customBlock');
});

it('exposes its name via `NAME` constant', function (): void {
    expect(CustomBlockAction::NAME)->toBe('customBlock');
});

it('has a label', function (): void {
    $action = CustomBlockAction::make();

    expect($action->getLabel())->toBeString();
    expect($action->getLabel())->not->toBeEmpty();
});
