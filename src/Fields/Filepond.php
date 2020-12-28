<?php

namespace Filament\Fields;

class Filepond extends Field {
    public $deleteMethod;

    /**
     * @return static
     */
    public function deleteMethod(string $method): self
    {
        $this->deleteMethod = $method;
        return $this;
    }
}