<div class="overflow-hidden border border-gray-300 divide-y bg-white shadow rounded-xl">
    <div class="flex items-center justify-between p-2">
        <div class="max-w-md">
            @if ($isSearchable())
                <label for="tableSearchQueryInput" class="sr-only">
                    Search
                </label>

                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center justify-center w-10 h-10 text-gray-400 transition pointer-events-none group-focus-within:text-primary-500">
                        <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="6.25" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                            <path fill="currentColor" d="M18.7197 19.7803C19.0126 20.0732 19.4874 20.0732 19.7803 19.7803C20.0732 19.4874 20.0732 19.0126 19.7803 18.7197L18.7197 19.7803ZM14.9697 16.0303L18.7197 19.7803L19.7803 18.7197L16.0303 14.9697L14.9697 16.0303Z"/>
                        </svg>
                    </span>

                    <input
                        wire:model="tableSearchQuery"
                        id="tableSearchQueryInput"
                        placeholder="Search"
                        type="search"
                        class="block w-full h-10 pl-10 placeholder-gray-400 transition duration-75 border-gray-300 rounded-lg shadow-sm focus:border-primary-60 focus:ring-1 focus:ring-inset focus:ring-primary-600"
                    >
                </div>
            @endif
        </div>
    </div>

    <div class="overflow-y-auto">
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
                @foreach (($records = $getRecords()) as $record)
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
    </div>

    @if ($isPaginationEnabled())
        <div class="p-2">
            <x-tables::pagination :paginator="$records" />
        </div>
    @endif
</div>
