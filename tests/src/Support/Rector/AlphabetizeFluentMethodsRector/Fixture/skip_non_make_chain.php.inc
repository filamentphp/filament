<?php

namespace Filament\Forms\Components;

class TextInput
{
    public static function create(string $name): static
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
}

// Should skip because it doesn't start with ::make()
TextInput::create('name')
    ->sortable() ->searchable(); ?>
