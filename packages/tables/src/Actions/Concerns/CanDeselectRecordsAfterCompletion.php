<?php

namespace Filament\Tables\Actions\Concerns;

use Closure;

trait CanDeselectRecordsAfterCompletion
{
    protected bool | Closure $shouldDeselectRecordsAfterCompletion = false;

    public function deselectRecordsAfterCompletion(bool | Closure $condition = true): static
    {
        $this->shouldDeselectRecordsAfterCompletion = $condition;

        return $this;
    }

    public function shouldDeselectRecordsAfterCompletion(): bool
    {
        return (bool) $this->evaluate($this->shouldDeselectRecordsAfterCompletion);
    }
}
