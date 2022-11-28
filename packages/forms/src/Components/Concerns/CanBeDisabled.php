<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Arr;

trait CanBeDisabled
{
    protected bool | Closure $isDisabled = false;

    public function disabled(bool | Closure $condition = true): static
    {
        $this->isDisabled = $condition;
        $this->dehydrated(fn (Component $component): bool => ! $component->evaluate($condition));

        return $this;
    }

    /**
     * @param string | array<string> $contexts
     */
    public function disabledOn(string | array $contexts): static
    {
        $this->disabled(static function (string $context, HasForms $livewire) use ($contexts): bool {
            foreach (Arr::wrap($contexts) as $disabledContext) {
                if ($disabledContext === $context || $livewire instanceof $disabledContext) {
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
