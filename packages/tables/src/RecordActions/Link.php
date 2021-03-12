<?php

namespace Filament\Tables\RecordActions;

use Illuminate\Support\Str;

class Link extends Action
{
    use Concerns\CanCallAction;
    use Concerns\CanOpenUrl;

    public $label;

    public $icon;

    public $display = 'text';

    public function label($label)
    {
        $this->label = $label;

        return $this;
    }

    public function displayIconOnly()
    {
        $this->display = 'icon';

        return $this;
    }

    public function displayIconAndText()
    {
        $this->display = 'both';

        return $this;
    }

    public function icon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    public function name($name)
    {
        $this->name = $name;

        $this->label(
            (string) Str::of($this->name)
                ->kebab()
                ->replace(['-', '_', '.'], ' ')
                ->ucfirst(),
        );

        return $this;
    }
}
