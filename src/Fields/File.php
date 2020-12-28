<?php

namespace Filament\Fields;

class File extends Field {
    public $sortMethod;
    public $deleteMethod;

    /**
     * @return static
     */
    public function sortMethod(string $method): self
    {
        $this->sortMethod = $method;
        return $this;
    }

    /**
     * @return static
     */
    public function deleteMethod(string $method): self
    {
        $this->deleteMethod = $method;
        return $this;
    }
}