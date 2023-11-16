<?php

namespace Filament\Resources\Concerns;

use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

trait HasTabs
{
    public ?string $activeTab = null;

    /**
     * @var array<string | int, Tab>
     */
    protected array $cachedTabs;

    protected function loadDefaultActiveTab(): void
    {
        if (filled($this->activeTab)) {
            return;
        }

        $this->activeTab = $this->getDefaultActiveTab();
    }

    /**
     * @return array<string | int, Tab>
     */
    public function getTabs(): array
    {
        return [];
    }

    /**
     * @return array<string | int, Tab>
     */
    public function getCachedTabs(): array
    {
        return $this->cachedTabs ??= $this->getTabs();
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return array_key_first($this->getCachedTabs());
    }

    public function updatedActiveTab(): void
    {
        $this->resetPage();
    }

    public function generateTabLabel(string $key): string
    {
        return (string) str($key)
            ->replace(['_', '-'], ' ')
            ->ucfirst();
    }

    protected function modifyQueryWithActiveTab(Builder $query): Builder
    {
        if (blank(filled($this->activeTab))) {
            return $query;
        }

        $tabs = $this->getCachedTabs();

        if (! array_key_exists($this->activeTab, $tabs)) {
            return $query;
        }

        return $tabs[$this->activeTab]->modifyQuery($query);
    }
}
