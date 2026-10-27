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
