<div class="fi-global-search ms-4 flex items-center">
    {{ filament()->renderHook('global-search.start') }}

    @if ($this->isEnabled())
        <div class="relative">
            <x-filament::global-search.field />

            @if ($results !== null)
                <x-filament::global-search.results-container
                    :results="$results"
                />
            @endif
        </div>
    @endif

    {{ filament()->renderHook('global-search.end') }}
</div>
