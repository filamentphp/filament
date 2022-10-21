<?php

namespace Filament\Tables\Columns\Concerns\Summary;

use Carbon\Carbon;
use Filament\Tables\Columns\Column;

abstract class Strategy
{
    public function __construct(
        protected Column $column,
        protected array $records
    ) {
    }

    abstract public function __invoke();

    protected function mapValue(?string $value, string $type): null|int|float|bool|string|Carbon
    {
        $value = trim(
            strip_tags($value)
        );

        if ($value === null || $value === '') {
            return null;
        }

        return match ($type) {
            'integer' => intval($value),
            'integer' => intval($value),
            'double', 'float' => floatval($value),
            'boolean' => $value = boolval($value),
            'datetime' => Carbon::parse($value),
            default => strval($value),
        };
    }
}
