<?php

namespace Filament\Tables\Actions\Concerns;

trait CanDeselectRecordsAfterCompletion
{
    protected bool $shouldDeselectRecordsAfterCompletion = false;

    public function deselectRecordsAfterCompletion(bool $condition = true): static
    {
        $this->shouldDeselectRecordsAfterCompletion = $condition;

        return $this;
    }

    public function shouldDeselectRecordsAfterCompletion(): bool
    {
        return $this->shouldDeselectRecordsAfterCompletion;
    }
}
