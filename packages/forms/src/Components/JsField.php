<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\RawJs;

class JsField extends Field implements HasEmbeddedView
{
    use Concerns\HasJsRenderer;

    protected string | RawJs | Closure | null $renderer = null;

    /** @var array<string, mixed> | Closure | null */
    protected array | Closure | null $rendererProps = null;

    /** @param array<string, mixed> | Closure | null $props */
    public function rendererProps(array | Closure | null $props): static
    {
        $this->rendererProps = $props;

        return $this;
    }

    /** @return array<string, mixed> */
    public function getRendererProps(): array
    {
        return $this->evaluate($this->rendererProps) ?? [];
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
