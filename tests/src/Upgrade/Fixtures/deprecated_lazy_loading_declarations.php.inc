<?php

namespace Filament\Tests\Upgrade\Fixtures\LazyLoadingDeclarations;

use Closure;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class EagerWidget extends Widget
{
    protected static bool $isLazy = false;

    public static function isLazy(): bool
    {
        return parent::isLazy() && static::$isLazy;
    }

    public function inspect(): bool
    {
        return self::isLazy() || self::$isLazy || parent::$isLazy;
    }
}

class CustomManager extends RelationManager
{
    protected static bool $isLazy = true;
    protected static bool $isBadgeDeferred = false;

    public static function isBadgeDeferred(Model $ownerRecord, string $pageClass): bool
    {
        return parent::isBadgeDeferred($ownerRecord, $pageClass) || static::$isBadgeDeferred;
    }
}

class CustomSchema extends Schema
{
    protected bool | Closure $isLoadingDeferred = false;

    public function isLoadingDeferred(): bool
    {
        return parent::isLoadingDeferred() || $this->evaluate($this->isLoadingDeferred);
    }
}

class GetterOnlyWidget extends Widget
{
    public static function isLazy(): bool
    {
        return static::$isLazy;
    }
}

class GetterOnlyManager extends RelationManager
{
    public static function isLazy(): bool
    {
        return static::$isLazy;
    }

    public static function isBadgeDeferred(Model $ownerRecord, string $pageClass): bool
    {
        return static::$isBadgeDeferred;
    }
}

function inspect(EagerWidget $widget, CustomSchema $schema): void
{
    $widget::isLazy();
    $schema->isLoadingDeferred();
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\LazyLoadingDeclarations;

use Closure;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class EagerWidget extends Widget
{
    protected static ?bool $isLazyLoaded = false;

    public static function isLazyLoaded(): bool
    {
        return parent::isLazyLoaded() && (static::$isLazyLoaded ?? true);
    }

    public function inspect(): bool
    {
        return self::isLazyLoaded() || (self::$isLazyLoaded ?? true) || (parent::$isLazyLoaded ?? true);
    }
}

class CustomManager extends RelationManager
{
    protected static ?bool $isLazyLoaded = true;
    protected static ?bool $isBadgeLazyLoaded = false;

    public static function isBadgeLazyLoaded(Model $ownerRecord, string $pageClass): bool
    {
        return parent::isBadgeLazyLoaded($ownerRecord, $pageClass) || (static::$isBadgeLazyLoaded ?? false);
    }
}

class CustomSchema extends Schema
{
    protected bool | Closure $isLazyLoaded = false;

    public function isLazyLoaded(): bool
    {
        return parent::isLazyLoaded() || $this->evaluate($this->isLazyLoaded);
    }
}

class GetterOnlyWidget extends Widget
{
    public static function isLazyLoaded(): bool
    {
        return static::$isLazyLoaded ?? true;
    }
}

class GetterOnlyManager extends RelationManager
{
    public static function isLazyLoaded(): bool
    {
        return static::$isLazyLoaded ?? true;
    }

    public static function isBadgeLazyLoaded(Model $ownerRecord, string $pageClass): bool
    {
        return static::$isBadgeLazyLoaded ?? false;
    }
}

function inspect(EagerWidget $widget, CustomSchema $schema): void
{
    $widget::isLazyLoaded();
    $schema->isLazyLoaded();
}
