<?php

namespace Filament\Forms\Components;

use Filament\Support\Concerns\HasExtraAlpineAttributes;

class Slider extends Field
{
//    use Concerns\CanBeAccepted;
//    use Concerns\CanBeInline;
//    use Concerns\CanFixIndistinctState;
//    use Concerns\HasToggleColors;
//    use Concerns\HasToggleIcons;
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.slider';

    protected function setUp(): void
    {
        parent::setUp();

//        $this->default(false);
//
//        $this->afterStateHydrated(static function (Slider $component, $state): void {
//            $component->state((bool) $state);
//        });
//
//        $this->rule('boolean');
    }
}

