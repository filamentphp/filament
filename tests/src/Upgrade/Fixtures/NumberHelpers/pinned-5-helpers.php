<?php

namespace Filament\Support;

use Illuminate\Support\Number;

// Unmodified helper definitions from Filament 5.x at
// `e3ea817fc42d1d4351ffa88a29d3b01b0a881fd8:packages/support/src/helpers.php`.
if (! function_exists('Filament\Support\format_money')) {
    /**
     * @deprecated Use `Illuminate\Support\Number::currency()` instead.
     */
    function format_money(float | int $money, string $currency, int $divideBy = 0): string
    {
        if ($divideBy) {
            $money /= $divideBy;
        }

        return Number::currency($money, $currency);
    }
}

if (! function_exists('Filament\Support\format_number')) {
    /**
     * @deprecated Use `Illuminate\Support\Number::format()` instead.
     */
    function format_number(float | int $number): string
    {
        return Number::format($number);
    }
}
