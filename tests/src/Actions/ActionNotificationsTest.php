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
        ->successNotification(null);

    expect($action->getSuccessNotificationTitle())->toBe('Created');

    $action->sendSuccessNotification();

    expect(session()->get('filament.notifications'))->toBeNull();
});

it('will not send a failure notification by default', function (): void {
    $action = CreateAction::make();

    expect($action->getFailureNotificationTitle())->toBe(null);

    $action->sendFailureNotification();

    expect(session()->get('filament.notifications'))->toBeNull();
});

it('will send a failure notification when set', function (): void {
    $action = CreateAction::make()
        ->failureNotificationTitle('Creation Failed');

    expect($action->getFailureNotificationTitle())->toBe('Creation Failed');

    $action->sendFailureNotification();

    expect(session()->get('filament.notifications'))->not()->toBeNull();
});

it('will not send a failure notification when disabled', function (): void {
    $action = CreateAction::make()
        ->failureNotificationTitle('Creation Failed')
        ->failureNotification(null);

    expect($action->getFailureNotificationTitle())->toBe('Creation Failed');

    $action->sendFailureNotification();

    expect(session()->get('filament.notifications'))->toBeNull();
});

it('will not send an unauthorized notification by default', function (): void {
    $action = CreateAction::make();

    $mockAuthResponse = $this->createMock(\Illuminate\Auth\Access\Response::class);

    $action->sendUnauthorizedNotification($mockAuthResponse);

    expect(session()->get('filament.notifications'))->toBeNull();
});

it('will send an unauthorized notification when set', function (): void {
    $action = CreateAction::make()
        ->unauthorizedNotificationTitle('Unauthorized Action');

    $mockAuthResponse = $this->createMock(\Illuminate\Auth\Access\Response::class);

    expect($action->getUnauthorizedNotificationTitle($mockAuthResponse))->toBe('Unauthorized Action');

    $action->sendUnauthorizedNotification($mockAuthResponse);

    expect(session()->get('filament.notifications'))->not()->toBeNull();
});

it('will not send an unauthorized notification when disabled', function (): void {
    $action = CreateAction::make()
        ->unauthorizedNotificationTitle('Unauthorized Action')
        ->unauthorizedNotification(null);

    $mockAuthResponse = $this->createMock(\Illuminate\Auth\Access\Response::class);

    expect($action->getUnauthorizedNotificationTitle($mockAuthResponse))->toBe('Unauthorized Action');

    $action->sendUnauthorizedNotification($mockAuthResponse);

    expect(session()->get('filament.notifications'))->toBeNull();
});

it('will send a rate limited notification by default', function (): void {
    $action = CreateAction::make();

    $tooManyRequestExceptionMock = $this->createMock(\DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException::class);

    expect($action->getRateLimitedNotificationTitle($tooManyRequestExceptionMock))->toBe(null);

    $action->sendRateLimitedNotification($tooManyRequestExceptionMock);

    expect(session()->get('filament.notifications'))->not()->toBeNull();
});

it('will not send a rate limited notification when disabled', function (): void {
    $action = CreateAction::make()
        ->rateLimitedNotification(null);

    $tooManyRequestExceptionMock = $this->createMock(\DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException::class);

    expect($action->getRateLimitedNotificationTitle($tooManyRequestExceptionMock))->toBe(null);

    $action->sendRateLimitedNotification($tooManyRequestExceptionMock);

    expect(session()->get('filament.notifications'))->toBeNull();
});
