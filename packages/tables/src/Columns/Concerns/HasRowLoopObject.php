<?php

namespace Filament\Tables\Columns\Concerns;

trait HasRowLoopObject
{
    protected \stdClass|null $loop = null;

    public function rowLoop(?\stdClass $loop): static
    {
        $this->loop = $loop;

        return $this;
    }

    public function getRowLoop(): ?\stdClass
    {
        return $this->loop;
    }
}
