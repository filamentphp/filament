<?php

declare(strict_types=1);

namespace Filament\Tests\Upgrade\Fixtures\NumberHelpers;

use function Filament\Support\format_money as money;
use function Filament\Support\format_number as decimal;

function results(): array
{
    return [
        decimal(1234.5),
        decimal(number: -1234.5),
        \Filament\Support\format_number(+42),
        money(123450, 'USD', 100),
        money(-42.5, 'USD'),
        money(125, 'GBP', 0),
        money(125, 'EUR', -100),
        money(divideBy: 100, currency: 'USD', money: 98765),
        money(currency: '', money: 12.5, divideBy: +0),
        money(money: 23, currency: 'INVALID', divideBy: -0),
    ];
}
-----
<?php

declare(strict_types=1);

namespace Filament\Tests\Upgrade\Fixtures\NumberHelpers;

use Illuminate\Support\Number;
use function Filament\Support\format_money as money;
use function Filament\Support\format_number as decimal;

function results(): array
{
    return [
        (string) Number::format(1234.5),
        (string) Number::format(-1234.5),
        (string) Number::format(+42),
        (string) Number::currency(123450 / 100, 'USD'),
        (string) Number::currency(-42.5, 'USD'),
        (string) Number::currency(125, 'GBP'),
        (string) Number::currency(125 / -100, 'EUR'),
        (string) Number::currency(98765 / 100, 'USD'),
        (string) Number::currency(12.5, ''),
        (string) Number::currency(23, 'INVALID'),
    ];
}
