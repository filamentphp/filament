<?php

namespace Filament\Tests\Upgrade\Fixtures\ManualNumberHelpers;

use Illuminate\Support\Number;

use function Filament\Support\format_money as money;
use function Filament\Support\format_number as decimal;

function manual($amount, $currency, $divisor, $arguments, $callback): void
{
    money($amount, 'USD', 100);
    money(1234, $currency, 100);
    money(1234, 'USD', $divisor);
    money(1234, 'USD', 100.0);
    money('1234', 'USD', 100);
    money(1234, 123, 100);
    money(1234, 'USD', '100');
    money(1234, 'USD', false);
    money(1234, 'USD', null);
    money(1234, 'USD', nextDivisor());
    money(currency: nextCurrency(), money: nextAmount(), divideBy: nextDivisor());
    money(...$arguments);
    money(1234, 'USD', 100, ignoredSideEffect());
    money(1234);
    money(money: 1234, currency: 'USD', unknown: 100);
    money(...);
    decimal($amount);
    decimal('1234');
    decimal(null);
    decimal(true);
    decimal(nextAmount());
    decimal(1234, ignoredSideEffect());
    decimal(1234, 2);
    decimal(...$arguments);
    decimal(...);
    $callback(1234);
    call_user_func('Filament\\Support\\format_number', 1234);
    $callback = 'Filament\\Support\\format_money';
    $callback(1234, 'USD');
    Number::format(1234, precision: 2);
    Number::currency(1234, in: 'USD', precision: 3);
    format_money(1234, 'USD');
    format_number(1234);
}

function format_money($amount, $currency) {}

function format_number($amount) {}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ManualNumberHelpers;

use Illuminate\Support\Number;

use function Filament\Support\format_money as money;
use function Filament\Support\format_number as decimal;

function manual($amount, $currency, $divisor, $arguments, $callback): void
{
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))($amount, 'USD', 100);
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(1234, $currency, 100);
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(1234, 'USD', $divisor);
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(1234, 'USD', 100.0);
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))('1234', 'USD', 100);
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(1234, 123, 100);
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(1234, 'USD', '100');
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(1234, 'USD', false);
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(1234, 'USD', null);
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(1234, 'USD', nextDivisor());
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(currency: nextCurrency(), money: nextAmount(), divideBy: nextDivisor());
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(...$arguments);
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(1234, 'USD', 100, ignoredSideEffect());
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(1234);
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(money: 1234, currency: 'USD', unknown: 100);
    static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency);
    (static fn(float|int $number): string => (string) Number::format($number))($amount);
    (static fn(float|int $number): string => (string) Number::format($number))('1234');
    (static fn(float|int $number): string => (string) Number::format($number))(null);
    (static fn(float|int $number): string => (string) Number::format($number))(true);
    (static fn(float|int $number): string => (string) Number::format($number))(nextAmount());
    (static fn(float|int $number): string => (string) Number::format($number))(1234, ignoredSideEffect());
    (static fn(float|int $number): string => (string) Number::format($number))(1234, 2);
    (static fn(float|int $number): string => (string) Number::format($number))(...$arguments);
    static fn(float|int $number): string => (string) Number::format($number);
    $callback(1234);
    call_user_func('Filament\\Support\\format_number', 1234);
    $callback = 'Filament\\Support\\format_money';
    $callback(1234, 'USD');
    Number::format(1234, precision: 2);
    Number::currency(1234, in: 'USD', precision: 3);
    format_money(1234, 'USD');
    format_number(1234);
}

function format_money($amount, $currency) {}

function format_number($amount) {}
