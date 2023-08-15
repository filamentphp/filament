<div class="fi-global-search flex items-center">
    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::global-search.start') }}

    <div class="sm:relative">
        <x-filament-panels::global-search.field />

        @if ($results !== null)
            <x-filament-panels::global-search.results-container
                :results="$results"
            />
        @endif
    </div>

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::global-search.end') }}
</div>
