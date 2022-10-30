<?php

namespace Filament\Resources\Pages;

use Closure;
use Illuminate\Routing\Route;

class PageRegistration
{
    public function __construct(
        protected string $page,
        protected Closure $route,
    ) {
    }

    public function registerRoute(): ?Route
    {
        return ($this->route)();
    }

    public function getPage(): string
    {
        return $this->page;
    }
}
