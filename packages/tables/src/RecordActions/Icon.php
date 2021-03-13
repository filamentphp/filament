<?php

namespace Filament\Tables\RecordActions;

class Icon extends Action
{
    use Concerns\CanCallAction;
    use Concerns\CanOpenUrl;

    protected $icon;

    protected $title;

    public function icon($icon)
    {
        $this->configure(function () use ($icon) {
            $this->icon = $icon;
        });

        return $this;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function title($title)
    {
        $this->configure(function () use ($title) {
            $this->title = $title;
        });

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }
}
