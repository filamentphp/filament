@php
    $playgroundUrl = fn (array $query): string => url('/table-playground?' . http_build_query(['style' => $style, 'layout' => $layout, ...$query]));
@endphp

<div class="space-y-4 p-4">
    <style>
        /* Playground CSS — scoped to this page, no Vite rebuild needed. */
        #table-playground {
        }
    </style>

    <x-filament::tabs label="Style">
        <x-filament::tabs.item tag="a" :href="$playgroundUrl(['style' => 'table'])" :active="$style === 'table'">
            Table
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="$playgroundUrl(['style' => 'grid'])" :active="$style === 'grid'">
            Grid
        </x-filament::tabs.item>
    </x-filament::tabs>

    <x-filament::tabs label="Layout">
        <x-filament::tabs.item tag="a" :href="$playgroundUrl(['layout' => 'scratch'])" :active="$layout === 'scratch'">
            Scratch
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="$playgroundUrl(['layout' => 'cards'])" :active="$layout === 'cards'">
            Card list
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="$playgroundUrl(['layout' => 'sidebar'])" :active="$layout === 'sidebar'">
            Sidebar filters
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="$playgroundUrl(['layout' => 'grid-controls'])" :active="$layout === 'grid-controls'">
            Grid controls
        </x-filament::tabs.item>
    </x-filament::tabs>

    <div id="table-playground">
        {{ $this->table }}
    </div>
</div>
