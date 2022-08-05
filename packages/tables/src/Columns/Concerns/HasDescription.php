<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Support\HtmlString;

trait HasDescription
{
    protected string | HtmlString | Closure | null $description = null;

    protected string | Closure | null $descriptionPosition = null;

    public function description(string | HtmlString | Closure | null $description, string | Closure | null $position = 'below'): static
    {
        $this->description = $description;
        $this->descriptionPosition($position);

        return $this;
    }

    public function descriptionPosition(string | Closure | null $position): static
    {
        $this->descriptionPosition = $position;

        return $this;
    }

    public function getDescription(): string | HtmlString | null
    {
        return $this->evaluate($this->description);
    }

    public function getDescriptionPosition(): string
    {
        return $this->evaluate($this->descriptionPosition) ?? 'below';
    }
}
