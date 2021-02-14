@pushonce('filament-styles:widgets')
    <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v0.x.x/dist/livewire-sortable.js"></script>
@endpushonce

<div>
    <x-filament::app-header :title="__('filament::dashboard.title')">
        <x-slot name="actions">
            <x-filament::dropdown class="cursor-pointer font-medium border rounded transition duration-200 shadow-sm inline-block relative focus:ring focus:ring-opacity-50 border-gray-300 from-gray-100 to-gray-200 text-gray-800 hover:to-gray-100 bg-gradient-to-b focus:ring-secondary-200 text-sm py-2 px-4 flex items-center space-x-1">
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
            <x-filament::widget title="Quick Links">
                <x-slot name="settings">
                    <x-filament::dropdown-link button>
                        Bookmark Setting...
                    </x-filament::dropdown-link>
                </x-slot>

                <ol class="divide-y divide-gray-200">
                    @for ($i = 1; $i <= 5; $i++)
                        <li class="py-3 flex items-center justify-between space-x-2">
                            <div class="flex-grow text-sm leading-tight">
                                <a href="#" class="link">Quick Link #{{ $i }}</a>
                            </div>

                            <button class="flex-shrink-0 text-gray-300 hover:text-danger-600 transition-colors duration-200">
                                <x-heroicon-o-x class="w-4 h-4" />
                            </button>
                        </li>
                    @endfor
                </ol>
            </x-filament::widget>

            <x-filament::widget title="Bar Chart" :columns="2">
                <x-slot name="settings">
                    <x-filament::dropdown-link button>
                        Chart Setting...
                    </x-filament::dropdown-link>

                    <x-filament::dropdown-link>
                        Another Chart Setting...
                    </x-filament::dropdown-link>
                </x-slot>

                widget content...
            </x-filament::widget>

            <x-filament::widget title="Activity">
                widget content...
            </x-filament::widget>
        </x-filament::widgets>
    </x-filament::app-content>
</div>
