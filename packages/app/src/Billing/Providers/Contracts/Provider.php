<?php

namespace Filament\Billing\Providers\Contracts;

use Closure;

interface Provider
{
    /**
     * @return class-string | callable-string | Closure | array<class-string, string>
     */
    public function getRouteAction(): string | Closure | array;

    public function getSubscribedMiddleware(): string;
}
