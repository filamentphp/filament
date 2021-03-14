<?php

namespace Filament\Tables\RecordActions\Concerns;

trait CanOpenUrl
{
    protected $shouldUrlOpenInNewTab = false;

    protected $url;

    public function getUrl($record)
    {
        if (is_callable($this->url)) {
            $callback = $this->url;

            return $callback($record);
        }

        return $this->url;
    }

    public function openUrlInNewTab()
    {
        $this->configure(function () {
            $this->shouldUrlOpenInNewTab = true;
        });

        return $this;
    }

    public function shouldUrlOpenInNewTab()
    {
        return $this->shouldUrlOpenInNewTab;
    }

    public function url($url, $shouldOpenInNewTab = false)
    {
        $this->configure(function () use ($shouldOpenInNewTab, $url) {
            $this->url = $url;
            if ($shouldOpenInNewTab) {
                $this->openUrlInNewTab();
            }
        });

        return $this;
    }
}
