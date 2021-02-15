<div>
    <x-filament::app-header :title="__('filament::dashboard.title')">
        <x-slot name="actions">
            <x-filament::dropdown class="cursor-pointer font-medium border rounded transition duration-200 shadow-sm inline-block relative focus:ring focus:ring-opacity-50 border-gray-300 from-gray-100 to-gray-200 text-gray-800 hover:to-gray-100 bg-gradient-to-b focus:ring-secondary-200 text-sm py-2 px-4 flex items-center space-x-1">
                <x-slot name="button">
                    <x-heroicon-o-plus class="w-4 h-4" aria-hidden="true" />
                    <span>{{ __('filament::widgets.add') }}</span>
                    <x-heroicon-o-chevron-down class="w-4 h-4" aria-hidden="true" />
                </x-slot>

                <x-filament::dropdown-link button>
                    Quick Links
                </x-filament::dropdown-link>
                <x-filament::dropdown-link button>
                    Activity
                </x-filament::dropdown-link>
                <x-filament::dropdown-link button>
                    Chart
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

                <div class="space-y-4">
                    <ol class="divide-y divide-gray-200">
                        @for ($i = 1; $i <= 5; $i++)
                            <li class="py-2.5 flex items-center justify-between space-x-3">
                                <button class="flex-shrink-0 flex text-gray-300 hover:text-gray-600 transition-colors duration-200">
                                    <x-filamenticon-o-sort class="w-3 h-3" />
                                </button>

                                <div class="flex-grow text-sm leading-tight">
                                    <a href="#" class="link">Quick Link #{{ $i }}</a>
                                </div>

                                <button class="flex-shrink-0 text-gray-300 hover:text-danger-600 transition-colors duration-200">
                                    <x-heroicon-o-x class="w-4 h-4" />
                                </button>
                            </li>
                        @endfor
                    </ol>

                    <div class="flex justify-end">
                        <x-filament::button
                            size="small"
                            class="flex items-center space-x-1"
                        >
                            <x-heroicon-o-plus class="w-4 h-4" aria-hidden="true" />
                            <span>Add Link</span>
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::widget>
            <x-filament::widget title="Activity">
                widget content...
            </x-filament::widget>
        </x-filament::widgets>
    </x-filament::app-content>
</div>
