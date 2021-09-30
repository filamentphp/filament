<?php

namespace Filament\Tables\RecordActions;

class Icon extends Action
{
    use Concerns\CanCallAction;
    use Concerns\CanOpenUrl;

    protected $icon;

    public function getIcon()
    {
        return $this->icon;
    }

    public function icon($icon)
    {
        $this->configure(function () use ($icon) {
            $this->icon = $icon;
        });

        return $this;
    }
}
