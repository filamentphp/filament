<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

trait HasContent
{
    protected View | Htmlable | Closure | null $content = null;

    protected View | Htmlable | Closure | null $contentFooter = null;

    /**
     * @var array<string, int | null> | Closure | null
     */
    protected array | Closure | null $contentGrid = null;

    public function content(View | Htmlable | Closure | null $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function contentFooter(View | Htmlable | Closure | null $footer): static
    {
        $this->contentFooter = $footer;

        return $this;
    }

    /**
     * @param  array<string, int | null> | Closure | null  $grid
     */
    public function contentGrid(array | Closure | null $grid): static
    {
        $this->contentGrid = $grid;

        return $this;
    }

    public function getContent(): View | Htmlable | null
    {
        return $this->evaluate($this->content);
    }

    /**
     * @return array<string, int | null> | null
     */
    public function getContentGrid(): ?array
    {
        return $this->evaluate($this->contentGrid);
    }

    public function getContentFooter(): View | Htmlable | null
    {
        return $this->evaluate($this->contentFooter);
    }
}
