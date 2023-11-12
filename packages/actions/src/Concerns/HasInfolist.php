<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\Contracts\HasRecord;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Infolist;

trait HasInfolist
{
    /**
     * @var array<Component> | Closure | null
     */
    protected array | Closure | null $infolist = null;

    /**
     * @param  array<Component> | Closure | null  $infolist
     */
    public function infolist(array | Closure | null $infolist): static
    {
        $this->infolist = $infolist;

        return $this;
    }

    public function getInfolist(): ?Infolist
    {
        $infolistName = $this->getInfolistName();

        if (blank($infolistName)) {
            return null;
        }

        $infolist = Infolist::make($this->getLivewire())
            ->name($infolistName);

        if ($this instanceof HasRecord) {
            $infolist->record($this->getRecord());
        }

        $modifiedInfolist = $this->evaluate($this->infolist, [
            'infolist' => $infolist,
        ]);

        if ($modifiedInfolist === null) {
            return null;
        }

        if (is_array($modifiedInfolist) && (! count($modifiedInfolist))) {
            return null;
        }

        if (is_array($modifiedInfolist)) {
            $modifiedInfolist = $infolist->schema($modifiedInfolist);
        }

        return $modifiedInfolist;
    }

    public function getInfolistName(): ?string
    {
        return null;
    }
}
