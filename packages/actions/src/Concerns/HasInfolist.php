<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;

trait HasInfolist
{
    /**
     * @deprecated Use `schema() instead.
     *
     * @param  array<Component | Action | ActionGroup> | Closure | null  $infolist
     */
    public function infolist(array | Closure | null $infolist): static
    {
        $this->schema($infolist);

        return $this;
    }
}
