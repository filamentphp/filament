<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasSidebar
{
    protected string $sidebarWidth = '20rem';

    protected string $collapsedSidebarWidth = '4.5rem';

    protected bool | Closure $isSidebarCollapsibleOnDesktop = false;

    protected bool | Closure $isSidebarFullyCollapsibleOnDesktop = false;

    protected bool | Closure $hasCollapsibleNavigationGroups = true;

    public function sidebarCollapsibleOnDesktop(bool | Closure $condition = true): static
    {
        $this->isSidebarCollapsibleOnDesktop = $condition;

        return $this;
    }

    public function sidebarFullyCollapsibleOnDesktop(bool | Closure $condition = true): static
    {
        $this->isSidebarFullyCollapsibleOnDesktop = $condition;

        return $this;
    }

    public function collapsibleNavigationGroups(bool | Closure $condition = true): static
    {
        $this->hasCollapsibleNavigationGroups = $condition;

        return $this;
    }

    public function sidebarWidth(string $width): static
    {
        $this->sidebarWidth = $width;

        return $this;
    }

    public function collapsedSidebarWidth(string $width): static
    {
        $this->collapsedSidebarWidth = $width;

        return $this;
    }

    public function getSidebarWidth(): string
    {
        return $this->sidebarWidth;
    }

    public function getCollapsedSidebarWidth(): string
    {
        return $this->collapsedSidebarWidth;
    }

    public function isSidebarCollapsibleOnDesktop(): bool
    {
        return (bool) $this->evaluate($this->isSidebarCollapsibleOnDesktop);
    }

    public function isSidebarFullyCollapsibleOnDesktop(): bool
    {
        return (bool) $this->evaluate($this->isSidebarFullyCollapsibleOnDesktop);
    }

    public function hasCollapsibleNavigationGroups(): bool
    {
        return (bool) $this->evaluate($this->hasCollapsibleNavigationGroups);
    }
}
