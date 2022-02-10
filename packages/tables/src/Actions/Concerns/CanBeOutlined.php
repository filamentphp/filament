<?php

namespace Filament\Tables\Actions\Concerns;

use Closure;

trait CanBeOutlined
{
    protected bool | Closure $isOutlined = false;

    public function outlined(bool | Closure $isOutlined): static
    {
        $this->isOutlined = $isOutlined;

        return $this;
    }

    public function isOutlined(): bool
    {
        return $this->evaluate($this->isOutlined);
    }
}
