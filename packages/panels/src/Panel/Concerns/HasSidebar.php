<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasSidebar
{
    protected string $sidebarWidth = '20rem';

    protected string $collapsedSidebarWidth = '4.5rem';

    protected Closure | bool $isSidebarCollapsibleOnDesktop = false;

    protected Closure | bool $isSidebarFullyCollapsibleOnDesktop = false;

    protected bool $hasCollapsibleNavigationGroups = true;

    public function sidebarCollapsibleOnDesktop(Closure | bool $condition = true): static
    {
        $this->isSidebarCollapsibleOnDesktop = $condition;

        return $this;
    }

    public function sidebarFullyCollapsibleOnDesktop(Closure | bool $condition = true): static
    {
        $this->isSidebarFullyCollapsibleOnDesktop = $condition;

        return $this;
    }

    public function collapsibleNavigationGroups(bool $condition = true): static
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
        return $this->evaluate($this->isSidebarCollapsibleOnDesktop);
    }

    public function isSidebarFullyCollapsibleOnDesktop(): bool
    {
        return $this->evaluate($this->isSidebarFullyCollapsibleOnDesktop);
    }

    public function hasCollapsibleNavigationGroups(): bool
    {
        return $this->hasCollapsibleNavigationGroups;
    }
}
