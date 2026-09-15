<div class="space-y-4 p-4">
    <style>
        /* Playground CSS — scoped to this page, no Vite rebuild needed. */
        #table-playground {
        }
    </style>

    <x-filament::tabs label="Style">
        <x-filament::tabs.item tag="a" :href="url('/table-playground?style=table')" :active="$style === 'table'">
            Table
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="url('/table-playground?style=grid')" :active="$style === 'grid'">
            Grid
        </x-filament::tabs.item>
    </x-filament::tabs>

    <div id="table-playground">
        {{ $this->table }}
    </div>
</div>
