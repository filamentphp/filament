<?php

namespace Filament\Tables\Columns\Concerns;

use stdClass;

trait HasRowLoop
{
    protected ?stdClass $rowLoop = null;

    public function rowLoop(?stdClass $rowLoop): static
    {
        $this->rowLoop = $rowLoop;

        return $this;
    }

    public function getRowLoop(): ?stdClass
    {
        return $this->rowLoop;
    }
}
