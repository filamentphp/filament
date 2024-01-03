<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Arr;

trait CanBeReadOnly
{
    protected bool | Closure $isReadOnly = false;

    /**
     * Remember that the input can still be modified on the client side if edited by browser dev tools.
     * Call `->dehydrated(false)` to prevent this.
     */
    public function readOnly(bool | Closure $condition = true): static
    {
        $this->isReadOnly = $condition;

        return $this;
    }

    /**
     * @param  string | array<string>  $operations
     */
    public function readOnlyOn(string | array $operations): static
    {
        $this->readOnly(static function (HasForms $livewire, string $operation) use ($operations): bool {
            foreach (Arr::wrap($operations) as $readOnlyOperation) {
                if ($readOnlyOperation === $operation || $livewire instanceof $readOnlyOperation) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function isReadOnly(): bool
    {
        return (bool) $this->evaluate($this->isReadOnly);
    }
}
