<?php

namespace Filament\Tables\Enums;

enum BulkActionsPosition
{
    case AboveTable;

    case AboveAndBelowTable;

    case BelowTable;

    public function isAboveTable(): bool
    {
        return match ($this) {
            self::AboveTable, self::AboveAndBelowTable => true,
            self::BelowTable => false,
        };
    }

    public function isBelowTable(): bool
    {
        return match ($this) {
            self::BelowTable, self::AboveAndBelowTable => true,
            self::AboveTable => false,
        };
    }
}
