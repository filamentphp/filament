<?php

namespace Filament\Fields;

class Textarea extends Field
{
    protected $rows = 2;

    public function rows($rows)
    {
        $this->rows = $rows;
        return $this;
    }
}