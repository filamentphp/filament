<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;

class Placeholder extends Component
{
    protected $name;

    protected $value;

    public function __construct($name, $value)
    {
        $this->name($name);
        $this->value($value);

        $this->setUp();
    }

    public function getLabel()
    {
        if ($this->label === null) {
            return (string) Str::of($this->getName())
                ->afterLast('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();
        }

        return parent::getLabel();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public static function make($name, $value)
    {
        return new static($name, $value);
    }

    protected function name($name)
    {
        $this->name = $name;

        return $this;
    }

    protected function value($value)
    {
        $this->value = $value;

        return $this;
    }
}
