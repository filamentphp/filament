<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Support\HtmlString;

trait HasDescription
{
    protected string | HtmlString | Closure | null $descriptionAbove = null;

    protected string | HtmlString | Closure | null $descriptionBelow = null;

    protected string | Closure | null $descriptionPosition = null;

    public function description(string | HtmlString | Closure | null $description, string | Closure | null $position = 'below'): static
    {
        if ($position == 'above') {
            $this->descriptionAbove($description);
        } else {
            $this->descriptionBelow($description);
        }

        return $this;
    }

    public function descriptionAbove(string | HtmlString | Closure | null $descriptionAbove): static
    {
        $this->descriptionAbove = $descriptionAbove;

        return $this;
    }

    public function descriptionBelow(string | HtmlString | Closure | null $descriptionBelow): static
    {
        $this->descriptionBelow = $descriptionBelow;

        return $this;
    }

    /**
     * @deprecated use descriptionAbove() or descriptionBelow()
     */
    public function descriptionPosition(string | Closure | null $position): static
    {
        $this->descriptionPosition = $position;

        return $this;
    }

    /**
     * @deprecated use getDescriptionAbove() or getDescriptionBelow()
     */
    public function getDescription(): string | HtmlString | null
    {
        if ($this->descriptionPosition == 'above') {
            return $this->getDescriptionAbove();
        } else {
            return $this->getDescriptionBelow();
        }
    }

    public function getDescriptionAbove(): string | HtmlString | null
    {
        return $this->evaluate($this->descriptionAbove);
    }

    public function getDescriptionBelow(): string | HtmlString | null
    {
        return $this->evaluate($this->descriptionBelow);
    }

    /**
     * @deprecated use direct getDescriptionAbove() or getDescriptionBelow()
     */
    public function getDescriptionPosition(): ?string
    {
        return $this->evaluate($this->descriptionPosition);
    }
}
