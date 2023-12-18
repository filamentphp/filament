<?php

namespace Filament\Panel\Concerns;

trait HasDefaultThemeMode
{
    protected string $defaultThemeMode = "system";

    public function defaultThemeMode(string $mode): static
    {
        $this->defaultThemeMode = $mode;

        return $this;
    }

    public function getDefaultThemeMode(): string
    {
        return $this->defaultThemeMode;
    }
}
