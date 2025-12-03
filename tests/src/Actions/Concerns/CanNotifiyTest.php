<?php

use Filament\Actions\CreateAction;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('will send a success notification by default', function (): void {
    $action = CreateAction::make();

    expect($action->getSuccessNotificationTitle())->toBe('Created');

    $action->sendSuccessNotification();

    expect(session()->get('filament.notifications'))->not()->toBeNull();
});

it('will not send a success notification when disabled', function (): void {
    $action = CreateAction::make()
        ->disableSuccessNotification();

    expect($action->getSuccessNotificationTitle())->toBe('Created');

    $action->sendSuccessNotification();

    expect(session()->get('filament.notifications'))->toBeNull();
});
