<?php

use Filament\Actions\Action;
use Filament\Tests\TestCase;
use Illuminate\Auth\Access\Response;

uses(TestCase::class);

describe('`authorizationNotificationOrHidden()`', function (): void {
    it('shows the action when the user is allowed', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::allow())
            ->authorizationNotificationOrHidden();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
        expect($action->isVisible())->toBeTrue();
    });

    it('shows the action when denied with a message', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::deny('You cannot do that.'))
            ->authorizationNotificationOrHidden();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
        expect($action->isVisible())->toBeTrue();
        expect($action->hasAuthorizationNotification())->toBeTrue();
        expect($action->getAuthorizationResponseWithMessage()->message())->toBe('You cannot do that.');
    });

    it('hides the action when denied with `Response::deny()` and no message', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::deny())
            ->authorizationNotificationOrHidden();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
        expect($action->isVisible())->toBeFalse();
    });

    it('hides the action when the policy returns bare `false`', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): bool => false)
            ->authorizationNotificationOrHidden();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
        expect($action->isVisible())->toBeFalse();
    });

    it('shows the action when `authorizationMessage()` is set even if the policy returns bare `false`', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): bool => false)
            ->authorizationMessage('Explicit message.')
            ->authorizationNotificationOrHidden();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
        expect($action->isVisible())->toBeTrue();
        expect($action->getAuthorizationResponseWithMessage()->message())->toBe('Explicit message.');
    });

    it('is a no-op when `condition: false` is passed', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): bool => false)
            ->authorizationNotificationOrHidden(false);

        expect($action->hasAuthorizationNotification())->toBeFalse();
        expect($action->shouldHideAuthorizationFeedbackWithoutMessage())->toBeFalse();
        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
        expect($action->isVisible())->toBeFalse();
    });

    it('stays hidden when `visible(false)` is also set', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::deny('Has a message.'))
            ->authorizationNotificationOrHidden()
            ->visible(false);

        expect($action->isVisible())->toBeFalse();
    });
});

describe('`authorizationTooltipOrHidden()`', function (): void {
    it('shows the action when the user is allowed', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::allow())
            ->authorizationTooltipOrHidden();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
        expect($action->isVisible())->toBeTrue();
    });

    it('shows the action with the deny message as a tooltip when denied with a message', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::deny('You cannot do that.'))
            ->authorizationTooltipOrHidden();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
        expect($action->isVisible())->toBeTrue();
        expect($action->hasAuthorizationTooltip())->toBeTrue();
        expect($action->getTooltip())->toBe('You cannot do that.');
    });

    it('hides the action when denied with `Response::deny()` and no message', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::deny())
            ->authorizationTooltipOrHidden();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
        expect($action->isVisible())->toBeFalse();
    });

    it('hides the action when the policy returns bare `false`', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): bool => false)
            ->authorizationTooltipOrHidden();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
        expect($action->isVisible())->toBeFalse();
    });

    it('shows the action when `authorizationMessage()` is set even if the policy returns bare `false`', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): bool => false)
            ->authorizationMessage('Explicit message.')
            ->authorizationTooltipOrHidden();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
        expect($action->isVisible())->toBeTrue();
        expect($action->getTooltip())->toBe('Explicit message.');
    });

    it('is a no-op when `condition: false` is passed', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): bool => false)
            ->authorizationTooltipOrHidden(false);

        expect($action->hasAuthorizationTooltip())->toBeFalse();
        expect($action->shouldHideAuthorizationFeedbackWithoutMessage())->toBeFalse();
        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
        expect($action->isVisible())->toBeFalse();
    });

    it('stays hidden when `visible(false)` is also set', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::deny('Has a message.'))
            ->authorizationTooltipOrHidden()
            ->visible(false);

        expect($action->isVisible())->toBeFalse();
    });
});

describe('regression', function (): void {
    it('still throws `LogicException` from `authorizationNotification()` when the deny has no message', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): bool => false)
            ->authorizationNotification();

        expect(fn () => $action->getAuthorizationResponseWithMessage())
            ->toThrow(LogicException::class, 'An authorization was denied without a message.');
    });

    it('still throws `LogicException` from `authorizationTooltip()` when the deny has no message', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): bool => false)
            ->authorizationTooltip();

        expect(fn () => $action->getAuthorizationResponseWithMessage())
            ->toThrow(LogicException::class, 'An authorization was denied without a message.');
    });
});
