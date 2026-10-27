<?php

namespace Filament\Support;

function format_money(float | int $money, string $currency, int $divideBy = 0): string
{
    return 'custom money';
}

function format_number(float | int $number): string
{
    return 'custom number';
}

function customResults(): array
{
    return [format_money(123450, 'USD', 100), format_number(1234.5)];
}
