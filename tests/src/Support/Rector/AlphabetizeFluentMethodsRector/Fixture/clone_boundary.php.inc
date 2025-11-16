<?php

namespace Filament\Forms\Components;

class TextInput
{
    public static function make(string $name): static
    {
        return new static();
    }

    public function sortable(): static
    {
        return $this;
    }

    public function searchable(): static
    {
        return $this;
    }

    public function clone(): static
    {
        return clone $this;
    }

    public function placeholder(string $text): static
    {
        return $this;
    }
}

TextInput::make('name')
    ->sortable()
    ->searchable()
    ->clone()
    ->sortable()
    ->placeholder('Enter name');

?>
-----
<?php

namespace Filament\Forms\Components;

class TextInput
{
    public static function make(string $name): static
    {
        return new static();
    }

    public function sortable(): static
    {
        return $this;
    }

    public function searchable(): static
    {
        return $this;
    }

    public function clone(): static
    {
        return clone $this;
    }

    public function placeholder(string $text): static
    {
        return $this;
    }
}

TextInput::make('name')
    ->searchable()
    ->sortable()
    ->clone()
    ->placeholder('Enter name')
    ->sortable();

?>
