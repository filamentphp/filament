<?php

namespace Filament\Tables\Actions\Concerns;

use Closure;

trait CanOpenUrl
{
    protected bool $shouldOpenUrlInNewTab = false;

    protected string | Closure | null $url = null;

    public function openUrlInNewTab(bool $condition = true): static
    {
        $this->shouldOpenUrlInNewTab = $condition;

        return $this;
    }

    public function url(string | Closure | null $url, bool $shouldOpenInNewTab = false): static
    {
        $this->shouldOpenUrlInNewTab = $shouldOpenInNewTab;
        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        if ($this->url instanceof Closure) {
            return app()->call($this->url, [
                'livewire' => $this->getLivewire(),
                'record' => $this->getRecord(),
            ]);
        }

        return $this->url;
    }

    public function shouldOpenUrlInNewTab(): bool
    {
        return $this->shouldOpenUrlInNewTab;
    }
}
