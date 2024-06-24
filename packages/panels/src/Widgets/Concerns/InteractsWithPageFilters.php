<?php

namespace Filament\Widgets\Concerns;

use Livewire\Attributes\Reactive;

trait InteractsWithPageFilters
{
    /**
     * @var array<string, mixed> | null
     */
    #[Reactive]
    public ?array $pageFilters = null;

    public function __get($property)
    {
        // Backwards compatibility for the `$this->filters` property before it was renamed.
        if ($property === 'filters') {
            return $this->pageFilters;
        }

        return parent::__get($property);
    }
}
