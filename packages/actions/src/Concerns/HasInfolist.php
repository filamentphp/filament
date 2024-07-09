<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Schema\Components\Component;

trait HasInfolist
{
    /**
     * @param  array<Component> | Closure | null  $infolist
     */
    public function infolist(array | Closure | null $infolist): static
    {
        $this->schema($infolist);

        return $this;
    }
}
