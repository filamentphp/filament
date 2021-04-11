@props([
    'records' => [],
    'selected' => [],
    'sortColumn' => null,
    'sortDirection' => 'asc',
    'table',
])

@if ($this->isReorderable())
    @pushonce('filament-scripts:table')
        <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v0.x.x/dist/livewire-sortable.js"></script>
    @endpushonce
@endif

<div class="overflow-x-auto bg-white rounded shadow-xl">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-200">
            <tr>
                <th class="w-4 p-4 whitespace-nowrap">
                    <input
                        {{ $records->count() && $records->count() === count($selected) ? 'checked' : null }}
                        wire:click="toggleSelectAll"
                        type="checkbox"
                        class="border-gray-300 rounded shadow-sm text-primary-600 focus:border-primary-600 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                    />
                </th>

                @foreach ($table->getVisibleColumns() as $column)
                    <th class="px-6 py-3 text-left text-gray-600" scope="col">
                        @if ($this->isSortable() && $column->isSortable())
                            <button
                                wire:click="sortBy('{{ $column->getName() }}')"
                                type="button"
                                class="flex items-center space-x-1 text-xs font-medium tracking-wider text-left uppercase group focus:outline-none focus:underline"
                            >
                                <span>{{ __($column->getLabel()) }}</span>

                                <span class="relative flex items-center">
                                    @if ($sortColumn === $column->getName())
                                        <span>
                                            @if ($sortDirection === 'asc')
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            @elseif ($sortDirection === 'desc')
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                            @endif
                                        </span>
                                    @else
                                        <svg class="w-3 h-3 transition-opacity duration-300 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    @endif
                                </span>
                            </button>
                        @else
                            <span class="text-xs font-medium tracking-wider uppercase">{{ __($column->getLabel()) }}</span>
                        @endif
                    </th>
                @endforeach

                <th scope="col"></th>

                @if ($this->isReorderable())
                    <th scope="col"></th>
                @endif
            </tr>
        </thead>

        <tbody
            @if ($this->isReorderable()) wire:sortable="reorder" @endif
            class="text-sm leading-tight divide-y divide-gray-200"
        >
            @forelse ($records as $record)
                <tr
                    wire:key="{{ $record->getKey() }}"
                    wire:loading.class="opacity-50"
                    @if ($this->isReorderable()) wire:sortable.item="{{ $record->getKey() }}" @endif
                    class="{{ $loop->index % 2 ? 'bg-gray-50' : 'bg-white' }}"
                >
                    <td class="p-4 whitespace-nowrap">
                        <input
                            {{ in_array($record->getKey(), $selected) ? 'checked' : null }}
                            wire:click="toggleSelected('{{ $record->getKey() }}')"
                            type="checkbox"
                            class="border-gray-300 rounded shadow-sm text-primary-600 focus:border-primary-600 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        />
                    </td>

                    @foreach ($table->getVisibleColumns() as $column)
                        <td class="px-6 whitespace-nowrap">
                            {{ $column->renderCell($record) }}
                        </td>
                    @endforeach

                    <td class="px-6 py-4 whitespace-nowrap flex justify-end items-center space-x-2">
                        @foreach ($table->getRecordActions() as $recordAction)
                            {{ $recordAction->render($record) }}
                        @endforeach
                    </td>

                    @if ($this->isReorderable())
                        <td wire:sortable.handle class="px-6 border-l cursor-move whitespace-nowrap">
                            <x-heroicon-o-menu-alt-4 class="w-4 h-4 text-gray-500 mx-auto"/>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td
                        class="px-6 py-4 whitespace-nowrap"
                        colspan="{{ count($table->getVisibleColumns()) + ($this->isReorderable() ? 3 : 2) }}"
                    >
                        <div class="flex items-center justify-center h-16">
                            <p class="font-mono text-xs text-gray-500">
                                {{ __('tables::table.messages.noRecords') }}
                            </p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
