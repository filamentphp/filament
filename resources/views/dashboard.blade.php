<div class="flex-grow">
    <x-filament::app-header :title="__('filament::dashboard.title')">
        <x-slot name="actions">
            <x-filament::dropdown class="btn flex items-center space-x-1">
                <x-slot name="button">
                    <span>Add Widget</span>
                    <x-heroicon-o-chevron-down class="w-4 h-4" aria-hidden="true" />
                </x-slot>

                <x-filament::dropdown-link button>
                    test
                </x-filament::dropdown-link>
            </x-filament::dropdown>
        </x-slot>
    </x-filament::app-header>

    <x-filament::app-content>
        <x-filament::widgets>
            <x-filament::widget title="Bookmarks">
                widget content...
            </x-filament::widget>

            <x-filament::widget title="Sample Chart" :columns="2">
                widget content...
            </x-filament::widget>

            <x-filament::widget title="Recent Updates" :columns="3">
                widget content...
            </x-filament::widget>
        </x-filament::widgets>
    </x-filament::app-content>
</div>
