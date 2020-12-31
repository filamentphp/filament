<?php

namespace Filament\Fields;

class Date extends Field {
    public $config = [
        'altInput' => true,
        'altFormat' => "F j, Y",
        'dateFormat' =>  "Y-m-d",
    ];

    /**
     * @return static
     */
    public function config(array $config): self
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }
}