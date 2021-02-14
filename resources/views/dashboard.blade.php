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
                <x-slot name="settings">
                    <x-filament::dropdown-link button>
                        Bookmark Setting...
                    </x-filament::dropdown-link>
                </x-slot>
                widget content...
            </x-filament::widget>

            <x-filament::widget title="Sample Chart" :columns="2" class="h-56">
                <x-slot name="settings">
                    <x-filament::dropdown-link>
                        Chart Setting...
                    </x-filament::dropdown-link>
                </x-slot>

                widget content...
            </x-filament::widget>

            <x-filament::widget title="Recent Updates">
                widget content...
            </x-filament::widget>
        </x-filament::widgets>
    </x-filament::app-content>
</div>
