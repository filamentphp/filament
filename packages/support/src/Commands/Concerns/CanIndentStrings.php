<?php

namespace Filament\Support\Commands\Concerns;

trait CanIndentStrings /** @phpstan-ignore trait.unused */
{
    protected function indentString(string $string, int $level = 1): string
    {
        return implode(
            PHP_EOL,
            array_map(
                fn (string $line) => ($line !== '') ? (str_repeat('    ', $level) . "{$line}") : '',
                explode(PHP_EOL, $string),
            ),
        );
    }
}
