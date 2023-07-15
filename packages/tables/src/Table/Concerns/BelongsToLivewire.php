<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables\Contracts\HasTable;

trait BelongsToLivewire
{
    protected HasTable $livewire;

    public function livewire(HasTable $livewire): static
    {
        $this->livewire = $livewire;

        return $this;
    }

    public function getLivewire(): HasTable
    {
        return $this->livewire;
    }

    public function makeTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return $this->getLivewire()->makeFilamentTranslatableContentDriver();
    }
}
