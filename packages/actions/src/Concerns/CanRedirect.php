<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanRedirect
{
    protected string | Closure | null $failureRedirectUrl = null;

    protected string | Closure | null $successRedirectUrl = null;

    public function dispatchFailureRedirect(): static
    {
        $url = $this->evaluate($this->failureRedirectUrl);

        if (filled($url)) {
            $this->redirect($url);
        }

        return $this;
    }

    public function dispatchSuccessRedirect(): static
    {
        $url = $this->evaluate($this->successRedirectUrl);

        if (filled($url)) {
            $this->redirect($url);
        }

        return $this;
    }

    public function redirect(string | Closure $url): void
    {
        $this->getLivewire()->redirect($this->evaluate($url));
    }

    public function failureRedirectUrl(string | Closure | null $url): static
    {
        $this->failureRedirectUrl = $url;

        return $this;
    }

    public function successRedirectUrl(string | Closure | null $url): static
    {
        $this->successRedirectUrl = $url;

        return $this;
    }
}
