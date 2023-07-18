<div class="fi-global-search flex items-center">
    {{ \Filament\Support\Facades\FilamentView::renderHook('global-search.start') }}

    <div class="relative">
        <x-filament::global-search.field />

        @if ($results !== null)
            <x-filament::global-search.results-container :results="$results" />
        @endif
    </div>

    {{ \Filament\Support\Facades\FilamentView::renderHook('global-search.end') }}
</div>
