<?php

namespace Filament\Tests\Upgrade\Fixtures\ComputedNumberHelpers;

use function Filament\Support\format_money as money;
use function Filament\Support\format_number as decimal;

function results(float $amount, string $currency, int $divisor): array
{
    return [
        money($amount + 0, $currency, 100),
        money($amount, $currency, $divisor),
        money(currency: $currency, money: $amount, divideBy: 100),
        decimal($amount),
        decimal(nextAmount()),
        money(nextAmount(), nextCurrency(), 100),
    ];
}

function manual(float $amount, string $currency, int $divisor = 0): void
{
    money(currency: nextCurrency(), money: nextAmount(), divideBy: 100);
    money($amount, $currency, nextDivisor());
    $nextAmount = function () use (&$divisor): float {
        $divisor = 100;

        return 1234.5;
    };
    money($nextAmount(), $currency, $divisor);
    money($amount, nextCurrency(), $divisor);
}

function nextAmount(): float { return 1234.5; }
function nextCurrency(): string { return 'USD'; }
function nextDivisor(): int { return 100; }
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ComputedNumberHelpers;

use Illuminate\Support\Number;
use function Filament\Support\format_money as money;
use function Filament\Support\format_number as decimal;

function results(float $amount, string $currency, int $divisor): array
{
    return [
        Number::currency(($amount + 0) / 100, $currency),
        Number::currency($divisor ? $amount / $divisor : $amount, $currency),
        Number::currency($amount / 100, $currency),
        Number::format($amount),
        Number::format(nextAmount()),
        Number::currency(nextAmount() / 100, nextCurrency()),
    ];
}

function manual(float $amount, string $currency, int $divisor = 0): void
{
    money(currency: nextCurrency(), money: nextAmount(), divideBy: 100);
    money($amount, $currency, nextDivisor());
    $nextAmount = function () use (&$divisor): float {
        $divisor = 100;

        return 1234.5;
    };
    money($nextAmount(), $currency, $divisor);
    money($amount, nextCurrency(), $divisor);
}

function nextAmount(): float { return 1234.5; }
function nextCurrency(): string { return 'USD'; }
function nextDivisor(): int { return 100; }
