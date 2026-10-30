<?php

namespace Filament\Tests\Upgrade\Fixtures\ApiPropertyCollisions;

use Filament\Schemas\Components\StateCasts\BooleanStateCast;

class IndependentProperty extends BooleanStateCast
{
    public bool $isStoredAsInt = true;

    public function __construct(bool $isStoredAsInt = true)
    {
        $isStoredAsInteger = false;
        $this->isStoredAsInt = $isStoredAsInt;
    }
}

function results(): array
{
    $cast = new IndependentProperty(isStoredAsInt: true);

    return [$cast->isStoredAsInt, $cast?->isStoredAsInt];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ApiPropertyCollisions;

use Filament\Schemas\Components\StateCasts\BooleanStateCast;

class IndependentProperty extends BooleanStateCast
{
    public bool $isStoredAsInteger = true;

    public function __construct(bool $isStoredAsInt = true)
    {
        $isStoredAsInteger = false;
        $this->isStoredAsInteger = $isStoredAsInt;
    }
}

function results(): array
{
    $cast = new IndependentProperty(isStoredAsInt: true);

    return [$cast->isStoredAsInteger, $cast?->isStoredAsInteger];
}
