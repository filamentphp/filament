<div class="border border-gray-300 divide-y bg-white shadow rounded-xl">
    <div class="flex items-center justify-between space-x-2 p-2">
        <div class="max-w-md">
            @if ($isSearchable())
                <label for="tableSearchQueryInput" class="sr-only">
                    {{ __('tables::table.fields.search_query.label') }}
                </label>

                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center justify-center w-9 h-9 text-gray-400 transition pointer-events-none group-focus-within:text-primary-500">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="6.25" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                            <path fill="currentColor" d="M18.7197 19.7803C19.0126 20.0732 19.4874 20.0732 19.7803 19.7803C20.0732 19.4874 20.0732 19.0126 19.7803 18.7197L18.7197 19.7803ZM14.9697 16.0303L18.7197 19.7803L19.7803 18.7197L16.0303 14.9697L14.9697 16.0303Z"/>
                        </svg>
                    </span>

                    <input
                        wire:model="tableSearchQuery"
                        id="tableSearchQueryInput"
                        placeholder="{{ __('tables::table.fields.search_query.placeholder') }}"
                        type="search"
                        class="block w-full h-9 pl-9 placeholder-gray-400 transition duration-75 border-gray-200 rounded-lg shadow-sm focus:border-primary-60 focus:ring-1 focus:ring-inset focus:ring-primary-600"
                    >
                </div>
            @endif
        </div>

        <div>
            @if ($isFilterable())
                <div x-data="{ isOpen: false }" class="relative inline-block">
                    <button
                        x-on:click="isOpen = ! isOpen"
                        type="button"
                        class="inline-flex items-center justify-center px-4 font-semibold tracking-tight text-gray-800 transition border rounded-lg h-9 hover:bg-gray-500/5 focus:ring-inset focus:ring-primary-600 focus:ring-1 focus:text-primary-600 focus:bg-primary-500/10 focus:border-primary-600 focus:outline-none"
                    >
                        <span>
                            {{ __('tables::table.buttons.filter.label') }}
                        </span>

                        <x-heroicon-o-filter class="w-4 h-4 ml-3 -mr-1" />
                    </button>

                    <div
                        x-show="isOpen"
                        x-on:click.away="isOpen = false"
                        x-transition:enter="transition ease duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2"
                        class="absolute right-0 z-10 w-screen max-w-xs mt-2 shadow-xl top-full rounded-xl"
                    >
                        <div class="px-6 py-4 bg-white shadow rounded-xl">
                            {{ $this->filtersForm }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="overflow-y-auto">
        @if (($records = $getRecords())->count())
            <table class="w-full text-left divide-y table-auto">
                <thead>
                    <tr class="divide-x bg-gray-50">
                        @foreach ($getColumns() as $column)
                            <th class="px-4 py-2 text-sm font-semibold text-gray-600">
                                {{ $column->getLabel() }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="divide-y whitespace-nowrap">
                    @foreach ($records as $record)
                        <tr class="divide-x">
                            @foreach ($getColumns() as $column)
                                @php
                                    $column->record($record);
                                @endphp

                                <td>
                                    @if ($action = $column->getAction())
                                        <button
                                            @if (is_string($action))
                                                wire:click="{{ $action }}('{{ $record->getKey() }}')"
                                            @elseif ($action instanceof \Closure)
                                                wire:click="callTableColumnAction('{{ $column->getName() }}', '{{ $record->getKey() }}')"
                                            @endif
                                            type="button"
                                            class="block text-left"
                                        >
                                            {{ $column }}
                                        </button>
                                    @elseif ($url = $column->getUrl())
                                        <a
                                            href="{{ $url }}"
                                            {{ $column->shouldOpenUrlInNewTab() ? 'target="_blank"' : null }}
                                            class="block"
                                        >
                                            {{ $column }}
                                        </a>
                                    @else
                                        {{ $column }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            @if ($view = $getEmptyStateView())
                {{ $view }}
            @else
                <div class="flex flex-col items-center justify-center max-w-md p-6 mx-auto space-y-6 text-center bg-white border m-4 rounded-2xl">
                    <div class="flex items-center justify-center w-16 h-16 text-blue-500 rounded-full bg-blue-50">
                        <x-dynamic-component :component="$getEmptyStateIcon()" class="w-6 h-6" />
                    </div>

                    <header class="max-w-xs space-y-1">
                        <h2 class="text-xl font-semibold tracking-tight">
                            {{ $getEmptyStateHeading() }}
                        </h2>

                        @if ($description = $getEmptyStateDescription())
                            <p class="text-sm font-medium text-gray-500">
                                {{ $description }}
                            </p>
                        @endif
                    </header>
                </div>
            @endif
        @endif
    </div>

    @if ($isPaginationEnabled())
        <div class="p-2">
            <x-tables::pagination :paginator="$records" />
        </div>
    @endif
</div>
