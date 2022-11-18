<?php

namespace Filament\Billing\Providers\Contracts;

use Closure;

interface Provider
{
    public function getRouteAction(): string | Closure | array;

    public function getSubscribedMiddleware(): string;
}
