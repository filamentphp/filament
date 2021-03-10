<?php

namespace Filament\Tables\Columns\Concerns;

trait CanOpenUrl
{
    public $shouldOpenUrlInNewTab = false;

    public $url;

    public function getUrl($record)
    {
        if ($this->url === null) return null;

        if (is_callable($this->url)) {
            $callback = $this->url;

            return $callback($record);
        }

        return $this->url;
    }

    public function openUrlInNewTab()
    {
        $this->shouldOpenUrlInNewTab = true;

        return $this;
    }

    public function url($url, $shouldOpenInNewTab = false)
    {
        $this->url = $url;
        if ($shouldOpenInNewTab) $this->openUrlInNewTab();

        return $this;
    }
}
