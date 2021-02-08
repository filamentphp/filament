<?php

namespace Filament\FieldConcerns;

trait HasId
{
    public $id;

    public function id($id)
    {
        $this->id = $id;

        return $this;
    }
}
