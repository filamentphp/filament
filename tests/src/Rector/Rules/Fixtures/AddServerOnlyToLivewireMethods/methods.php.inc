<?php

namespace Filament\Tests\Rector\Fixtures\MethodAccess;

use Filament\Support\Attributes\Exposed as Frontend;
use Filament\Support\Attributes\ServerOnly;

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
    public function internal(): void {}

    public static function configuration(): void {}

    #[Frontend]
    public function action(): void {}

    #[ServerOnly]
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

use Filament\Support\Attributes\Exposed as Frontend;
use Filament\Support\Attributes\ServerOnly;

trait NestedTrait
{
    #[\Filament\Support\Attributes\ServerOnly]
    public function nested(): void {}
}

trait UsedTrait
{
    use NestedTrait;

    #[\Filament\Support\Attributes\ServerOnly]
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
    #[\Filament\Support\Attributes\ServerOnly]
    public function internal(): void {}

    #[\Filament\Support\Attributes\ServerOnly]
    public static function configuration(): void {}

    #[Frontend]
    public function action(): void {}

    #[ServerOnly]
    public function alreadyProtected(): void {}

    protected function helper(): void {}

    private function privateHelper(): void {}
}

class UnrelatedClass
{
    public function unrelated(): void {}
}
