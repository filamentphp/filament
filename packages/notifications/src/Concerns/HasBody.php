<?php

namespace Filament\Notifications\Concerns;

use Closure;
use Illuminate\Support\HtmlString;

trait HasBody
{
    protected string | HtmlString | Closure | null $body = null;

    public function body(string | HtmlString | Closure | null $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->evaluate($this->body);
    }
}
