<?php

namespace Filament\Support\Icons;

use Illuminate\Support\Arr;

class Icon
{
    public ?string $name = null;

    public ?string $size = null;

    public ?string $color = null;

    /**
     * @var array<string | int, bool | string>
     */
    public array $class = [];

    final public function __construct(?string $name = null)
    {
        $this->name = $name;
    }

    public static function make(?string $name = null): static
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

    /**
     * @param  string | array<string | int, bool | string> | null  $class
     */
    public function class(string | array | null $class): static
    {
        $this->class = ($class === null) ? [] : Arr::wrap($class);

        return $this;
    }
}
