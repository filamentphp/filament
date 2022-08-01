<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Support\HtmlString;

trait HasDescription
{
    protected string | Closure | null $description = null;

    protected string | Closure | null $descriptionPosition = 'below';

    public function description(string | Closure | HtmlString | null $description, string | Closure | null $descriptionPosition = 'below'): static
    {
        $this->description = $description;
        $this->descriptionPosition($descriptionPosition);

        return $this;
    }

    public function getDescription(): string | HtmlString | null
    {
        return $this->evaluate($this->description);
    }

    public function descriptionPosition(string | Closure | null $descriptionPosition = 'string'): static
    {
        $this->descriptionPosition = $descriptionPosition;

        return $this;
    }

    public function getDescriptionPosition(): string
    {
        return $this->evaluate($this->descriptionPosition) ?? 'below';
    }
}
