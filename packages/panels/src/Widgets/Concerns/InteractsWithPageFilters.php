<?php

namespace Filament\Widgets\Concerns;

use Livewire\Attributes\Reactive;

trait InteractsWithPageFilters /** @phpstan-ignore trait.unused */
{
    /**
     * @var array<string, mixed> | null
     */
    #[Reactive]
    public ?array $pageFilters = null;

    public function __get($property): mixed
    {
        // Backwards compatibility for the `$this->filters` property before it was renamed.
        if ($property === 'filters') {
            return $this->pageFilters;
        }

        return parent::__get($property);
    }
}
