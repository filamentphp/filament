<?php

namespace Filament\Tables\Columns\Concerns;

trait CanOpenUrl
{
    protected $shouldOpenUrlInNewTab = false;

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
            $this->shouldOpenUrlInNewTab = true;
        });

        return $this;
    }

    public function shouldOpenUrlInNewTab()
    {
        return $this->shouldOpenUrlInNewTab;
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
