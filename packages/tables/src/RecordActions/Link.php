<?php

namespace Filament\Tables\RecordActions;

use Illuminate\Support\Str;

class Link extends Action
{
    use Concerns\CanCallAction;
    use Concerns\CanOpenUrl;

    public $label;

    public function label($label)
    {
        $this->label = $label;

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
