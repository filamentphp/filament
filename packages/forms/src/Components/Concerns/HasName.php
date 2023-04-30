<?php

namespace Filament\Forms\Components\Concerns;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

trait HasName
{
    protected string $name;

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string | Htmlable | null
    {
        $label = parent::getLabel() ?? (string) Str::of($this->getName())
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();

        return (is_string($label) && $this->shouldTranslateLabel) ?
            __($label) :
            $label;
    }
}
