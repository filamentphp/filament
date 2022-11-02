<?php

namespace Filament\Tables\Columns\Layout;

class View extends Component
{
    final public function __construct(string $view)
    {
        $this->view($view);
    }

    public static function make(string $view): static
    {
        return app(static::class, ['view' => $view]);
    }
}
