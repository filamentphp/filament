<?php

namespace Filament\Forms\Components;

use Filament\Support\Concerns\HasExtraAlpineAttributes;

class Tabs extends Component
{
    use HasExtraAlpineAttributes;

    protected string $view = 'forms::components.tabs';

    final public function __construct(string $label)
    {
        $this->label($label);
    }

    public static function make(string $label): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    public function tabs(array $tabs): static
    {
        $this->childComponents($tabs);

        return $this;
    }
}
