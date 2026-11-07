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
}

class CustomManager extends RelationManager
{
    protected static bool $isLazy = false;
    protected static bool $isBadgeDeferred = true;

    public static function isBadgeDeferred(Model $ownerRecord, string $pageClass): bool
    {
        return parent::isBadgeDeferred($ownerRecord, $pageClass) || static::$isBadgeDeferred;
    }
}

trait EagerHook
{
    public static function isLazy(): bool
    {
        return ! parent::isLazy() || static::$isLazy;
    }
}

class TraitWidget extends Widget
{
    use EagerHook;

    protected static bool $isLazy = false;
}

class InheritedWidget extends TraitWidget
{
    public static function isLazy(): bool
    {
        return ! parent::isLazy();
    }
}

class TraitManager extends RelationManager
{
    use EagerHook;

    protected static bool $isLazy = false;
}

class CustomSchema extends Schema
{
    protected bool | Closure $isLoadingDeferred = false;

    public function isLoadingDeferred(): bool
    {
        return parent::isLoadingDeferred() || $this->evaluate($this->isLoadingDeferred);
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
        return parent::isLazyLoaded() && static::$isLazyLoaded;
    }
}

class CustomManager extends RelationManager
{
    protected static ?bool $isLazyLoaded = false;
    protected static ?bool $isBadgeLazyLoaded = true;

    public static function isBadgeLazyLoaded(Model $ownerRecord, string $pageClass): bool
    {
        return parent::isBadgeLazyLoaded($ownerRecord, $pageClass) || static::$isBadgeLazyLoaded;
    }
}

trait EagerHook
{
    public static function isLazyLoaded(): bool
    {
        return ! parent::isLazyLoaded() || static::$isLazyLoaded;
    }
}

class TraitWidget extends Widget
{
    use EagerHook;

    protected static ?bool $isLazyLoaded = false;
}

class InheritedWidget extends TraitWidget
{
    public static function isLazyLoaded(): bool
    {
        return ! parent::isLazyLoaded();
    }
}

class TraitManager extends RelationManager
{
    use EagerHook;

    protected static ?bool $isLazyLoaded = false;
}

class CustomSchema extends Schema
{
    protected bool | Closure $isLazyLoaded = false;

    public function isLazyLoaded(): bool
    {
        return parent::isLazyLoaded() || $this->evaluate($this->isLazyLoaded);
    }
}

function inspect(EagerWidget $widget, CustomSchema $schema): void
{
    $widget::isLazyLoaded();
    $schema->isLazyLoaded();
}
