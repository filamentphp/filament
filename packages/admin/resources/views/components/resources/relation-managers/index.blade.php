@props([
    'activeManager',
    'managers',
    'ownerRecord',
])

<div class="space-y-2 filament-resources-relation-managers-container">
    @if (count($managers) > 1)
        <div class="flex justify-center">
            <x-filament::tabs>
                @foreach ($managers as $managerKey => $manager)
                    <button
                        wire:click="$set('activeRelationManager', '{{ $managerKey }}')"
                        @if ($activeManager == $managerKey)
                            aria-selected
                            tabindex="0"
                        @else
                            tabindex="-1"
                        @endif
                        role="tab"
                        type="button"
                        @class([
                            'flex whitespace-nowrap items-center h-8 px-5 font-medium rounded-lg whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-inset',
                            'hover:text-gray-800 focus:text-primary-600' => $activeManager != $managerKey,
                            'dark:text-gray-400 dark:hover:text-gray-300 dark:focus:text-primary-400' => ($activeManager != $managerKey) && config('filament.dark_mode'),
                            'text-primary-600 shadow bg-white' => $activeManager == $managerKey,
                        ])
                    >
                        {{ $manager::getTitle() }}
                    </button>
                @endforeach
            </x-filament::tabs>
        </div>
    @endif

    @if (filled($activeManager))
        <div
            @if (count($managers) > 1)
                id="relationManager{{ ucfirst($activeManager) }}"
                role="tabpanel"
                tabindex="0"
            @endif
            class="focus:outline-none"
        >
            @livewire(\Livewire\Livewire::getAlias($managers[$activeManager], $managers[$activeManager]::getName()), ['ownerRecord' => $ownerRecord], key($activeManager))
        </div>
    @endif
</div>
