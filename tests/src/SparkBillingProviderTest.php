<?php

use Filament\Billing\Providers\Http\Middleware\VerifySparkBillableIsSubscribed;
use Filament\Billing\Providers\SparkBillingProvider;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('returns `VerifySparkBillableIsSubscribed` middleware from `getSubscribedMiddleware()`', function (): void {
    $provider = new SparkBillingProvider;

    expect($provider->getSubscribedMiddleware())->toBe(VerifySparkBillableIsSubscribed::class);
});

it('returns a `Closure` from `getRouteAction()`', function (): void {
    $provider = new SparkBillingProvider;

    expect($provider->getRouteAction())->toBeInstanceOf(Closure::class);
});

it('throws `LogicException` when Spark is not installed', function (): void {
    $provider = new SparkBillingProvider;
    $action = $provider->getRouteAction();

    expect(static fn () => $action())->toThrow(LogicException::class, 'Laravel Spark is not installed.');
});
