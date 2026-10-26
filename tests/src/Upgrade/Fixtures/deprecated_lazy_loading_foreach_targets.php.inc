<?php

namespace Filament\Tests\Upgrade\Fixtures\LazyLoadingForeachTargets;

use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Model;

class ForeachTargetsManager extends RelationManager
{
    protected static bool $isLazy = true;
    protected static bool $isBadgeDeferred = false;

    public static function isLazy(): bool
    {
        return static::$isLazy;
    }

    public static function isBadgeDeferred(Model $ownerRecord, string $pageClass): bool
    {
        return static::$isBadgeDeferred;
    }

    public function configure(): void
    {
        foreach ([[true, false]] as static::$isBadgeDeferred => [static::$isLazy, $other]) {}
    }
}
