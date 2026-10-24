<?php

namespace Filament\Tests\Rector\Fixtures\MethodAccess;

use Filament\Support\Components\Attributes\Exposed as Frontend;
use Filament\Support\Attributes\Unexposed;

trait NestedTrait
{
    public function nested(): void {}
}

trait UsedTrait
{
    use NestedTrait;

    public function internal(): void {}

    #[Frontend]
    public function action(): void {}
}

trait UnusedTrait
{
    public function unrelated(): void {}
}

class ChildComponent extends Component
{
    use ShadowedExposedMethods {
        traitAction as aliasedTraitAction;
    }

    public function traitAction(): void {}

    public function aliasedTraitAction(): void {}

    public function inheritedTraitAction(): void {}

    public function internal(): void {}

    public static function configuration(): void {}

    public function inheritedAction(): void {}

    public function blockedAction(): void {}

    #[FrontendAction]
    public function customAction(): void {}

    #[Frontend]
    public function action(): void {}

    #[Unexposed]
    public function alreadyProtected(): void {}

    protected function helper(): void {}

    private function privateHelper(): void {}
}

class UnrelatedClass
{
    public function unrelated(): void {}
}
-----
<?php

namespace Filament\Tests\Rector\Fixtures\MethodAccess;

use Filament\Support\Components\Attributes\Exposed as Frontend;
use Filament\Support\Attributes\Unexposed;

trait NestedTrait
{
    #[\Filament\Support\Attributes\Unexposed]
    public function nested(): void {}
}

trait UsedTrait
{
    use NestedTrait;

    #[\Filament\Support\Attributes\Unexposed]
    public function internal(): void {}

    #[Frontend]
    public function action(): void {}
}

trait UnusedTrait
{
    public function unrelated(): void {}
}

class ChildComponent extends Component
{
    use ShadowedExposedMethods {
        traitAction as aliasedTraitAction;
    }

    public function traitAction(): void {}

    public function aliasedTraitAction(): void {}

    public function inheritedTraitAction(): void {}

    #[\Filament\Support\Attributes\Unexposed]
    public function internal(): void {}

    #[\Filament\Support\Attributes\Unexposed]
    public static function configuration(): void {}

    public function inheritedAction(): void {}

    #[\Filament\Support\Attributes\Unexposed]
    public function blockedAction(): void {}

    #[FrontendAction]
    public function customAction(): void {}

    #[Frontend]
    public function action(): void {}

    #[Unexposed]
    public function alreadyProtected(): void {}

    protected function helper(): void {}

    private function privateHelper(): void {}
}

class UnrelatedClass
{
    public function unrelated(): void {}
}
