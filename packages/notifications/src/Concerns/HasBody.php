<?php

namespace Filament\Notifications\Concerns;

use Closure;

trait HasBody
{
    protected string | Closure | null $body = null;

    protected bool $shouldTranslateBody = false;

    public function body(string | Closure | null $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function translateBody(bool $shouldTranslateBody = true): static
    {
        $this->shouldTranslateBody = $shouldTranslateBody;

        return $this;
    }

    public function getBody(): ?string
    {
        $body = $this->evaluate($this->body);

        return is_string($body) && $this->shouldTranslateBody
            ? __($body)
            : $body;
    }
}
