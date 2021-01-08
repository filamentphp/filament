<?php

namespace Filament\Fields;

class Relation extends Select {
    public $modelDirective = 'wire:model';
    public $addMethod;
    public $deleteMethod;
    public $sortMethod;

    /**
     * @return static
     */
    public function addMethod(string $method): self
    {
        $this->addMethod = $method;
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

    /**
     * @return static
     */
    public function sortMethod(string $method): self
    {
        $this->sortMethod = $method;
        return $this;
    }
}