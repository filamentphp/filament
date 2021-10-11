<?php

namespace Filament\Tables\Columns;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;

class Column
{
    use Macroable;
    use Tappable;

    public ?string $label = null;

    public string $name;

    final public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        $static = new static($name);
        $static->setUp();

        return $static;
    }

    protected function setUp(): void
    {
    }

    public function getLabel(): string
    {
        return $this->label ?? (string) Str::of($this->getName())
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }

    public function getName(): string
    {
        return $this->name;
    }
}
