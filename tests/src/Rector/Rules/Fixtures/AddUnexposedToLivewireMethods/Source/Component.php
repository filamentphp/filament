<?php

namespace Filament\Tests\Rector\Fixtures\MethodAccess;

use Attribute;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;

trait ExposedMethods
{
    #[Exposed]
    public function traitAction(): void {}
}

trait ShadowedExposedMethods
{
    use ExposedMethods;

    public function traitAction(): void {}
}

class Component extends \Livewire\Component
{
    use ShadowedExposedMethods {
        traitAction as inheritedTraitAction;
    }
    use UsedTrait;

    public function inheritedTraitAction(): void {}

    #[Exposed]
    public function inheritedAction(): void {}

    #[Exposed, Unexposed]
    public function blockedAction(): void {}
}

#[Attribute(Attribute::TARGET_METHOD)]
class FrontendAction extends Exposed {}
