<?php

namespace Filament\Concerns;

trait HasSidebar
{
    protected string $sidebarWidth = '18rem';

    protected string $collapsedSidebarWidth = '5.4rem';

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
}
