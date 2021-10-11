<?php

namespace Filament\Tables\Columns\Concerns;

trait CanOpenUrl
{
    protected $shouldUrlOpenInNewTab = false;

    protected $url;

    public function getUrl($record)
    {
        $url = $this->url;

        if (
            $url === null &&
            $this->isPrimary() &&
            $this->getTable()->getPrimaryColumnUrl()
        ) {
            $url = $this->getTable()->getPrimaryColumnUrl();
        }

        if (is_callable($url)) {
            $callback = $url;

            return $callback($record);
        }

        return $url;
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
        if (
            $this->url === null &&
            $this->isPrimary() &&
            $this->getTable()->getPrimaryColumnUrl()
        ) {
            return $this->getTable()->shouldPrimaryColumnUrlOpenInNewTab();
        }

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
