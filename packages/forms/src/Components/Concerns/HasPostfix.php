<?php

namespace Filament\Forms\Components\Concerns;

trait HasPostfix
{
    protected $postfix;

    public function getPostfix()
    {
        return $this->postfix;
    }

    public function postfix($postfix)
    {
        $this->configure(function () use ($postfix) {
            $this->postfix = $postfix;
        });

        return $this;
    }
}
