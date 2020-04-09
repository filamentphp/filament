<span class="inline-flex items-center">
    @if ($sortField !== $field)
        <x-heroicon-o-selector class="w-4 h-4 mr-1" />
    @elseif ($sortAsc)
        <x-heroicon-o-sort-ascending class="w-4 h-4 mr-1" />
    @else
        <x-heroicon-o-sort-descending class="w-4 h-4 mr-1" />
    @endif
    {{ $label }}
</span>