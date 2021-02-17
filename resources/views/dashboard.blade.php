<div>
    <x-filament::app-header :title="__('filament::dashboard.title')">
        <x-slot name="actions">
            <x-filament::dropdown 
                :asButton="true"
                class="flex items-center space-x-1"
            >
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
                                    <a href="#" class="text-xs font-mono link">Link #{{ $i }} Label</a>
                                </div>

                                <button class="flex-shrink-0 text-gray-300 hover:text-danger-600 transition-colors duration-200">
                                    <span class="sr-only">Remove Link</span>
                                    <x-heroicon-o-x class="flex-shrink-0 w-4 h-4" />
                                </button>
                            </li>
                        @endfor
                    </ol>

                    <form class="flex justify-between space-x-4">
                        <div class="flex-grow grid grid-cols-2 gap-4">
                            <input 
                                placeholder="Label"
                                class="py-1 border-b text-xs font-mono border-gray-200 placeholder-opacity-100 placeholder-gray-500 focus:placeholder-gray-300 outline-none focus:border-gray-400"
                            >
                            <input 
                                placeholder="URL"
                                class="py-1 border-b text-xs font-mono border-gray-200 placeholder-opacity-100 placeholder-gray-500 focus:placeholder-gray-300 outline-none focus:border-gray-400"
                            >
                        </div>
                        <button class="flex-shrink-0 flex items-center space-x-1 text-gray-500 hover:text-success-600 transition-colors duration-200">
                            <span class="text-xs">Add Link</span>
                            <x-heroicon-o-plus class="flex-shrink-0 w-4 h-4" />
                        </button>
                    </form>
                </div>
            </x-filament::widget>
            <x-filament::widget 
                title="Activity"
                :columns="2"
            >
                <span class="text-xs font-mono">filters and a paginated table (10 or so results)</span>
            </x-filament::widget>
        </x-filament::widgets>
    </x-filament::app-content>
</div>
