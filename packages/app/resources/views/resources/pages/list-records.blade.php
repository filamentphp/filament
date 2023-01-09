<x-filament::page @class([
    'filament-resources-list-records-page',
    'filament-resources-' . str_replace('/', '-', $this->getResource()::getSlug()),
])>
    @if (count($queryTabs = $this->getQueryTabs()))
        <div class="flex justify-center">
            <x-filament::tabs>
                @foreach ($queryTabs as $tabKey => $query)
                    @php
                        $activeQueryTab = strval($activeQueryTab);
                        $tabKey = strval($tabKey);
                    @endphp

                    <button
                        wire:click="$set('activeQueryTab', {{ filled($tabKey) ? "'{$tabKey}'" : 'null' }})"
                        @if ($activeQueryTab === $tabKey)
                            aria-selected
                        @endif
                        role="tab"
                        type="button"
                        @class([
                            'flex whitespace-nowrap items-center h-8 px-5 font-medium rounded-lg whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-inset',
                            'hover:text-gray-800 focus:text-primary-600 dark:text-gray-400 dark:hover:text-gray-300 dark:focus:text-gray-400' => $activeQueryTab !== $tabKey,
                            'text-primary-600 shadow bg-white dark:text-white dark:bg-primary-600' => $activeQueryTab === $tabKey,
                        ])
                    >
                        {{ $this->getLabelFromQueryTabKey($tabKey) }}
                    </button>
                @endforeach
            </x-filament::tabs>
        </div>
    @endif

    {{ $this->table }}
</x-filament::page>
