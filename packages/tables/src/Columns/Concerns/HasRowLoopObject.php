<?php

namespace Filament\Tables\Columns\Concerns;

use stdClass;

trait HasRowLoopObject
{
    protected ?stdClass $loop = null;

    public function rowLoop(?stdClass $loop): static
    {
        $this->loop = $loop;

        return $this;
    }

    public function getRowLoop(): ?stdClass
    {
        return $this->loop;
    }
}
