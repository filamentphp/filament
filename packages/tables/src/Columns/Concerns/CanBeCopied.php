<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Support\Concerns\CanBeCopied as BaseTrait;

trait CanBeCopied
{
    use BaseTrait;

    public function isClickDisabled(): bool
    {
        if (parent::isClickDisabled()) {
            return true;
        }

        $state = $this->getState();

        if (! is_array($state)) {
            return $this->isCopyable($state);
        }

        if (! $this->isCopyable instanceof Closure) {
            return $this->isCopyable;
        }

        foreach ($state as $item) {
            if ($this->isCopyable($item)) {
                return true;
            }
        }

        return false;
    }
}
