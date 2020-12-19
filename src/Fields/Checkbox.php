<?php

namespace Filament\Fields;

class Checkbox extends Field {
    public $type = 'checkbox';

    /**
     * @return static
     */
    public function type($type): self
    {
        $this->type = $type;
        return $this;
    }
}