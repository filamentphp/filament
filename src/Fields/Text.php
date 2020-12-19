<?php

namespace Filament\Fields;

class Text extends Field {
    public $type = 'text';

    /**
     * @return static
     */
    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }
}