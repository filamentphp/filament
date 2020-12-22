<?php

namespace Filament\Fields;

class Select extends Field {
    public $placeholder = 'Choose';
    public $options = [];

    /**
     * @return static
     */
    public function placeholder($placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * @return static
     */
    public function options(array $options): self
    {
        $this->options = $options;
        return $this;
    }
}