<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\RawJs;

class JsField extends Field
{
    use Concerns\HasJsRenderer;

    protected string | RawJs | Closure | null $renderer = null;

    /** @var array<string, mixed> | Closure | null */
    protected array | Closure | null $rendererConfiguration = null;

    /** @param array<string, mixed> | Closure | null $configuration */
    public function rendererConfiguration(array | Closure | null $configuration): static
    {
        $this->rendererConfiguration = $configuration;

        return $this;
    }

    /** @return array<string, mixed> */
    public function getRendererConfiguration(): array
    {
        return $this->evaluate($this->rendererConfiguration) ?? [];
    }

    public function renderer(string | RawJs | Closure | null $renderer): static
    {
        $this->renderer = $renderer;

        return $this;
    }

    public function getRenderer(): string | RawJs | null
    {
        return $this->evaluate($this->renderer);
    }
}
