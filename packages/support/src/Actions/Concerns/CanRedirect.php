<?php

namespace Filament\Support\Actions\Concerns;

use Closure;

trait CanRedirect
{
    protected Closure | string | null $redirectUrl = null;

    public function redirect()
    {
        if (filled($redirect = $this->getRedirectUrl())) {
            $this->getLivewire()->redirect($redirect);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->evaluate($this->redirectUrl);
    }

    public function redirectUrl(Closure | string $redirectUrl): static
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }
}
