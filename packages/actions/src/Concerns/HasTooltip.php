<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasTooltip
{
    protected string | Closure | null $tooltip = null;

    public function tooltip(string | Closure | null $tooltip): static
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function getTooltip(): ?string
    {
        if (
            $this->hasAuthorizationTooltip() &&
            ($response = $this->getAuthorizationResponseWithMessage())->denied() &&
            filled($responseMessage = $response->message())
        ) {
            return $responseMessage;
        }

        return $this->evaluate($this->tooltip);
    }
}
