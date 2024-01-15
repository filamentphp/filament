<?php

namespace Filament\Pages\Concerns;

use Filament\Facades\Filament;
use Livewire\Attributes\Locked;

trait HasUnsavedDataChangesAlert
{
    #[Locked]
    public string $savedDataHash;

    protected ?bool $hasUnsavedDataChangesAlert = null;

    public function mountHasUnsavedDataChangesAlert(): void
    {
        $this->rememberData();
    }

    protected function rememberData(): void
    {
        if (! $this->hasUnsavedDataChangesAlert()) {
            return;
        }

        $this->savedDataHash = md5((string) str(json_encode($this->data))->replace('\\', ''));
    }

    protected function hasUnsavedDataChangesAlert(): bool
    {
        return $this->hasUnsavedDataChangesAlert ?? Filament::hasUnsavedChangesAlerts();
    }
}
