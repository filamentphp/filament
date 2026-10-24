<?php

use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\FilamentManager;
use Filament\Panel;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Policies\TicketPolicy;
use Filament\Tests\TestCase;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

use function Filament\authorize;
use function Filament\get_authorization_response;

uses(TestCase::class);

describe('`filament()`', function (): void {
    it('returns the application manager', function (): void {
        expect(filament())->toBeInstanceOf(FilamentManager::class)->toBe(app('filament'));
    });

    it('returns a registered plugin', function (): void {
        $plugin = new class implements Plugin
        {
            public function getId(): string
            {
                return 'helper-test';
            }

            public function register(Panel $panel): void {}

            public function boot(Panel $panel): void {}
        };

        Filament::getCurrentOrDefaultPanel()->plugin($plugin);

        expect(filament('helper-test'))->toBe($plugin);
    });
});

describe('`get_authorization_response()`', function (): void {
    beforeEach(function (): void {
        $this->actingAs(new User);
    });

    it('honors existing policies in either policy-checking mode', function (bool $shouldCheckPolicyExistence, bool $isAllowed): void {
        app()->bind(TicketPolicy::class . '::viewAny', static fn (): bool => $isAllowed);

        expect(get_authorization_response('viewAny', Ticket::class, $shouldCheckPolicyExistence)->allowed())->toBe($isAllowed);
    })->with([true, false])->with([true, false]);

    it('preserves policy denial messages and status', function (): void {
        app()->bind(TicketPolicy::class . '::update', static fn (): Response => Response::denyWithStatus(404, 'Not available'));

        $response = get_authorization_response('update', new Ticket);

        expect($response->denied())->toBeTrue()
            ->and($response->message())->toBe('Not available')
            ->and($response->status())->toBe(404);
    });

    it('accepts string, backed-enum and unit-enum actions', function (UnitEnum | string $action): void {
        app()->bind(TicketPolicy::class . '::viewAny', static fn (): bool => false);

        expect(get_authorization_response($action, Ticket::class)->denied())->toBeTrue();
    })->with([
        'string' => ['viewAny'],
        'backed enum' => [fn (): HelperBackedAbility => HelperBackedAbility::Browse],
        'unit enum' => [fn (): HelperUnitAbility => HelperUnitAbility::viewAny],
    ]);

    it('allows missing policies and methods by default', function (): void {
        expect(get_authorization_response('missing', Ticket::class)->allowed())->toBeTrue()
            ->and(get_authorization_response('missing', User::class)->allowed())->toBeTrue();
    });

    it('honors gate before callbacks when a policy method is missing', function (bool | Response | null $result, bool $isAllowed): void {
        Gate::before(static fn (): bool | Response | null => $result);

        $response = get_authorization_response('missing', Ticket::class);

        expect($response->allowed())->toBe($isAllowed);

        if ($result instanceof Response) {
            expect($response)->toBe($result);
        }
    })->with([
        'allow' => [true, true],
        'deny' => [false, false],
        'abstain' => [null, true],
        'response' => [Response::deny('Denied by callback'), false],
    ]);

    it('rejects missing policies and methods in strict mode', function (string $model): void {
        Filament::getCurrentOrDefaultPanel()->strictAuthorization();

        expect(fn () => get_authorization_response('missing', $model))
            ->toThrow(LogicException::class, 'Strict authorization mode is enabled');
    })->with(['missing method' => [Ticket::class], 'missing policy' => [User::class]]);

    it('rejects undefined abilities without a policy in strict mode when policy checking is disabled', function (): void {
        Filament::getCurrentOrDefaultPanel()->strictAuthorization();

        expect(fn () => get_authorization_response('missing', User::class, false))
            ->toThrow(LogicException::class, 'Strict authorization mode is enabled');
    });

    it('allows defined abilities in strict mode when policy checking is disabled', function (bool $isAllowed): void {
        Filament::getCurrentOrDefaultPanel()->strictAuthorization();
        Gate::define('custom', static fn (): bool => $isAllowed);

        expect(get_authorization_response('custom', Ticket::class, false)->allowed())->toBe($isAllowed);
    })->with([true, false]);

    it('denies undefined abilities when policy checking is disabled', function (): void {
        expect(get_authorization_response('missing', Ticket::class, false)->denied())->toBeTrue();
    });
});

describe('`authorize()`', function (): void {
    beforeEach(function (): void {
        $this->actingAs(new User);
    });

    it('returns an allowed response', function (): void {
        expect(authorize('viewAny', Ticket::class)->allowed())->toBeTrue();
    });

    it('throws an authorization exception for denied policies', function (): void {
        app()->bind(TicketPolicy::class . '::viewAny', static fn (): Response => Response::deny('Not permitted'));

        expect(fn () => authorize('viewAny', Ticket::class))->toThrow(AuthorizationException::class, 'Not permitted');
    });

    it('can authorize gate abilities without a policy', function (): void {
        Gate::define('custom', static fn (): bool => false);

        expect(fn () => authorize('custom', Ticket::class, false))->toThrow(AuthorizationException::class);
    });
});

enum HelperBackedAbility: string
{
    case Browse = 'viewAny';
}

enum HelperUnitAbility
{
    case viewAny;
}
