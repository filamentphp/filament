<?php

namespace Filament\Tests\Upgrade\Fixtures\ApiProperties;

use Filament\Schemas\Components\StateCasts\BooleanStateCast;

class ConfiguredCast extends BooleanStateCast
{
    public bool $isStoredAsInt = true;

    public function __construct() {}
}

function inspect(?BooleanStateCast $cast): ?bool
{
    return $cast?->isStoredAsInt;
}

function result(): array
{
    $cast = new ConfiguredCast;

    return [$cast->isStoredAsInt, inspect($cast), inspect(null)];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ApiProperties;

use Filament\Schemas\Components\StateCasts\BooleanStateCast;

class ConfiguredCast extends BooleanStateCast
{
    public bool $isStoredAsInteger = true;

    public function __construct() {}
}

function inspect(?BooleanStateCast $cast): ?bool
{
    return $cast?->isStoredAsInteger;
}

function result(): array
{
    $cast = new ConfiguredCast;

    return [$cast->isStoredAsInteger, inspect($cast), inspect(null)];
}
