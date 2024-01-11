<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Support\Components\ComponentManager;

trait Configurable
{
    public static function configureUsing(Closure $modifyUsing, ?Closure $during = null, bool $isImportant = false): mixed
    {
        return app(ComponentManager::class)->configureUsing(
            static::class,
            $modifyUsing,
            $during,
            $isImportant,
        );
    }

    public function configure(): static
    {
        app(ComponentManager::class)->configure(
            $this,
            $this->setUp(...),
        );

        return $this;
    }

    protected function setUp(): void
    {
    }
}
