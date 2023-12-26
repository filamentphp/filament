<?php

namespace Filament\Pages\Concerns;

use Livewire\Attributes\Locked;

trait CanRememberSavedData
{
    /**
     * @var array<string, mixed>
     */
    #[Locked]
    public array $savedData = [];

    public function mountCanRememberSavedData(): void
    {
        $this->rememberData();
    }

    protected function rememberData(): void
    {
        $this->savedData = $this->data;
    }
}
