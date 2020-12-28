<?php

namespace Filament\Fields;

class File extends Field {
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