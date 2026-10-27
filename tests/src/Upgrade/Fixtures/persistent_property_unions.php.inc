<?php

namespace Filament\Tests\Upgrade\Fixtures\PersistentPropertyUnions;

use Closure;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;

class PropertyCollisionSchema extends Schema
{
    protected bool | Closure $isLoadingDeferred = false;

    protected bool | Closure $isLazyLoaded = true;
}

function inspectSchema(Schema | PropertyCollisionSchema $schema): bool
{
    return $schema->isLoadingDeferred();
}

class ReviewableSchema extends Schema
{
    protected bool | Closure $isLoadingDeferred = true;

    public function isLoadingDeferred(): bool
    {
        return parent::isLoadingDeferred();
    }

    public function inspectProperty(): bool
    {
        return $this->evaluate($this->isLoadingDeferred);
    }

    public function configure(): void
    {
        $this->deferLoading(condition: false);
    }
}

class PropertyCollisionWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static ?bool $isLazyLoaded = true;
}

function inspectWidget(Widget | PropertyCollisionWidget $widget): bool
{
    return $widget::isLazy();
}

class ReviewableWidget extends Widget
{
    protected static bool $isLazy = false;

    public static function isLazy(): bool
    {
        return parent::isLazy();
    }

    public function inspectProperty(): bool
    {
        return static::$isLazy;
    }
}
