<x-filament::page
    :class="\Illuminate\Support\Arr::toCssClasses([
        'filament-resources-list-records-page',
        'filament-resources-' . str_replace('/', '-', $this->getResource()::getSlug()),
    ])"
>
    {{ \Filament\Facades\Filament::renderHook('table.start') }}

    {{ $this->table }}

    {{ \Filament\Facades\Filament::renderHook('table.end') }}
</x-filament::page>
