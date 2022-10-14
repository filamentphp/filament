<div class="filament-global-search flex items-center ml-4">
    {{ \Filament\Facades\Filament::renderHook('global-search.start') }}

    @if ($this->isEnabled())
        <div class="relative">
            <x-filament::global-search.input />

            @if ($results !== null)
                <x-filament::global-search.results-container :results="$results" />
            @endif
        </div>
    @endif

    {{ \Filament\Facades\Filament::renderHook('global-search.end') }}
</div>
