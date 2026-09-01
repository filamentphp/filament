<?php

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\CreateAction;
use Filament\Tests\TestCase;
use Illuminate\Auth\Access\Response;

uses(TestCase::class);

describe('success notifications', function (): void {
    it('will send a success notification by default', function (): void {
        $action = CreateAction::make();

        expect($action->getSuccessNotificationTitle())->toBe('Created');

        $action->sendSuccessNotification();

        expect(session()->get('filament.notifications'))->not()->toBeNull();
    });

    it('will not send a success notification when disabled via `successNotification(null)`', function (): void {
        $action = CreateAction::make()
            ->successNotification(null);

        expect($action->getSuccessNotificationTitle())->toBe('Created');

        $action->sendSuccessNotification();

        expect(session()->get('filament.notifications'))->toBeNull();
    });
});

describe('failure notifications', function (): void {
    it('will send a failure notification when set via `failureNotificationTitle()`', function (): void {
        $action = CreateAction::make()
            ->failureNotificationTitle('Creation Failed');

        expect($action->getFailureNotificationTitle())->toBe('Creation Failed');

        $action->sendFailureNotification();

        expect(session()->get('filament.notifications'))->not()->toBeNull();
    });

    it('will not send a failure notification by default', function (): void {
        $action = CreateAction::make();

        expect($action->getFailureNotificationTitle())->toBe(null);

        $action->sendFailureNotification();

        expect(session()->get('filament.notifications'))->toBeNull();
    });

    it('will not send a failure notification when disabled via `failureNotification(null)`', function (): void {
        $action = CreateAction::make()
            ->failureNotificationTitle('Creation Failed')
            ->failureNotification(null);

        expect($action->getFailureNotificationTitle())->toBe('Creation Failed');

        $action->sendFailureNotification();

        expect(session()->get('filament.notifications'))->toBeNull();
    });
});

describe('unauthorized notifications', function (): void {
    it('will send an unauthorized notification when set via `unauthorizedNotificationTitle()`', function (): void {
        $action = CreateAction::make()
            ->unauthorizedNotificationTitle('Unauthorized Action');

        $mockAuthResponse = $this->createMock(Response::class);

        expect($action->getUnauthorizedNotificationTitle($mockAuthResponse))->toBe('Unauthorized Action');

        $action->sendUnauthorizedNotification($mockAuthResponse);

        expect(session()->get('filament.notifications'))->not()->toBeNull();
    });

    it('will not send an unauthorized notification by default', function (): void {
        $action = CreateAction::make();

        $mockAuthResponse = $this->createMock(Response::class);

        $action->sendUnauthorizedNotification($mockAuthResponse);

        expect(session()->get('filament.notifications'))->toBeNull();
    });

    it('will not send an unauthorized notification when disabled via `unauthorizedNotification(null)`', function (): void {
        $action = CreateAction::make()
            ->unauthorizedNotificationTitle('Unauthorized Action')
            ->unauthorizedNotification(null);

        $mockAuthResponse = $this->createMock(Response::class);

        expect($action->getUnauthorizedNotificationTitle($mockAuthResponse))->toBe('Unauthorized Action');

        $action->sendUnauthorizedNotification($mockAuthResponse);

        expect(session()->get('filament.notifications'))->toBeNull();
    });
});

describe('rate limited notifications', function (): void {
    it('will send a rate limited notification by default', function (): void {
        $action = CreateAction::make();

        $tooManyRequestExceptionMock = $this->createMock(TooManyRequestsException::class);

        expect($action->getRateLimitedNotificationTitle($tooManyRequestExceptionMock))->toBe(null);

        $action->sendRateLimitedNotification($tooManyRequestExceptionMock);

        expect(session()->get('filament.notifications'))->not()->toBeNull();
    });

    it('will not send a rate limited notification when disabled via `rateLimitedNotification(null)`', function (): void {
        $action = CreateAction::make()
            ->rateLimitedNotification(null);

        $tooManyRequestExceptionMock = $this->createMock(TooManyRequestsException::class);

        expect($action->getRateLimitedNotificationTitle($tooManyRequestExceptionMock))->toBe(null);

        $action->sendRateLimitedNotification($tooManyRequestExceptionMock);

        expect(session()->get('filament.notifications'))->toBeNull();
    });
});
