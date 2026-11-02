<?php

namespace Filament\Tables\Columns\Concerns;

use stdClass;

trait HasRowLoop
{
    protected ?stdClass $rowLoop = null;

    public function rowLoop(?stdClass $loop): static
    {
        $this->rowLoop = $loop;

        return $this;
    }

    public function getRowLoop(): ?stdClass
    {
        return $this->rowLoop;
    }
}
