<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

trait HasHeader
{
    protected string | Htmlable | Closure | null $heading = null;

    protected View | Htmlable | Closure | null $header = null;

    protected string | Htmlable | Closure | null $description = null;

    public function description(string | Htmlable | Closure | null $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function header(View | Htmlable | Closure | null $header): static
    {
        $this->header = $header;

        return $this;
    }

    public function heading(string | Htmlable | Closure | null $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getHeader(): View | Htmlable | null
    {
        return $this->evaluate($this->header);
    }

    public function getHeading(): string | Htmlable | null
    {
        return $this->evaluate($this->heading);
    }

    public function getDescription(): string | Htmlable | null
    {
        return $this->evaluate($this->description);
    }
}
