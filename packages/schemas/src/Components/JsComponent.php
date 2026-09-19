<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Support\RawJs;

class JsComponent extends Component
{
    use Concerns\HasJsRenderer;

    protected string | RawJs | Closure | null $renderer = null;

    /** @var array<string, mixed> | Closure | null */
    protected array | Closure | null $rendererProps = null;

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

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
