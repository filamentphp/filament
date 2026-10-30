<?php

namespace Filament\Tests\Upgrade\Fixtures\ThinActionAndTableAliases;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Filament\Tables\Concerns\CanGroupRecords;
use Filament\Tables\Concerns\HasColumnManager;

class Owner
{
    use CanGroupRecords;
    use HasColumnManager;
    use InteractsWithActions;

    public static array $events = [];

    public function identity(): static
    {
        return $this;
    }

    #[Unexposed]
    public function mountedActionHasSchema(?Action $mountedAction = null, string $additional = 'default'): bool
    {
        static::$events[] = ['has', $mountedAction !== null, $additional];

        return $mountedAction !== null;
    }

    protected function getMountedActionSchema(?int $actionNestingIndex = 99, ?Action $mountedAction = null, string $additional = 'default'): ?Schema
    {
        static::$events[] = ['schema', $actionNestingIndex, $mountedAction !== null, $additional];

        return null;
    }

    #[Exposed]
    public function applyTableColumnManager(?array $state = null, bool $wasReordered = false): void
    {
        static::$events[] = ['columns', $state, $wasReordered];
    }

    #[Unexposed]
    public function getTableColumnsSessionKey(string $additional = 'default'): string
    {
        static::$events[] = ['session', $additional];

        return 'columns';
    }

    #[Unexposed]
    public function updatedTableGrouping(string $additional = 'default'): void
    {
        static::$events[] = ['group', $additional];
    }
}
