@props([
    'records' => [],
    'selected' => [],
    'sortColumn' => null,
    'sortDirection' => 'asc',
    'table',
])

<div class="shadow-xl rounded bg-white overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-4 w-4 whitespace-nowrap">
                    <input
                        {{ $records->count() && $records->count() === count($selected) ? 'checked' : null }}
                        wire:click="toggleSelectAll"
                        type="checkbox"
                        class="rounded text-secondary-700 shadow-sm focus:border-secondary-700 focus:ring focus:ring-secondary-200 focus:ring-opacity-50 border-gray-300"
                    />
                </th>

                @foreach ($table->getVisibleColumns() as $column)
                    <th class="px-6 py-3 text-left text-gray-500" scope="col">
                        @if ($table->isSortable() && $column->isSortable())
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

                @if ($table->recordButtonLabel)
                    <th scope="col">
                        <span class="sr-only">{{ __($table->recordButtonLabel) }}</span>
                    </th>
                @endif
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 text-sm leading-tight">
            @forelse ($records as $record)
                <tr
                    wire:loading.class="opacity-50"
                    class="{{ $loop->index % 2 ? 'bg-gray-50' : null }}"
                >
                    <td class="p-4 whitespace-nowrap">
                        <input
                            {{ in_array($record->getKey(), $selected) ? 'checked' : null }}
                            wire:click="toggleSelected('{{ $record->getKey() }}')"
                            type="checkbox"
                            class="rounded text-secondary-700 shadow-sm focus:border-secondary-700 focus:ring focus:ring-secondary-200 focus:ring-opacity-50 border-gray-300"
                        />
                    </td>

                    @foreach ($table->getVisibleColumns() as $column)
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $column->renderCell($record) }}
                        </td>
                    @endforeach

                    @if ($table->recordButtonLabel)
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            @if ($table->recordAction)
                                <button
                                    wire:click="{{ $table->recordAction }}('{{ $record->getKey() }}')"
                                    type="button"
                                    class="hover:underline text-secondary-500 hover:text-secondary-700 transition-colors duration-200 font-medium"
                                >
                                    {{ __($table->recordButtonLabel) }}
                                </button>
                            @elseif ($table->recordUrl)
                                <a
                                    href="{{ $table->getRecordUrl($record) }}"
                                    class="hover:underline text-secondary-500 hover:text-secondary-700 transition-colors duration-200 font-medium"
                                >
                                    {{ __($table->recordButtonLabel) }}
                                </a>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td
                        class="px-6 py-4 whitespace-nowrap"
                        colspan="{{ count($table->getVisibleColumns()) + 1 + ($table->recordUrl ? 1 : 0) }}"
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
