<?php

declare(strict_types=1);

namespace Filament\Tests\Upgrade\Fixtures\StrictNumberHelpers;

use function Filament\Support\format_money;
use function Filament\Support\format_number;

function manual($amount): void
{
    format_money($amount, 'USD', 100);
    format_money(1234, 'USD', 0.5);
    format_money(1234, 'USD', '100');
    format_money('1234', 'USD', 100);
    format_money(1234, 123, 100);
    format_number('1234');
    format_number(true);
    format_number(1234, sideEffect());
    $money = format_money(...);
    $number = format_number(...);
}
-----
<?php

declare(strict_types=1);

namespace Filament\Tests\Upgrade\Fixtures\StrictNumberHelpers;

use Illuminate\Support\Number;
use function Filament\Support\format_money;
use function Filament\Support\format_number;

function manual($amount): void
{
    Number::currency($amount / 100, 'USD');
    format_money(1234, 'USD', 0.5);
    format_money(1234, 'USD', '100');
    format_money('1234', 'USD', 100);
    format_money(1234, 123, 100);
    format_number('1234');
    format_number(true);
    format_number(1234, sideEffect());
    $money = format_money(...);
    $number = format_number(...);
}
