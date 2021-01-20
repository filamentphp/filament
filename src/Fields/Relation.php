<?php

namespace Filament\Fields;

class Relation extends Select
{
    public $addMethod;

    public $deleteMethod;

    public $modelDirective = 'wire:model';

    public $sortMethod;

    public function addMethod(string $method)
    {
        $this->addMethod = $method;

        return $this;
    }

    public function deleteMethod(string $method)
    {
        $this->deleteMethod = $method;

        return $this;
    }

    public function sortMethod(string $method)
    {
        $this->sortMethod = $method;

        return $this;
    }
}
