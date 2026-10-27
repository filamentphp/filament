<?php

declare(strict_types=1);

namespace Filament\Tests\Upgrade\Fixtures\NumberVariables;

use function Filament\Support\format_money;
use function Filament\Support\format_number;

function format(int | float $amount, string $currency): array
{
    return [format_number($amount), format_money(currency: $currency, money: $amount, divideBy: -100), format_money($amount, $currency, 0)];
}
-----
<?php

declare(strict_types=1);

namespace Filament\Tests\Upgrade\Fixtures\NumberVariables;

use Illuminate\Support\Number;
use function Filament\Support\format_money;
use function Filament\Support\format_number;

function format(int | float $amount, string $currency): array
{
    return [(string) Number::format($amount), (string) Number::currency($amount / -100, $currency), (string) Number::currency($amount, $currency)];
}
