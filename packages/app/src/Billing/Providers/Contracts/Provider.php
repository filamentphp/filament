<?php

namespace Filament\Billing\Providers\Contracts;

use Closure;

interface Provider
{
    /**
     * @return callable-string | Closure | array<string, string>
     */
    public function getRouteAction(): string | Closure | array;

    public function getSubscribedMiddleware(): string;
}
