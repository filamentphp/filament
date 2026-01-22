<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Operation;
use Illuminate\Support\Arr;
use Livewire\Component as LivewireComponent;

trait CanBeDisabled
{
    protected bool | Closure $isDisabled = false;

    public function disabled(bool | Closure $condition = true): static
    {
        $this->isDisabled = $condition;
        $this->saved(fn (Component $component): bool => ! $component->evaluate($condition));

        return $this;
    }

    /**
     * @param  string | Operation | array<string | Operation>  $operations
     */
    public function disabledOn(string | Operation | array $operations): static
    {
        $this->disabled(static function (LivewireComponent & HasSchemas $livewire, string $operation) use ($operations): bool {
            foreach (Arr::wrap($operations) as $disabledOperation) {
                if ($disabledOperation instanceof Operation) {
                    $disabledOperation = $disabledOperation->value;
                }

                if ($disabledOperation === $operation || $livewire instanceof $disabledOperation) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->evaluate($this->isDisabled) || $this->getContainer()->isDisabled();
    }

    public function isEnabled(): bool
    {
        return ! $this->isDisabled();
    }
}
