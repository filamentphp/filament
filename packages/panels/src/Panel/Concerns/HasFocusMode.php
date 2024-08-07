<?php

namespace Filament\Panel\Concerns;

trait HasFocusMode
{
    protected bool $hasFocusMode = false;
    protected bool $showOnListPage = true;
    protected bool $showOnCreatePage = true;
    protected bool $showOnEditPage = true;

    /**
     * @param  bool  $condition
     * @param  bool  $showOnListPage
     * @param  bool  $showOnEditPage
     */
    public function focusMode(bool $condition = true, bool $showOnListPage = true, bool $showOnCreatePage = true, bool $showOnEditPage = true): static
    {
        $this->hasFocusMode = $condition;
        $this->showOnListPage = $showOnListPage;
        $this->showOnCreatePage = $showOnCreatePage;
        $this->showOnEditPage = $showOnEditPage;

        return $this;
    }

    public function hasFocusMode(): bool
    {
        return $this->hasFocusMode;
    }

    public function canShowFocusModeOnListPage(): bool
    {
        return $this->showOnListPage;
    }

    public function canShowFocusModeOnCreatePage(): bool
    {
        return $this->showOnCreatePage;
    }

    public function canShowFocusModeOnEditPage(): bool
    {
        return $this->showOnEditPage;
    }

}
