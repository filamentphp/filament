<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Support\Components\ComponentManager;

trait Configurable
{
    public static function configureUsing(Closure $modifyUsing, ?Closure $during = null, bool $isImportant = false): mixed
    {
        if (! ($during || ComponentManager::isResolved())) {
            app()->resolving(
                ComponentManager::class,
                fn (ComponentManager $manager) => $manager->configureUsing(
                    static::class,
                    $modifyUsing,
                    $during,
                    $isImportant,
                ),
            );

            return null;
        }

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
