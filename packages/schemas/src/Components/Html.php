<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Illuminate\Contracts\Support\Htmlable;

class Html extends Component implements HasEmbeddedView
{
    protected string | Htmlable | Closure | null $content = null;

    final public function __construct(string | Htmlable | Closure | null $content)
    {
        $this->content($content);
    }

    public static function make(string | Htmlable | Closure | null $content): static
    {
        $static = app(static::class, ['content' => $content]);
        $static->configure();

        return $static;
    }

    public function content(string | Htmlable | Closure | null $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getContent(): string | Htmlable | null
    {
        return $this->evaluate($this->content);
    }

    public function toEmbeddedHtml(): string
    {
        $content = $this->getContent();

        if ($content instanceof Htmlable) {
            return $content->toHtml();
        }

        return $content ?? '';
    }
}
