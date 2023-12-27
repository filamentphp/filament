<?php

namespace Filament\Pages\Concerns;

use Filament\Facades\Filament;
use Livewire\Attributes\Locked;

trait HasUnsavedDataChangesAlert
{
    /**
     * @var array<string, mixed>
     */
    #[Locked]
    public array $savedData = [];

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

        $this->savedData = $this->data;
    }

    protected function hasUnsavedDataChangesAlert(): bool
    {
        return $this->hasUnsavedDataChangesAlert ?? Filament::hasUnsavedChangesAlerts();
    }
}
