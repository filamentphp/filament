<?php

namespace Filament\Traits\FieldConcerns;

trait CanHaveId
{
    public $id;

    public function id($id)
    {
        $this->id = $id;

        return $this;
    }
}
