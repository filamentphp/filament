<?php

namespace Filament\Resources\Pages;

use Closure;
use Filament\Context;
use Illuminate\Routing\Route;

class PageRegistration
{
    public function __construct(
        protected string $page,
        protected Closure $route,
    ) {
    }

    public function registerRoute(Context $context): ?Route
    {
        return ($this->route)($context);
    }

    public function getPage(): string
    {
        return $this->page;
    }
}
