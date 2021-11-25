<?php

namespace Filament\Pages\Actions\Concerns;

trait CanOpenUrl
{
    protected bool $shouldOpenUrlInNewTab = false;

    protected $url = null;

    public function openUrlInNewTab(bool $condition = true): static
    {
        $this->shouldOpenUrlInNewTab = $condition;

        return $this;
    }

    public function url(string | callable | null $url, bool $shouldOpenInNewTab = false): static
    {
        $this->shouldOpenUrlInNewTab = $shouldOpenInNewTab;
        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return value($this->url);
    }

    public function shouldOpenUrlInNewTab(): bool
    {
        return $this->shouldOpenUrlInNewTab;
    }
}
