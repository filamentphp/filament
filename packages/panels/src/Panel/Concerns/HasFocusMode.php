<?php

namespace Filament\Panel\Concerns;

trait HasFocusMode
{
    protected bool $hasFocusMode = false;
    protected bool $showOnListPage = true;
    protected bool $showOnFormPage = true;

    /**
     * @param  bool  $condition
     * @param  bool  $showOnListPage
     * @param  bool  $showOnFormPage
     */
    public function focusMode(bool $condition = true, bool $showOnListPage = true, bool $showOnFormPage = true): static
    {
        $this->hasFocusMode = $condition;
        $this->showOnListPage = $showOnListPage;
        $this->showOnFormPage = $showOnFormPage;

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

    public function canShowFocusModeOnFormPage(): bool
    {
        return $this->showOnFormPage;
    }

}