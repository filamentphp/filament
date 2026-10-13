<?php

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Tests\TestCase;
use Illuminate\Auth\Access\Response;

uses(TestCase::class);

describe('success notifications', function (): void {
    it('will send a success notification by default', function (): void {
        $action = CreateAction::make();

        expect($action->getSuccessNotificationTitle())->toBe('Created');

        $action->sendSuccessNotification();

        Notification::assertNotified();
    });

    it('will not send a success notification when disabled via `successNotification(null)`', function (): void {
        $action = CreateAction::make()
            ->successNotification(null);

        expect($action->getSuccessNotificationTitle())->toBe('Created');

        $action->sendSuccessNotification();

        Notification::assertNotNotified();
    });
});

describe('failure notifications', function (): void {
    it('will send a failure notification when set via `failureNotificationTitle()`', function (): void {
        $action = CreateAction::make()
            ->failureNotificationTitle('Creation Failed');

        expect($action->getFailureNotificationTitle())->toBe('Creation Failed');

        $action->sendFailureNotification();

        Notification::assertNotified('Creation Failed');
    });

    it('will not send a failure notification by default', function (): void {
        $action = CreateAction::make();

        expect($action->getFailureNotificationTitle())->toBe(null);

        $action->sendFailureNotification();

        Notification::assertNotNotified();
    });

    it('will not send a failure notification when disabled via `failureNotification(null)`', function (): void {
        $action = CreateAction::make()
            ->failureNotificationTitle('Creation Failed')
            ->failureNotification(null);

        expect($action->getFailureNotificationTitle())->toBe('Creation Failed');

        $action->sendFailureNotification();

        Notification::assertNotNotified();
    });
});

describe('unauthorized notifications', function (): void {
    it('will send an unauthorized notification when set via `unauthorizedNotificationTitle()`', function (): void {
        $action = CreateAction::make()
            ->unauthorizedNotificationTitle('Unauthorized Action');

        $authorizationResponse = Response::deny();

        expect($action->getUnauthorizedNotificationTitle($authorizationResponse))->toBe('Unauthorized Action');

        $action->sendUnauthorizedNotification($authorizationResponse);

        Notification::assertNotified('Unauthorized Action');
    });

    it('will not send an unauthorized notification by default', function (): void {
        $action = CreateAction::make();

        $authorizationResponse = Response::deny();

        $action->sendUnauthorizedNotification($authorizationResponse);

        Notification::assertNotNotified();
    });

    it('will not send an unauthorized notification when disabled via `unauthorizedNotification(null)`', function (): void {
        $action = CreateAction::make()
            ->unauthorizedNotificationTitle('Unauthorized Action')
            ->unauthorizedNotification(null);

        $authorizationResponse = Response::deny();

        expect($action->getUnauthorizedNotificationTitle($authorizationResponse))->toBe('Unauthorized Action');

        $action->sendUnauthorizedNotification($authorizationResponse);

        Notification::assertNotNotified();
    });
});

describe('rate limited notifications', function (): void {
    it('will send a rate limited notification by default', function (): void {
        $action = CreateAction::make();

        $exception = new TooManyRequestsException(CreateAction::class, 'call', '127.0.0.1', 60);

        expect($action->getRateLimitedNotificationTitle($exception))->toBe(null);

        $action->sendRateLimitedNotification($exception);

        Notification::assertNotified();
    });

    it('will not send a rate limited notification when disabled via `rateLimitedNotification(null)`', function (): void {
        $action = CreateAction::make()
            ->rateLimitedNotification(null);

        $exception = new TooManyRequestsException(CreateAction::class, 'call', '127.0.0.1', 60);

        expect($action->getRateLimitedNotificationTitle($exception))->toBe(null);

        $action->sendRateLimitedNotification($exception);

        Notification::assertNotNotified();
    });
});
