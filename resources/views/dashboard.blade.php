<div>
    <x-filament::app-header :title="__($title)">
        <x-slot name="actions">
            {{--
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
                    About
                </x-filament::dropdown-link>
                <x-filament::dropdown-link button>
                    Access Log
                </x-filament::dropdown-link>
            </x-filament::dropdown>
            --}}
        </x-slot>
    </x-filament::app-header>

    <x-filament::app-content>
        <x-filament::widgets>
            <x-filament::widget :title="config('filament.name').' v1.0'">
                {{--
                <x-slot name="settings">
                    <x-filament::dropdown-link button>
                        Remove
                    </x-filament::dropdown-link>
                </x-slot>
                --}}
                <div class="prose-sm prose">
                    <li><a href="https://filamentadmin.com/docs" target="_blank" rel="noopener noreferrer">Documentation</a></li>
                    <li><a href="https://github.com/laravel-filament/filament" target="_blank" rel="noopener noreferrer">Source Code</a></li>
                </div>
            </x-filament::widget>
            <x-filament::widget
                title="Access Log"
                :columns="1"
            >   
                <span class="font-mono text-xs">Access log...</span>
            </x-filament::widget>
        </x-filament::widgets>
    </x-filament::app-content>
</div>
