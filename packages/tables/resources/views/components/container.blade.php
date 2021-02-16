@props([
    'records' => [],
    'sortColumn' => null,
    'sortDirection' => 'asc',
    'table',
])

<div {{ $attributes->merge(['class' => 'space-y-4']) }}>
    <div class="flex items-center space-x-4 {{ $table->searchable && $table->pagination ? 'justify-between' : ($table->pagination ? 'justify-end' : null) }}">
        @if ($table->searchable)
            <div class="relative flex-grow max-w-screen-md">
                <input
                    type="search"
                    wire:model="search"
                    placeholder="Search"
                    class="pl-10 block w-full rounded shadow-sm placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-secondary-300 focus:ring focus:ring-secondary-200 focus:ring-opacity-50 border-gray-300"
                />

                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none" aria-hidden="true">
                    <x-heroicon-o-search class="w-5 h-5" wire:loading.remove />

                    <x-filament::loader class="w-5 h-5" wire:loading />
                </div>
            </div>
        @endif

        @if ($table->pagination)
            <div class="flex items-center space-x-2">
                <label for="records-per-page" class="text-sm leading-tight font-medium cursor-pointer">
                    {{ __('filament::datatable.perPage') }}
                </label>

                <select wire:model="recordsPerPage" id="records-per-page" class="rounded shadow-sm focus:border-secondary-300 focus:ring focus:ring-secondary-200 focus:ring-opacity-50 border-gray-300">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        @endif
    </div>

    <x-filament::table>
        <x-slot name="head">
            @foreach ($table->getVisibleColumns() as $column)
                <th class="px-6 py-3 text-left text-gray-500" scope="col">
                    @if ($table->sortable && $column->isSortable())
                        <button wire:click="sortBy('{{ $column->name }}')" type="button" class="flex items-center space-x-1 text-left text-xs font-medium uppercase tracking-wider group focus:outline-none focus:underline">
                            <span>{{ __($column->label) }}</span>

                            <span class="relative flex items-center">
                                <span>
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
                            </span>
                        </button>
                                 @else
                        <span class="text-xs font-medium uppercase tracking-wider">{{ __($column->label) }}</span>
                       @endif
                </th>
            @endforeach

            @if ($table->recordUrl)
                <th scope="col"></th>
            @endif
        </x-slot>

        <x-slot name="body">
            @forelse ($records as $record)
                <x-filament::table.row wire:loading.class.delay="opacity-50">
                    @foreach ($table->getVisibleColumns() as $column)
                        <x-filament::table.cell>
                            {{ $column->renderCell($record) }}
                        </x-filament::table.cell>
                    @endforeach

                    @if ($table->recordUrl)
                        <x-filament::table.cell class="text-right">
                            <a href="{{ $table->getRecordUrl($record) }}" class="text-secondary-500 underline hover:text-secondary-700 transition-colors duration-200">Edit</a>
                        </x-filament::table.cell>
                    @endif
                </x-filament::table.row>
            @empty
                <x-filament::table.row>
                    <x-filament::table.cell :colspan="count($table->getVisibleColumns()) + ($table->recordUrl ? 1 : 0)">
                        <div class="flex items-center justify-center h-16">
                            <p class="text-gray-500 font-mono text-xs">No records found</p>
                        </div>
                    </x-filament::table.cell>
                </x-filament::table.row>
            @endforelse
        </x-slot>
    </x-filament::table>

    @if ($table->pagination)
        {{ $records->links() }}
    @endif
</div>
