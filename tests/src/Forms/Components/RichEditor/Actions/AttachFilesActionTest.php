<?php

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\Actions\AttachFilesAction;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('creates an `Action` with name `attachFiles`', function (): void {
    $action = AttachFilesAction::make();

    expect($action)->toBeInstanceOf(Action::class);
    expect($action->getName())->toBe('attachFiles');
});

it('has a label', function (): void {
    $action = AttachFilesAction::make();

    expect($action->getLabel())->toBeString();
    expect($action->getLabel())->not->toBeEmpty();
});

it('has a modal heading', function (): void {
    $action = AttachFilesAction::make();

    expect($action->getModalHeading())->toBeString();
    expect($action->getModalHeading())->not->toBeEmpty();
});
