<?php

namespace Filament\Forms\Components;

class Placeholder extends Component
{
    protected $name;

    public function __construct($placeholderValue)
    {
        $this->name($placeholderValue);
        $this->setUp();
    }

    protected function name($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public static function make(string $placeholderValue)
    {
        return new static($placeholderValue);
    }
}
