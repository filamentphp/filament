<?php

namespace Filament\Forms\Components;

class Checkbox extends Field
{
    use Concerns\CanBeAccepted;
    use Concerns\CanBeInline;
    use Concerns\HasExtraInputAttributes;

    protected string $view = 'forms::components.checkbox';

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(false);

        $this->afterStateHydrated(static function (Checkbox $component, $state): void {
            $component->state((bool) $state);
        });

        $this->rule('boolean');
    }
}
