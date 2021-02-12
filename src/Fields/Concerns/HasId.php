<?php

namespace Filament\Fields\Concerns;

trait HasId
{
    public $id;

    public function id($id)
    {
        $this->id = $id;

        return $this;
    }
}
