<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanOpenUrl
{
    protected bool | string | Closure $shouldOpenUrlInNewTab = false;

    protected string | Closure | null $url = null;

    protected bool | Closure $shouldPostToUrl = false;

    public function openUrlInNewTab(bool | string | Closure $condition = true): static
    {
        $this->shouldOpenUrlInNewTab = $condition;

        return $this;
    }

    public function url(string | Closure | null $url, bool | string | Closure $shouldOpenInNewTab = false): static
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

    public function getUrlTarget(): ?string
    {
        $value = $this->evaluate($this->shouldOpenUrlInNewTab);

        if (is_string($value) && $value !== '') {
            return $value;
        }

        if ($value) {
            return '_blank';
        }

        return null;
    }

    public function shouldPostToUrl(): bool
    {
        return (bool) $this->evaluate($this->shouldPostToUrl);
    }
}
