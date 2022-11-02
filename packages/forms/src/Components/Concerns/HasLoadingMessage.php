<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasLoadingMessage
{
    protected string | Closure | null $loadingMessage = null;

    public function loadingMessage(string | Closure | null $message): static
    {
        $this->loadingMessage = $message;

        return $this;
    }

    public function getLoadingMessage(): string
    {
        return $this->evaluate($this->loadingMessage) ?? __('forms::components.select.loading_message');
    }
}
