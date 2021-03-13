<?php

namespace Filament\Tables\RecordActions;

use Illuminate\Support\Str;

class Link extends Action
{
    use Concerns\CanCallAction;
    use Concerns\CanOpenUrl;

    protected $icon;

    protected $label;

    public function getIcon()
    {
        return $this->icon;
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

    public function getTitle()
    {
        if ($this->title === null) {
            return $this->getLabel();
        }

        return parent::getTitle();
    }

    public function hasIcon()
    {
        return $this->icon !== null;
    }

    public function icon($icon)
    {
        $this->configure(function () use ($icon) {
            $this->icon = $icon;
        });

        return $this;
    }

    public function label($label)
    {
        $this->configure(function () use ($label) {
            $this->label = $label;
        });

        return $this;
    }
}
