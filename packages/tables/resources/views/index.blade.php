<div class="overflow-hidden bg-white shadow rounded-xl">
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
            @foreach ($getRecords() as $record)
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
