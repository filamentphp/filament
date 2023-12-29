<?php

namespace Filament\Widgets\Concerns;

use Livewire\Attributes\Reactive;

trait InteractsWithPageFilters
{
    /**
     * @var array<string, mixed> | null
     */
    #[Reactive]
    public ?array $filters = null;
}
