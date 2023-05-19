<?php

namespace Filament\Billing\Providers\Http\Middleware;

use Exception;
use Filament\Facades\Filament;
use Spark\Http\Middleware\VerifyBillableIsSubscribed;

// We cannot explicitly require Spark through Composer, as Stripe
// and Paddle integrations have different packages associated
// with them. As such, we must check if this class exists.
if (! class_exists(VerifyBillableIsSubscribed::class)) {
    throw new Exception('Laravel Spark is not installed.');
}

class VerifySparkBillableIsSubscribed extends VerifyBillableIsSubscribed
{
    protected function redirect(string $billableType): string
    {
        return Filament::getTenantBillingUrl();
    }
}
