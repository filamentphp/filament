<?php

namespace Filament\Forms\Components;

use Filament\Support\Concerns\HasExtraAlpineAttributes;

class Toggle extends Field
{
    use Concerns\CanBeAccepted;
    use Concerns\CanBeInline;
    use Concerns\CanFixIndistinctState;
    use Concerns\HasToggleColors;
    use Concerns\HasToggleIcons;
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.toggle';

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(false);

        $this->afterStateHydrated(static function (Toggle $component, $state): void {
            $component->state((bool) $state);
        });

        $this->rule('boolean');
    }
}
