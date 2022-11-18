<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Arr;

trait CanBeReadonly
{
    protected bool | Closure $isReadonly = false;

    public function readonly(bool | Closure $condition = true): static
    {
        $this->isReadonly = $condition;

        return $this;
    }

    public function readonlyOn(string | array $contexts): static
    {
        $this->readonly(static function (string $context, HasForms $livewire) use ($contexts): bool {
            foreach (Arr::wrap($contexts) as $readonlyContext) {
                if ($readonlyContext === $context || $livewire instanceof $readonlyContext) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function isReadonly(): bool
    {
        return $this->evaluate($this->isReadonly);
    }
}
