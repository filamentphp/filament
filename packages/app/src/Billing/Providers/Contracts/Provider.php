<?php

namespace Filament\Billing\Providers\Contracts;

use Closure;

interface Provider
{
    public function getRouteAction(): string | array | Closure;

    public function getSubscribedMiddleware(): string;
}
