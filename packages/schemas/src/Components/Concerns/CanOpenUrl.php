<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait CanOpenUrl
{
    protected bool | Closure $shouldOpenUrlInNewTab = false;

    protected string | Closure | null $url = null;

    public function openUrlInNewTab(bool | Closure $condition = true): static
    {
        $this->shouldOpenUrlInNewTab = $condition;

        return $this;
    }

    public function url(string | Closure | null $url, bool | Closure | null $shouldOpenInNewTab = null): static
    {
        // Security: If this URL is derived from user input, validate it
        // to prevent XSS via `javascript:` protocol URLs rendered
        // in `href` attributes.

        if ($shouldOpenInNewTab !== null) {
            $this->openUrlInNewTab($shouldOpenInNewTab);
        }

        $this->url = $url;

        return $this;
    }

    public function getUrl(mixed $state = null, ?Model $relatedRecord = null): ?string
    {
        $isItemCall = func_num_args() > 0;

        return match (true) {
            $isItemCall !== $this->hasStateBasedUrls() => null,
            $isItemCall => $this->evaluate($this->url, [
                'state' => $state,
                'relatedRecord' => $relatedRecord,
            ]),
            default => $this->evaluate($this->url),
        };
    }

    public function hasStateBasedUrls(): bool
    {
        return $this->evaluationValueIsFunctionAndHasParameter($this->url, 'state')
            || $this->evaluationValueIsFunctionAndHasParameter($this->url, 'relatedRecord');
    }

    public function shouldOpenUrlInNewTab(): bool
    {
        return (bool) $this->evaluate($this->shouldOpenUrlInNewTab);
    }
}
