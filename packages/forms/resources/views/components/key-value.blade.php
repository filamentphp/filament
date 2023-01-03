<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $debounce = $getDebounce();
        $isDeletable = $isDeletable();
        $isDisabled = $isDisabled();
        $isReorderable = $isReorderable();
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

                        @if (($isDeletable || $isReorderable) && (! $isDisabled))
                            <th
                                scope="col"
                                x-show="rows.length > 1"
                                class="{{ ($isDeletable && $isReorderable) ? 'w-16' : 'w-12' }}"
                            >
                                <span class="sr-only"></span>
                            </th>
                        @endif
                    </tr>
                </thead>

                <tbody
                    @if ($isReorderable)
                        x-sortable
                        x-on:end="reorderRows($event)"
                    @endif
                    x-ref="tableBody"
                    class="divide-y whitespace-nowrap dark:divide-gray-600"
                >
                    <template x-for="(row, index) in rows" x-bind:key="index" x-ref="rowTemplate">
                        <tr
                            @if ($isReorderable)
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

                            @if (($isDeletable || $isReorderable) && (! $isDisabled))
                                <td x-show="rows.length > 1" class="whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        @if ($isReorderable)
                                            <button
                                                x-sortable-handle
                                                type="button"
                                                class="text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                            >
                                                <x-filament::icon
                                                    name="heroicon-m-arrows-up-down"
                                                    alias="filament-forms::components.key-value.buttons.reorder"
                                                    size="h-4 w-4"
                                                />

                                                <span class="sr-only">
                                                    {{ $getReorderButtonLabel() }}
                                                </span>
                                            </button>
                                        @endif

                                        @if ($isDeletable)
                                            <button
                                                x-on:click="deleteRow(index)"
                                                type="button"
                                                class="text-danger-600 hover:text-danger-700"
                                            >
                                                <x-filament::icon
                                                    name="heroicon-m-trash"
                                                    alias="filament-forms::components.key-value.buttons.delete"
                                                    size="h-4 w-4"
                                                />

                                                <span class="sr-only">
                                                    {{ $getDeleteButtonLabel() }}
                                                </span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    </template>
                </tbody>
            </table>

            @if ($isAddable() && (! $isDisabled))
                <button
                    x-on:click="addRow"
                    type="button"
                    class="w-full px-4 py-2 flex items-center space-x-1 rtl:space-x-reverse text-sm font-medium text-gray-800 hover:bg-gray-50 focus:bg-gray-50 dark:text-white dark:bg-gray-800/60 dark:hover:bg-gray-800/30"
                >
                    <x-filament::icon
                        name="heroicon-m-plus"
                        alias="filament-forms::components.key-value.buttons.add"
                        size="h-4 w-4"
                    />

                    <span>
                        {{ $getAddButtonLabel() }}
                    </span>
                </button>
            @endif
        </div>
    </div>
</x-dynamic-component>
