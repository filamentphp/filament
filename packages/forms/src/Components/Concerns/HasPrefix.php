<?php

namespace Filament\Forms\Components\Concerns;

trait HasPrefix
{
    public $prefix;

    public function prefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }
}
