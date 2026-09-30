<?php

namespace Filament\Livewire;

use Filament\Facades\Filament;
use Filament\GlobalSearch\GlobalSearchResults;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class GlobalSearch extends Component
{
    public ?string $search = '';

    protected bool $areResultsDeferred = false;

    public function mount(): void
    {
        if (! Filament::getCurrentOrDefaultPanel()->persistsGlobalSearchInSession()) {
            return;
        }

        $this->search = session()->get($this->getSearchSessionKey(), '');

        // Defers the results until the field is focused, so the search does not run on every page load.
        // Protected properties are not dehydrated, so this is reset on the next request.
        $this->areResultsDeferred = filled($this->search);
    }

    public function updatedSearch(): void
    {
        if (! Filament::getCurrentOrDefaultPanel()->persistsGlobalSearchInSession()) {
            return;
        }

        session()->put(
            $this->getSearchSessionKey(),
            $this->search,
        );
    }

    public function getSearchSessionKey(): string
    {
        $panel = Filament::getCurrentOrDefaultPanel()->getId();

        return "filament.{$panel}.global_search";
    }

    public function getResults(): ?GlobalSearchResults
    {
        $search = trim($this->search);

        if (blank($search)) {
            return null;
        }

        $results = Filament::getGlobalSearchProvider()->getResults($this->search);

        if ($results === null) {
            return $results;
        }

        $this->dispatch('open-global-search-results');

        return $results;
    }

    public function render(): View
    {
        return view('filament-panels::livewire.global-search', [
            'areResultsDeferred' => $this->areResultsDeferred,
            'results' => $this->areResultsDeferred ? null : $this->getResults(),
        ]);
    }
}
