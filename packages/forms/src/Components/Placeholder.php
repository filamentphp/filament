<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;

class Placeholder extends Component
{
    protected $name;
    protected $value;

    public function __construct(string $name, string $value)
    {
        $this->name($name);
        $this->value($value);
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

    protected function value($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
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

    public static function make(string $name, string $value)
    {
        return new static($name, $value);
    }
}
