<?php

use Filament\Actions\Action;
use Filament\Tests\TestCase;
use Illuminate\Auth\Access\Response;

uses(TestCase::class);

describe('`authorizationNotification()`', function (): void {
    it('shows the action when the user is allowed', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::allow())
            ->authorizationNotification();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
        expect($action->isVisible())->toBeTrue();
    });

    it('shows the action when denied with a message', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::deny('You cannot do that.'))
            ->authorizationNotification();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
        expect($action->isVisible())->toBeTrue();
        expect($action->hasAuthorizationNotification())->toBeTrue();
        expect($action->getAuthorizationResponseWithMessage()->message())->toBe('You cannot do that.');
    });

    it('hides the action when denied with `Response::deny()` and no message', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::deny())
            ->authorizationNotification();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
        expect($action->isVisible())->toBeFalse();
    });

    it('hides the action when the policy returns bare `false`', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): bool => false)
            ->authorizationNotification();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
        expect($action->isVisible())->toBeFalse();
    });

    it('shows the action when `authorizationMessage()` is set even if the policy returns bare `false`', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): bool => false)
            ->authorizationMessage('Explicit message.')
            ->authorizationNotification();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
        expect($action->isVisible())->toBeTrue();
        expect($action->getAuthorizationResponseWithMessage()->message())->toBe('Explicit message.');
    });

    it('is a no-op when `condition: false` is passed', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): bool => false)
            ->authorizationNotification(false);

        expect($action->hasAuthorizationNotification())->toBeFalse();
        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
        expect($action->isVisible())->toBeFalse();
    });

    it('stays hidden when `visible(false)` is also set', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::deny('Has a message.'))
            ->authorizationNotification()
            ->visible(false);

        expect($action->isVisible())->toBeFalse();
    });
});

describe('`authorizationTooltip()`', function (): void {
    it('shows the action when the user is allowed', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::allow())
            ->authorizationTooltip();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
        expect($action->isVisible())->toBeTrue();
    });

    it('shows the action with the deny message as a tooltip when denied with a message', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::deny('You cannot do that.'))
            ->authorizationTooltip();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
        expect($action->isVisible())->toBeTrue();
        expect($action->hasAuthorizationTooltip())->toBeTrue();
        expect($action->getTooltip())->toBe('You cannot do that.');
    });

    it('hides the action when denied with `Response::deny()` and no message', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::deny())
            ->authorizationTooltip();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
        expect($action->isVisible())->toBeFalse();
    });

    it('hides the action when the policy returns bare `false`', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): bool => false)
            ->authorizationTooltip();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
        expect($action->isVisible())->toBeFalse();
    });

    it('shows the action when `authorizationMessage()` is set even if the policy returns bare `false`', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): bool => false)
            ->authorizationMessage('Explicit message.')
            ->authorizationTooltip();

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
        expect($action->isVisible())->toBeTrue();
        expect($action->getTooltip())->toBe('Explicit message.');
    });

    it('is a no-op when `condition: false` is passed', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): bool => false)
            ->authorizationTooltip(false);

        expect($action->hasAuthorizationTooltip())->toBeFalse();
        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
        expect($action->isVisible())->toBeFalse();
    });

    it('stays hidden when `visible(false)` is also set', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::deny('Has a message.'))
            ->authorizationTooltip()
            ->visible(false);

        expect($action->isVisible())->toBeFalse();
    });
});
