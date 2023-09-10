<?php

namespace Filament\Panel\Concerns;

trait HasSidebar
{
    protected string $sidebarWidth = '20rem';

    protected string $collapsedSidebarWidth = '4.5rem';

    protected bool $isSidebarCollapsibleOnDesktop = false;

    protected bool $isSidebarFullyCollapsibleOnDesktop = false;

    protected bool $hasCollapsibleNavigationGroups = true;

    public function sidebarCollapsibleOnDesktop(bool $condition = true): static
    {
        $this->isSidebarCollapsibleOnDesktop = $condition;

        return $this;
    }

    public function sidebarFullyCollapsibleOnDesktop(bool $condition = true): static
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
        return $this->isSidebarCollapsibleOnDesktop;
    }

    public function isSidebarFullyCollapsibleOnDesktop(): bool
    {
        return $this->isSidebarFullyCollapsibleOnDesktop;
    }

    public function hasCollapsibleNavigationGroups(): bool
    {
        return $this->hasCollapsibleNavigationGroups;
    }
}
