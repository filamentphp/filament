<?php

namespace Filament\Tables\Columns\Concerns;

trait HasLoopObject
{
    protected \stdClass|null $loop = null;

    public function loop(?\stdClass $loop): static
    {
        $this->loop = $loop;

        return $this;
    }

    public function getLoop(): ?\stdClass
    {
        return $this->loop;
    }
}
