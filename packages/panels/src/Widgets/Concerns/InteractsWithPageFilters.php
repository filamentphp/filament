<?php

namespace Filament\Widgets\Concerns;

use Filament\Support\Components\Attributes\Unexposed;
use Livewire\Attributes\Reactive;

trait InteractsWithPageFilters /** @phpstan-ignore trait.unused */
{
    /**
     * @var array<string, mixed> | null
     */
    #[Reactive]
    public ?array $pageFilters = null;

    #[Unexposed]
    public function __get($property): mixed
    {
        return parent::__get($property);
    }
}
