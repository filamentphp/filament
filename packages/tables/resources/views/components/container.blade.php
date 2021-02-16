@props([
    'records' => [],
    'sortColumn' => null,
    'sortDirection' => 'asc',
    'table',
])

<div {{ $attributes->merge(['class' => 'space-y-8']) }}>
    <div class="sm:flex items-center space-y-4 sm:space-y-0 sm:space-x-4 {{ ($table->filterable || $table->searchable) && $table->pagination ? 'justify-between' : ($table->pagination ? 'justify-end' : null) }}">
        @if ($table->filterable || $table->searchable)
            <div class="flex rounded shadow-sm border border-gray-300">
                @if ($table->searchable)
                    <div class="relative flex-grow">
                        <input
                            type="search"
                            wire:model="search"
                            placeholder="{{ __('tables::table.search.placeholder') }}"
                            class="text-sm sm:text-base {{ $table->filterable ? 'rounded-l' : 'rounded' }} pl-10 block w-full placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-secondary-300 focus:ring focus:ring-secondary-200 focus:ring-opacity-50 border-0"
                        />

                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none" aria-hidden="true">
                            <x-heroicon-o-search class="w-5 h-5" wire:loading.remove.delay />

                            <svg wire:loading.delay xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" fill="currentColor" class="w-5 h-5 transition-all duration-300">
                                <path d="M6.306 28.014c1.72 10.174 11.362 17.027 21.536 15.307C38.016 41.6 44.87 31.958 43.15 21.784l-4.011.678c1.345 7.958-4.015 15.502-11.974 16.847-7.959 1.346-15.501-4.014-16.847-11.973l-4.011.678z">
                                <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur=".7s" repeatCount="indefinite"/></path>
                            </svg>
                        </div>
                    </div>
                @endif

                @if ($table->filterable)
                    <select wire:model="filter" class="text-sm {{ $table->searchable ? 'rounded-r' : 'rounded' }} flex-shrink-0 sm:text-base focus:border-secondary-300 focus:ring focus:ring-secondary-200 focus:ring-opacity-50 border-0 {{ $table->searchable ? 'border-l' : null }} border-gray-300">
                        <option>{{ __('tables::table.filter.placeholder') }}</option>

                        @foreach ($table->getVisibleFilters() as $filter)
                            <option value="{{ $filter->name }}">{{ __($filter->label) }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        @endif

        @if ($table->pagination)
            <div class="flex-shrink-0 flex justify-end items-center space-x-2">
                <label for="records-per-page" class="text-sm leading-tight font-medium cursor-pointer">
                    {{ __('tables::table.pagination.fields.recordsPerPage.label') }}
                </label>

                <select wire:model="recordsPerPage" id="records-per-page" class="text-sm sm:text-base rounded shadow-sm focus:border-secondary-300 focus:ring focus:ring-secondary-200 focus:ring-opacity-50 border-gray-300">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        @endif
    </div>

    <div class="shadow-xl rounded bg-white overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    @foreach ($table->getVisibleColumns() as $column)
                        <th class="px-6 py-3 text-left text-gray-500" scope="col">
                            @if ($table->sortable && $column->isSortable())
                                <button
                                    wire:click="sortBy('{{ $column->name }}')"
                                    type="button"
                                    class="flex items-center space-x-1 text-left text-xs font-medium uppercase tracking-wider group focus:outline-none focus:underline"
                                >
                                    <span>{{ __($column->label) }}</span>

                                    <span class="relative flex items-center">
                                        @if ($sortColumn === $column->name)
                                            <span>
                                                @if ($sortDirection === 'asc')
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                @elseif ($sortDirection === 'desc')
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                                @endif
                                            </span>
                                        @else
                                            <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        @endif
                                    </span>
                                </button>
                            @else
                                <span class="text-xs font-medium uppercase tracking-wider">{{ __($column->label) }}</span>
                            @endif
                        </th>
                    @endforeach

                    @if ($table->recordUrl)
                        <th scope="col">
                            <span class="sr-only">{{ __($table->recordButtonLabel) }}</span>
                        </th>
                    @endif
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 text-sm leading-tight">
                @forelse ($records as $record)
                    <tr
                        class="{{ $loop->index % 2 ? 'bg-gray-50' : null }}"
                        wire:loading.class.delay="opacity-50"
                    >
                        @foreach ($table->getVisibleColumns() as $column)
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $column->renderCell($record) }}
                            </td>
                        @endforeach

                        @if ($table->recordUrl)
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a
                                    href="{{ $table->getRecordUrl($record) }}"
                                    class="hover:underline text-primary-500 hover:text-primary-700 transition-colors duration-200 font-medium"
                                >
                                    {{ __($table->recordButtonLabel) }}
                                </a>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td
                            class="px-6 py-4 whitespace-nowrap"
                            colspan="{{ count($table->getVisibleColumns()) + ($table->recordUrl ? 1 : 0) }}"
                        >
                            <div class="flex items-center justify-center h-16">
                                <p class="text-gray-500 font-mono text-xs">
                                    {{ __('tables::table.messages.noRecords') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($table->pagination)
        {{ $records->links() }}
    @endif
</div>
