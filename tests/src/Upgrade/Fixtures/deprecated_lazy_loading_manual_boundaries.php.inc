<?php

namespace Filament\Tests\Upgrade\Fixtures\LazyLoadingBoundaries;

use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Livewire;
use Filament\Widgets\Widget;

class PropertyCollision extends Widget
{
    protected static bool $isLazy = false;
    protected static ?bool $isLazyLoaded = null;

    public function inspect(): bool
    {
        return static::$isLazy;
    }
}

class MethodCollision extends Widget
{
    public static function isLazy(): bool
    {
        return parent::isLazy();
    }

    public static function isLazyLoaded(): bool
    {
        return false;
    }
}

class ForwardingOverride extends Widget
{
    public static function isLazy(): bool
    {
        return static::isLazyLoaded();
    }
}

class ExplicitNewConfiguration extends Widget
{
    protected static ?bool $isLazyLoaded = false;

    public static function isLazy(): bool
    {
        return parent::isLazy();
    }
}

class InheritedDefault extends Widget
{
    public function isConfigured(): bool
    {
        return isset(static::$isLazy);
    }
}

class WritableConfiguration extends Widget
{
    protected static bool $isLazy = false;

    public static function isLazy(): bool
    {
        return static::$isLazy;
    }

    public function configure(): void
    {
        static::$isLazy = true;
        $reference = &static::$isLazy;
    }
}

class EmbeddedOverride extends Livewire
{
    public function lazy(bool | Closure $condition = true): static
    {
        return $this;
    }

    public function lazyLoaded(bool | Closure $condition = true): static
    {
        return $this;
    }
}

class GroupedProperties extends Widget
{
    protected static bool $other = true, $isLazy = false;

    public function inspect(): bool
    {
        return static::$isLazy;
    }
}

class BindingOverride extends TextInput
{
    public function lazy(): static
    {
        return parent::lazy();
    }
}

function inspect(BindingOverride $binding, EmbeddedOverride $embedded): void
{
    MethodCollision::isLazy();
    ForwardingOverride::isLazy();
    ExplicitNewConfiguration::isLazy();
    $binding->lazy();
    $embedded->lazy();
}
