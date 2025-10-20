<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;

trait CanOpenUrl
{
    protected bool | string | Closure $shouldOpenUrlInNewTab = false;

    protected string | Closure | null $url = null;

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

    public function getUrl(mixed $state = null): ?string
    {
        if (func_num_args() === 1) {
            return $this->hasStateBasedUrls()
                ? $this->evaluate($this->url, [
                    'state' => $state,
                ])
                : null;
        }

        if ($this->hasStateBasedUrls()) {
            return null;
        }

        return $this->evaluate($this->url);
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

    public function hasStateBasedUrls(): bool
    {
        return $this->evaluationValueIsFunctionAndHasParameter($this->url, parameterName: 'state');
    }

    public function shouldOpenUrlInNewTab(): bool
    {
        return (bool) $this->evaluate($this->shouldOpenUrlInNewTab);
    }
}
