<?php

namespace Filament\Forms\Components;

class CheckboxList extends Field
{
    use Concerns\HasOptions;

    protected string $view = 'forms::components.checkbox-list';

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);

        $this->afterStateHydrated(static function (CheckboxList $component, $state) {
            if (is_array($state)) {
                return;
            }

            $component->state([]);
        });
    }
}
