<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Support\Str;

trait CanGenerateUuids
{
    protected ?Closure $generateUuidUsing = null;

    public function generateUuidUsing(?Closure $callback): static
    {
        $this->generateUuidUsing = $callback;

        return $this;
    }

    public function generateUuid(): string
    {
        if ($this->generateUuidUsing) {
            return $this->evaluate($this->generateUuidUsing);
        }

        return (string) Str::uuid();
    }
}
