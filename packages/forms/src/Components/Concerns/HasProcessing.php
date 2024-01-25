<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasProcessing
{
    protected string | Closure | null $processingMessage = null;

    public function processingMessage(string | Closure | null $processingMessage): static
    {
        $this->processingMessage = $processingMessage;

        return $this;
    }

    public function getProcessingMessage(): ?string
    {
        return $this->evaluate($this->processingMessage);
    }
}
