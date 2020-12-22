<?php

namespace Filament\Fields;

class Checkbox extends Field {
    public $type = 'checkbox';
    public $showErrors = true;

    /**
     * @return static
     */
    public function type($type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return static
     */
    public function hideErrorOutput(): self
    {
        $this->showErrors = false;
        return $this;
    }
}