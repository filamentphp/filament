<?php

namespace Filament\Tests\Upgrade\Fixtures\ComputedNumberHelpers;

use function Filament\Support\format_money as money;
use function Filament\Support\format_number as decimal;

function moneyCallback(): \Closure
{
    return money(...);
}

function numberCallback(): \Closure
{
    return decimal(...);
}

function results(callable $observe): array
{
    $results = [];

    foreach ([23751.5, -18429.75, 0] as $amount) {
        foreach ([7, -13, 0, '7', '-13', '0', 7.0, false, null, [], 'invalid'] as $divisor) {
            $results[] = $observe(static fn () => money($amount + 0, 'USD', $divisor));
        }
    }

    foreach (['1234.75', true, false, null, [], 'invalid', INF, NAN] as $amount) {
        $results[] = $observe(static fn () => decimal($amount));
        $results[] = $observe(static fn () => money($amount, 'USD'));
    }

    foreach ([123, false, null, [], new class
    {
        public function __toString(): string
        {
            return 'USD';
        }
    }] as $currency) {
        $results[] = $observe(static fn () => money(23751.5, $currency, 7));
    }

    $results['default'] = $observe(static fn () => money(23751.5, 'USD'));
    $results['named-default'] = $observe(static fn () => money(currency: 'USD', money: 23751.5));
    $results['false-formatter'] = $observe(static fn () => money(23751.5, 'X'));
    $results['coerced-zero'] = $observe(static fn () => money(23751.5, 'USD', 0.5));

    $events = [];
    $effect = static function (string $name, mixed $value) use (&$events): mixed {
        $events[] = $name;

        return $value;
    };
    $results['ordered'] = $observe(static fn () => money(currency: $effect('currency', 'USD'), divideBy: $effect('divisor', -13), money: $effect('amount', 23751.5)));
    $results['extra'] = $observe(static fn () => money($effect('amount', 23751.5), $effect('currency', 'USD'), $effect('divisor', 7), $effect('extra', 999)));
    $results['invalid-with-effects'] = $observe(static fn () => money(currency: $effect('invalid-currency', []), money: $effect('validated-last', 23751.5)));
    $results['extra-decimal'] = $observe(static fn () => decimal($effect('decimal', 1234.75), $effect('ignored-precision', 2)));
    $results['events'] = $events;

    foreach ([['currency' => 'USD', 'divideBy' => -13, 'money' => 23751.5], [23751.5, 'USD', 7, 'extra'], [23751.5], ['money' => 1, 'currency' => 'USD', 'unknown' => 2]] as $arguments) {
        $results[] = $observe(static fn () => money(...$arguments));
    }

    $results['missing'] = $observe(static fn () => money(23751.5));
    $results['unknown'] = $observe(static fn () => money(money: 23751.5, currency: 'USD', unknown: 7));
    $results['missing-number'] = $observe(static fn () => decimal());
    $results['unknown-number'] = $observe(static fn () => decimal(number: 1, precision: 2));

    foreach ([moneyCallback(), \Filament\Tests\Upgrade\Fixtures\StrictNumberHelpers\moneyCallback()] as $callback) {
        $results[] = $observe(static fn () => $callback(currency: 'USD', divideBy: '-13', money: '23751.5'));
        $results[] = $observe(static fn () => $callback(23751.5, 'X'));
        $results[] = $observe(static fn () => $callback(23751.5, 'USD', null));
    }

    foreach ([numberCallback(), \Filament\Tests\Upgrade\Fixtures\StrictNumberHelpers\numberCallback()] as $callback) {
        $results[] = $observe(static fn () => $callback('1234.75'));
        $results[] = $observe(static fn () => $callback(null));
    }

    return $results;
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ComputedNumberHelpers;

use Closure;
use Illuminate\Support\Number;
use function Filament\Support\format_money as money;
use function Filament\Support\format_number as decimal;

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
        foreach ([7, -13, 0, '7', '-13', '0', 7.0, false, null, [], 'invalid'] as $divisor) {
            $results[] = $observe(static fn () => (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))($amount + 0, 'USD', $divisor));
        }
    }

    foreach (['1234.75', true, false, null, [], 'invalid', INF, NAN] as $amount) {
        $results[] = $observe(static fn () => (static fn(float|int $number): string => (string) Number::format($number))($amount));
        $results[] = $observe(static fn () => (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))($amount, 'USD'));
    }

    foreach ([123, false, null, [], new class
    {
        public function __toString(): string
        {
            return 'USD';
        }
    }] as $currency) {
        $results[] = $observe(static fn () => (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(23751.5, $currency, 7));
    }

    $results['default'] = $observe(static fn () => (string) Number::currency(23751.5, 'USD'));
    $results['named-default'] = $observe(static fn () => (string) Number::currency(23751.5, 'USD'));
    $results['false-formatter'] = $observe(static fn () => (string) Number::currency(23751.5, 'X'));
    $results['coerced-zero'] = $observe(static fn () => (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(23751.5, 'USD', 0.5));

    $events = [];
    $effect = static function (string $name, mixed $value) use (&$events): mixed {
        $events[] = $name;

        return $value;
    };
    $results['ordered'] = $observe(static fn () => (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(currency: $effect('currency', 'USD'), divideBy: $effect('divisor', -13), money: $effect('amount', 23751.5)));
    $results['extra'] = $observe(static fn () => (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))($effect('amount', 23751.5), $effect('currency', 'USD'), $effect('divisor', 7), $effect('extra', 999)));
    $results['invalid-with-effects'] = $observe(static fn () => (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(currency: $effect('invalid-currency', []), money: $effect('validated-last', 23751.5)));
    $results['extra-decimal'] = $observe(static fn () => (static fn(float|int $number): string => (string) Number::format($number))($effect('decimal', 1234.75), $effect('ignored-precision', 2)));
    $results['events'] = $events;

    foreach ([['currency' => 'USD', 'divideBy' => -13, 'money' => 23751.5], [23751.5, 'USD', 7, 'extra'], [23751.5], ['money' => 1, 'currency' => 'USD', 'unknown' => 2]] as $arguments) {
        $results[] = $observe(static fn () => (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(...$arguments));
    }

    $results['missing'] = $observe(static fn () => (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(23751.5));
    $results['unknown'] = $observe(static fn () => (static fn(float|int $money, string $currency, int $divideBy = 0): string => (string) Number::currency($divideBy ? $money / $divideBy : $money, $currency))(money: 23751.5, currency: 'USD', unknown: 7));
    $results['missing-number'] = $observe(static fn () => (static fn(float|int $number): string => (string) Number::format($number))());
    $results['unknown-number'] = $observe(static fn () => (static fn(float|int $number): string => (string) Number::format($number))(number: 1, precision: 2));

    foreach ([moneyCallback(), \Filament\Tests\Upgrade\Fixtures\StrictNumberHelpers\moneyCallback()] as $callback) {
        $results[] = $observe(static fn () => $callback(currency: 'USD', divideBy: '-13', money: '23751.5'));
        $results[] = $observe(static fn () => $callback(23751.5, 'X'));
        $results[] = $observe(static fn () => $callback(23751.5, 'USD', null));
    }

    foreach ([numberCallback(), \Filament\Tests\Upgrade\Fixtures\StrictNumberHelpers\numberCallback()] as $callback) {
        $results[] = $observe(static fn () => $callback('1234.75'));
        $results[] = $observe(static fn () => $callback(null));
    }

    return $results;
}
