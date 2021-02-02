<?php

namespace Filament\Fields;

class File extends InputField
{
    public $deleteMethod;

    public $sortMethod;

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
