<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Support\HtmlString;

trait HasDescription
{
    protected string | HtmlString | Closure | null $descriptionAbove = null;

    protected string | HtmlString | Closure | null $descriptionBelow = null;

    public function description(string | HtmlString | Closure | null $description, string | Closure | null $position = 'below'): static
    {
        if ($position == 'above') {
            $this->descriptionAbove = $description;
        } else {
            $this->descriptionBelow = $description;
        }

        return $this;
    }

    /**
     * @deprecated Use `description(position: 'above')` instead.
     */
    public function descriptionPosition(string $position = 'below'): static
    {
        if ($position === 'above') {
            $this->descriptionAbove = $this->descriptionBelow;
            $this->descriptionBelow = null;
        }

        return $this;
    }

    public function getDescriptionAbove(): string | HtmlString | null
    {
        return $this->evaluate($this->descriptionAbove);
    }

    public function getDescriptionBelow(): string | HtmlString | null
    {
        return $this->evaluate($this->descriptionBelow);
    }
}
