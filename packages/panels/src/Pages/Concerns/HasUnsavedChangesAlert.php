<?php

namespace Filament\Pages\Concerns;

use Filament\Facades\Filament;
use Livewire\Attributes\Locked;

trait HasUnsavedChangesAlert
{
    /**
     * @var array<string, mixed>
     */
    #[Locked]
    public array $savedData = [];

    protected ?bool $hasUnsavedChangesAlert = null;

    public function mountHasUnsavedChangesAlert(): void
    {
        $this->rememberData();
    }

    protected function rememberData(): void
    {
        if (! $this->hasUnsavedChangesAlert()) {
            return;
        }

        $this->savedData = $this->data;
    }

    protected function hasUnsavedChangesAlert(): bool
    {
        return $this->hasUnsavedChangesAlert ?? Filament::hasUnsavedChangesAlerts();
    }
}
