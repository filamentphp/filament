<?php

namespace Filament\Forms\Components\Concerns;

trait CanBeConditionallyModified
{
    public function if(bool | callable $condition, callable $then, ?callable $default = null): static
    {
        $result = $this->evaluate($condition);
        $default ??= fn () => [];

        if ($result) {
            $this->evaluate($then);
        } else {
            $this->evaluate($default);
        }

        return $this;
    }
}
