<?php

namespace Filament\Http\Livewire;

use Filament\Facades\Filament;
use Livewire\Component;

class GlobalSearch extends Component
{
    public $searchQuery = '';

    public function getResults(): ?array
    {
        $searchQuery = trim($this->searchQuery);

        if ($searchQuery === '') {
            return null;
        }

        $results = [];

        foreach (Filament::getResources() as $resource) {
            if (! $resource::canGloballySearch()) {
                continue;
            }

            $resourceResults = $resource::getGlobalSearchResults($searchQuery);

            if (! $resourceResults->count()) {
                continue;
            }

            $results[$resource::getPluralLabel()] = $resourceResults;
        }

        $this->dispatchBrowserEvent('open-global-search-results');

        return $results;
    }

    protected function isEnabled(): bool
    {
        foreach (Filament::getResources() as $resource) {
            if ($resource::canGloballySearch()) {
                return true;
            }
        }

        return false;
    }

    public function render()
    {
        return view('filament::global-search', [
            'results' => $this->getResults(),
        ]);
    }
}
