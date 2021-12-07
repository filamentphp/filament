<?php

namespace Filament\Forms\Components\Concerns;

trait HasHelperText
{
    protected $helperText = null;

    public function helperText(string | callable $text): static
    {
        $this->helperText = $text;

        return $this;
    }

    public function getHelperText(): ?string
    {
        return $this->evaluate($this->helperText);
    }
}
