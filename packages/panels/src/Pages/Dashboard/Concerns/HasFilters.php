<?php

namespace Filament\Pages\Dashboard\Concerns;

use Livewire\Attributes\Url;

trait HasFilters /** @phpstan-ignore trait.unused */
{
    /**
     * @var array<string, mixed> | null
     */
    #[Url]
    public ?array $filters = null;

    protected bool $persistsFiltersInSession = true;

    public function mountHasFilters(): void
    {
        $shouldPersistFiltersInSession = $this->persistsFiltersInSession();
        $filtersSessionKey = $this->getFiltersSessionKey();

        if (! count($this->filters ?? [])) {
            $this->filters = null;
        }

        if (
            ($this->filters === null) &&
            $shouldPersistFiltersInSession &&
            session()->has($filtersSessionKey)
        ) {
            $this->filters = session()->get($filtersSessionKey);
        }

        // https://github.com/filamentphp/filament/pull/7999
        if ($this->filters) {
            $this->normalizeTableFilterValuesFromQueryString($this->filters);
        }

        if (method_exists($this, 'getFiltersForm')) {
            $this->getFiltersForm()->fill($this->filters);
        }

        if ($shouldPersistFiltersInSession) {
            session()->put(
                $filtersSessionKey,
                $this->filters,
            );
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function normalizeTableFilterValuesFromQueryString(array &$data): void
    {
        foreach ($data as &$value) {
            if (is_array($value)) {
                $this->normalizeTableFilterValuesFromQueryString($value);
            } elseif ($value === 'null') {
                $value = null;
            } elseif ($value === 'false') {
                $value = false;
            } elseif ($value === 'true') {
                $value = true;
            }
        }
    }

    public function updatedFilters(): void
    {
        if ($this->persistsFiltersInSession()) {
            session()->put(
                $this->getFiltersSessionKey(),
                $this->filters,
            );
        }
    }

    public function getFiltersSessionKey(): string
    {
        $livewire = md5($this::class);

        return "{$livewire}_filters";
    }

    public function persistsFiltersInSession(): bool
    {
        return $this->persistsFiltersInSession;
    }
}
