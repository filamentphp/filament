<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Support\HtmlString;

trait HasDescription
{
    protected string | Closure | null $description = null;

    protected bool | Closure | null $showDescriptionOnTop = null;

    public function description(string | Closure | HtmlString | null $description, bool | Closure | null $showDescriptionOnTop = false): static
    {
        $this->description = $description;
        $this->showDescriptionOnTop($showDescriptionOnTop);

        return $this;
    }

    public function getDescription(): string | HtmlString | null
    {
        return $this->evaluate($this->description);
    }

    public function showDescriptionOnTop(bool | Closure | null $showDescriptionOnTop = true): static
    {
        $this->showDescriptionOnTop = $showDescriptionOnTop;

        return $this;
    }

    public function getShowDescriptionOnTop(): bool
    {
        return $this->evaluate($this->showDescriptionOnTop) ?? false;
    }
}
