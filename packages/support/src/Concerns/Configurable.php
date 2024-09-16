<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Support\Components\ComponentManager;

trait Configurable
{
    public static function configureUsing(Closure $modifyUsing, ?Closure $during = null, bool $isImportant = false): mixed
    {
        return ComponentManager::resolve()->configureUsing(
            static::class,
            $modifyUsing,
            $during,
            $isImportant,
        );
    }

    public function configure(): static
    {
        ComponentManager::resolve()->configure(
            $this,
            $this->setUp(...),
        );

        return $this;
    }

    protected function setUp(): void
    {
    }
}
