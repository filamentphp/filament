<?php

namespace Filament\Tables\RecordActions;

use Illuminate\Support\Str;

class Link extends Action
{
    use Concerns\CanCallAction;
    use Concerns\CanOpenUrl;

    protected $label;

    protected $icon;

    public function label($label)
    {
        $this->configure(function () use ($label) {
            $this->label = $label;
        });

        return $this;
    }

    public function icon($icon)
    {
        $this->configure(function () use ($icon) {
            $this->icon = $icon;
        });

        return $this;
    }

    public function getLabel()
    {
        if ($this->label === null) {
            return (string) Str::of($this->getName())
                ->kebab()
                ->replace(['-', '_', '.'], ' ')
                ->ucfirst();
        }

        return $this->label;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function hasIcon()
    {
        return $this->icon !== null;
    }
}
