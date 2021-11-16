<div class="relative">
    @if ($this->isEnabled())
        <x-filament::global-search.input />

        @if ($results !== null)
            <x-filament::global-search.results-container :results="$results" />
        @endif
    @endif
</div>
