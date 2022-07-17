<x-filament::page class="filament-resources-list-records-page filament-resource-{{ str_replace('/', '-', $this->getResource()::getSlug()) }}">
    {{ $this->table }}
</x-filament::page>
