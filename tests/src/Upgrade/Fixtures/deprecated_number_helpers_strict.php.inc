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
}

function moneyCallback(): \Closure
{
    return format_money(...);
}

function numberCallback(): \Closure
{
    return format_number(...);
}

function results(callable $observe): array
{
    $results = [];

    foreach ([23751.5, -18429.75, 0] as $amount) {
        foreach ([7, -13, 0, '7', 0.5, false, null] as $divisor) {
            $results[] = $observe(static fn () => format_money($amount + 0, 'USD', $divisor));
        }
    }

    foreach (['1234.75', true, false, null, [], 1234.75, INF, NAN] as $amount) {
        $results[] = $observe(static fn () => format_number($amount));
        $results[] = $observe(static fn () => format_money($amount, 'USD'));
    }

    foreach ([moneyCallback(), \Filament\Tests\Upgrade\Fixtures\ComputedNumberHelpers\moneyCallback()] as $callback) {
        $results[] = $observe(static fn () => $callback(23751.5, 'USD', -13));
        $results[] = $observe(static fn () => $callback(23751.5, 'USD', '7'));
        $results[] = $observe(static fn () => $callback('23751.5', 'USD'));
        $results[] = $observe(static fn () => $callback(23751.5, 123));
        $results[] = $observe(static fn () => $callback(23751.5, 'X'));
    }

    foreach ([numberCallback(), \Filament\Tests\Upgrade\Fixtures\ComputedNumberHelpers\numberCallback()] as $callback) {
        $results[] = $observe(static fn () => $callback(1234.75));
        $results[] = $observe(static fn () => $callback('1234.75'));
    }

    $results['strict-currency'] = $observe(static fn () => format_money(23751.5, 123));
    $results['default'] = $observe(static fn () => format_money(23751.5, 'USD'));
    $results['false-formatter'] = $observe(static fn () => format_money(23751.5, 'X'));

    return $results;
}
-----
<?php

declare(strict_types=1);

namespace Filament\Tests\Upgrade\Fixtures\StrictNumberHelpers;

use Illuminate\Support\Number;
use Closure;
use function Filament\Support\format_money;
use function Filament\Support\format_number;

function manual($amount): void
{
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))($amount, 'USD', 100);
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(1234, 'USD', 0.5);
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(1234, 'USD', '100');
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))('1234', 'USD', 100);
    (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(1234, 123, 100);
    (static fn(float|int $number): string => (string) Number::format($number))('1234');
    (static fn(float|int $number): string => (string) Number::format($number))(true);
    (static fn(float|int $number): string => (string) Number::format($number))(1234, sideEffect());
}

function moneyCallback(): Closure
{
    return static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency);
}

function numberCallback(): Closure
{
    return static fn(float|int $number): string => (string) Number::format($number);
}

function results(callable $observe): array
{
    $results = [];

    foreach ([23751.5, -18429.75, 0] as $amount) {
        foreach ([7, -13, 0, '7', 0.5, false, null] as $divisor) {
            $results[] = $observe(static fn () => (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))($amount + 0, 'USD', $divisor));
        }
    }

    foreach (['1234.75', true, false, null, [], 1234.75, INF, NAN] as $amount) {
        $results[] = $observe(static fn () => (static fn(float|int $number): string => (string) Number::format($number))($amount));
        $results[] = $observe(static fn () => (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))($amount, 'USD'));
    }

    foreach ([moneyCallback(), \Filament\Tests\Upgrade\Fixtures\ComputedNumberHelpers\moneyCallback()] as $callback) {
        $results[] = $observe(static fn () => $callback(23751.5, 'USD', -13));
        $results[] = $observe(static fn () => $callback(23751.5, 'USD', '7'));
        $results[] = $observe(static fn () => $callback('23751.5', 'USD'));
        $results[] = $observe(static fn () => $callback(23751.5, 123));
        $results[] = $observe(static fn () => $callback(23751.5, 'X'));
    }

    foreach ([numberCallback(), \Filament\Tests\Upgrade\Fixtures\ComputedNumberHelpers\numberCallback()] as $callback) {
        $results[] = $observe(static fn () => $callback(1234.75));
        $results[] = $observe(static fn () => $callback('1234.75'));
    }

    $results['strict-currency'] = $observe(static fn () => (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(23751.5, 123));
    $results['default'] = $observe(static fn () => (string) Number::currency(23751.5, 'USD'));
    $results['false-formatter'] = $observe(static fn () => (string) Number::currency(23751.5, 'X'));

    return $results;
}
