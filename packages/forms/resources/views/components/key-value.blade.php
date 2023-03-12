<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $addAction = $getAction('add');
        $deleteAction = $getAction('delete');
        $reorderAction = $getAction('reorder');

        $debounce = $getDebounce();
        $isDisabled = $isDisabled();
        $statePath = $getStatePath();
    @endphp

    <div
        x-ignore
        ax-load
        ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('key-value', 'filament/forms') }}"
        x-data="keyValueFormComponent({
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $statePath . '\')') }},
        })"
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->merge($getExtraAlpineAttributes(), escape: false)
                ->class(['filament-forms-key-value-component'])
        }}
    >
        <div class="border border-gray-300 divide-y shadow-sm bg-white rounded-xl overflow-hidden dark:bg-gray-700 dark:border-gray-600 dark:divide-gray-600">
            <table class="w-full text-start divide-y table-auto dark:divide-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/60">
                        <th
                            class="px-4 py-2 whitespace-nowrap font-medium text-sm text-gray-600 dark:text-gray-300"
                            scope="col"
                        >
                            {{ $getKeyLabel() }}
                        </th>

                        <th
                            class="px-4 py-2 whitespace-nowrap font-medium text-sm text-gray-600 dark:text-gray-300"
                            scope="col"
                        >
                            {{ $getValueLabel() }}
                        </th>

                        @if (($deleteAction || $reorderAction) && (! $isDisabled))
                            <th
                                scope="col"
                                x-show="rows.length > 1"
                                class="{{ ($deleteAction && $reorderAction) ? 'w-16' : 'w-12' }}"
                            >
                                <span class="sr-only"></span>
                            </th>
                        @endif
                    </tr>
                </thead>

                <tbody
                    @if ($reorderAction)
                        x-sortable
                        x-on:end="reorderRows($event)"
                    @endif
                    x-ref="tableBody"
                    class="divide-y whitespace-nowrap dark:divide-gray-600"
                >
                    <template x-for="(row, index) in rows" x-bind:key="index" x-ref="rowTemplate">
                        <tr
                            @if ($reorderAction)
                                x-bind:x-sortable-item="row.key"
                            @endif
                            class="divide-x rtl:divide-x-reverse dark:divide-gray-600"
                        >
                            <td>
                                <input
                                    type="text"
                                    x-model="row.key"
                                    x-on:input.debounce.{{ $debounce ?? '500ms' }}="updateState"
                                    @if ($placeholder = $getKeyPlaceholder()) placeholder="{{ $placeholder }}" @endif
                                    @if ((! $canEditKeys()) || $isDisabled)
                                        disabled
                                    @endif
                                    class="w-full px-4 py-3 font-mono text-sm bg-transparent border-0 focus:ring-0"
                                >
                            </td>

                            <td class="whitespace-nowrap">
                                <input
                                    type="text"
                                    x-model="row.value"
                                    x-on:input.debounce.{{ $debounce ?? '500ms' }}="updateState"
                                    @if ($placeholder = $getValuePlaceholder()) placeholder="{{ $placeholder }}" @endif
                                    @if ((! $canEditValues()) || $isDisabled)
                                        disabled
                                    @endif
                                    class="w-full px-4 py-3 font-mono text-sm bg-transparent border-0 focus:ring-0"
                                >
                            </td>

                            @if (($deleteAction || $reorderAction) && (! $isDisabled))
                                <td x-show="rows.length > 1" class="whitespace-nowrap">
                                    <div class="flex items-center justify-center px-2">
                                        @if ($reorderAction)
                                            <div x-sortable-handle>
                                                {{ $reorderAction }}
                                            </div>
                                        @endif

                                        @if ($deleteAction)
                                            <div x-on:click="deleteRow(index)">
                                                {{ $deleteAction }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    </template>
                </tbody>
            </table>

            @if ($addAction && (! $isDisabled))
                <div x-on:click="addRow" class="px-4 py-3">
                    {{ $addAction }}
                </div>
            @endif
        </div>
    </div>
</x-dynamic-component>
