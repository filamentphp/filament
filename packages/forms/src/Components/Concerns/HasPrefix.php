<?php

namespace Filament\Forms\Components\Concerns;

trait HasPrefix
{
    protected $prefix;

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function prefix($prefix)
    {
        $this->configure(function () use ($prefix) {
            $this->prefix = $prefix;
        });

        return $this;
    }
}
