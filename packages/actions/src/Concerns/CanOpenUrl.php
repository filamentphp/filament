<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanOpenUrl
{
    protected bool | Closure $shouldOpenUrlInNewTab = false;

    protected string | Closure | null $url = null;

    protected bool | Closure $shouldPostToUrl = false;

    public function openUrlInNewTab(bool | Closure $condition = true): static
    {
        $this->shouldOpenUrlInNewTab = $condition;

        return $this;
    }

    public function url(string | Closure | null $url, bool | Closure $shouldOpenInNewTab = false): static
    {
        $this->openUrlInNewTab($shouldOpenInNewTab);
        $this->url = $url;

        return $this;
    }

    public function postToUrl(bool | Closure $condition = true): static
    {
        $this->shouldPostToUrl = $condition;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->evaluate($this->url) ?? $this->getHasActionsLivewire()?->getDefaultActionUrl($this);
    }

    public function shouldOpenUrlInNewTab(): bool
    {
        return (bool) $this->evaluate($this->shouldOpenUrlInNewTab);
    }

    public function shouldPostToUrl(): bool
    {
        return (bool) $this->evaluate($this->shouldPostToUrl);
    }
}
