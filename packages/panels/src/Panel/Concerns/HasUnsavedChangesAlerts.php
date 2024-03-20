<?php

namespace Filament\Panel\Concerns;

use Closure;
use Exception;

trait HasUnsavedChangesAlerts
{
    protected bool | Closure $hasUnsavedChangesAlerts = false;

    public function unsavedChangesAlerts(bool | Closure $condition = true): static
    {
        $this->hasUnsavedChangesAlerts = $condition;

        return $this;
    }

    public function hasUnsavedChangesAlerts(): bool
    {
        $hasAlerts = (bool) $this->evaluate($this->hasUnsavedChangesAlerts);

        if ($hasAlerts && $this->hasSpaMode()) {
            throw new Exception('Unsaved changes alerts are not supported in SPA mode.');
        }

        return $hasAlerts;
    }
}
