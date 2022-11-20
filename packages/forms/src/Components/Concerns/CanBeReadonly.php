<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Arr;

trait CanBeReadOnly
{
    protected bool | Closure $isReadOnly = false;

    public function readOnly(bool | Closure $condition = true): static
    {
        $this->isReadOnly = $condition;

        return $this;
    }

    public function readOnlyOn(string | array $contexts): static
    {
        $this->readOnly(static function (string $context, HasForms $livewire) use ($contexts): bool {
            foreach (Arr::wrap($contexts) as $readOnlyContext) {
                if ($readOnlyContext === $context || $livewire instanceof $readOnlyContext) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function isReadOnly(): bool
    {
        return $this->evaluate($this->isReadOnly);
    }
}
