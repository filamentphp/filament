<?php

namespace Filament\Support\Icons;

use Illuminate\Support\Arr;

class Icon
{
    public string $name;

    public ?string $size = null;

    public ?string $color = null;

    public array $class = [];

    final public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function size(?string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function color(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function class(string | array | null $class): static
    {
        $this->class = ($class === null) ? [] : Arr::wrap($class);

        return $this;
    }
}
