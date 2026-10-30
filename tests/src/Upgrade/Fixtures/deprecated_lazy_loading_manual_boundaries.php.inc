<?php

namespace Filament\Tests\Upgrade\Fixtures\LazyLoadingBoundaries;

use Filament\Forms\Components\TextInput;
use Filament\Widgets\Widget;

class PropertyCollision extends Widget
{
    protected static bool $isLazy = false;
    protected static ?bool $isLazyLoaded = null;
}

class MethodCollision extends Widget
{
    public static function isLazy(): bool
    {
        return false;
    }

    public static function isLazyLoaded(): bool
    {
        return true;
    }
}

class BindingOverride extends TextInput
{
    public function lazy(): static
    {
        return $this;
    }
}

class CustomNames
{
    public function lazy(): string
    {
        return 'custom';
    }

    public static function isLazy(): bool
    {
        return false;
    }
}

function inspect(BindingOverride $binding, CustomNames $custom): void
{
    MethodCollision::isLazy();
    $binding->lazy();
    $custom->lazy();
    CustomNames::isLazy();
}
