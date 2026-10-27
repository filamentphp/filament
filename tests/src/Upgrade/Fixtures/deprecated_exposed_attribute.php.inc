<?php

namespace Filament\Tests\Upgrade\Fixtures\ExposedAttribute;

use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\ExposedLivewireMethod as LegacyExposed;

trait ExposedMethods
{
    #[LegacyExposed]
    public function inherited(): void {}
}

class Counter
{
    use ExposedMethods;

    #[\Filament\Support\Components\Attributes\ExposedLivewireMethod]
    public function increment(): void {}

    #[Exposed]
    public function current(): void {}

    #[LegacyExposed, Exposed]
    public function duplicate(): void {}

    #[Unrelated\ExposedLivewireMethod]
    public function unrelated(): void {}
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ExposedAttribute;

use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\ExposedLivewireMethod as LegacyExposed;

trait ExposedMethods
{
    #[Exposed]
    public function inherited(): void {}
}

class Counter
{
    use ExposedMethods;

    #[Exposed]
    public function increment(): void {}

    #[Exposed]
    public function current(): void {}

    #[LegacyExposed, Exposed]
    public function duplicate(): void {}

    #[Unrelated\ExposedLivewireMethod]
    public function unrelated(): void {}
}
