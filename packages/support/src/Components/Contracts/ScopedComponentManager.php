<?php

namespace Filament\Support\Components\Contracts;

use Closure;
use Filament\Support\Components\Component;

interface ScopedComponentManager
{
    public function configureUsing(string $component, Closure $modifyUsing, ?Closure $during = null, bool $isImportant = false): mixed;

    public function configure(Component $component, Closure $setUp): void;

    /**
     * @return array<string, Closure>
     */
    public function extractPublicMethods(Component $component): array;
}
