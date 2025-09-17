<?php

namespace Filament\Billing\Providers\Contracts;

use Closure;

interface BillingProvider
{
    /**
     * @return class-string | callable-string | Closure | array<class-string, string>
     */
    public function getRouteAction(): string | Closure | array;

    public function getSubscribedMiddleware(): string;
}
