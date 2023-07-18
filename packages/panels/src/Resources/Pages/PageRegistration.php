<?php

namespace Filament\Resources\Pages;

use Closure;
use Filament\Panel;
use Illuminate\Routing\Route;

class PageRegistration
{
    public function __construct(
        protected string $page,
        protected Closure $route,
    ) {
    }

    public function registerRoute(Panel $panel): ?Route
    {
        return ($this->route)($panel);
    }

    public function getPage(): string
    {
        return $this->page;
    }
}
