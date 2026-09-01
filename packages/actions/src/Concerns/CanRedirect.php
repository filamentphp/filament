<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Support\Facades\FilamentView;

trait CanRedirect
{
    protected string | Closure | null $failureRedirectUrl = null;

    protected string | Closure | null $successRedirectUrl = null;

    public function dispatchFailureRedirect(): static
    {
        $url = $this->evaluate($this->failureRedirectUrl) ?? $this->getHasActionsLivewire()->getDefaultActionFailureRedirectUrl($this);

        if (filled($url)) {
            $this->redirect($url);
        }

        return $this;
    }

    public function dispatchSuccessRedirect(): static
    {
        $url = $this->evaluate($this->successRedirectUrl) ?? $this->getHasActionsLivewire()->getDefaultActionSuccessRedirectUrl($this);

        if (filled($url)) {
            $this->redirect($url);
        }

        return $this;
    }

    public function redirect(string | Closure $url, ?bool $navigate = null): void
    {
        $url = $this->evaluate($url);

        $this->getLivewire()->redirect($url, navigate: $navigate ?? FilamentView::hasSpaMode($url));
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
