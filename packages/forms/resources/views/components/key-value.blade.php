<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div
        x-data="keyValueFormComponent({
                    state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
                })"
        {{ $attributes->merge($getExtraAttributes())->class(['filament-forms-key-value-component']) }}
        {{ $getExtraAlpineAttributeBag() }}
    >
        <div
            @class([
                'divide-y overflow-hidden rounded-xl border border-gray-300 bg-white shadow-sm',
                'dark:divide-gray-600 dark:border-gray-600 dark:bg-gray-700' => config('forms.dark_mode'),
            ])
        >
            <table
                @class([
                    'w-full table-auto divide-y text-start',
                    'dark:divide-gray-700' => config('forms.dark_mode'),
                ])
            >
                <thead>
                    <tr
                        @class([
                            'bg-gray-50',
                            'dark:bg-gray-800/60' => config('forms.dark_mode'),
                        ])
                    >
                        <th
                            @class([
                                'whitespace-nowrap px-4 py-2 text-start text-sm font-medium text-gray-600',
                                'dark:text-gray-300' => config('forms.dark_mode'),
                            ])
                            scope="col"
                        >
                            {{ $getKeyLabel() }}
                        </th>

                        <th
                            @class([
                                'whitespace-nowrap px-4 py-2 text-start text-sm font-medium text-gray-600',
                                'dark:text-gray-300' => config('forms.dark_mode'),
                            ])
                            scope="col"
                        >
                            {{ $getValueLabel() }}
                        </th>

                        @if (($canDeleteRows() || $isReorderable()) && $isEnabled())
                            <th
                                scope="col"
                                x-show="rows.length"
                                class="{{ ($canDeleteRows() && $isReorderable()) ? 'w-16' : 'w-12' }}"
                            >
                                <span class="sr-only"></span>
                            </th>
                        @endif
                    </tr>
                </thead>

                <tbody
                    @if ($isReorderable())
                        x-sortable
                        x-on:end="reorderRows($event)"
                    @endif
                    x-ref="tableBody"
                    @class([
                        'divide-y whitespace-nowrap',
                        'dark:divide-gray-600' => config('forms.dark_mode'),
                    ])
                >
                    <template
                        x-for="(row, index) in rows"
                        x-bind:key="index"
                        x-ref="rowTemplate"
                    >
                        <tr
                            @if ($isReorderable())
                                x-bind:x-sortable-item="row.key"
                            @endif
                            @class([
                                'divide-x rtl:divide-x-reverse',
                                'dark:divide-gray-600' => config('forms.dark_mode'),
                            ])
                        >
                            <td>
                                <input
                                    type="text"
                                    x-model="row.key"
                                    x-on:input.debounce.{{ $getDebounce() ?? '500ms' }}="updateState"
                                    {!! ($placeholder = $getKeyPlaceholder()) ? "placeholder=\"{$placeholder}\"" : '' !!}
                                    @if ((! $canEditKeys()) || $isDisabled())
                                        disabled
                                    @endif
                                    class="w-full border-0 bg-transparent px-4 py-3 font-mono text-sm focus:ring-0"
                                />
                            </td>

                            <td class="whitespace-nowrap">
                                <input
                                    type="text"
                                    x-model="row.value"
                                    x-on:input.debounce.{{ $getDebounce() ?? '500ms' }}="updateState"
                                    {!! ($placeholder = $getValuePlaceholder()) ? "placeholder=\"{$placeholder}\"" : '' !!}
                                    @if ((! $canEditValues()) || $isDisabled())
                                        disabled
                                    @endif
                                    class="w-full border-0 bg-transparent px-4 py-3 font-mono text-sm focus:ring-0"
                                />
                            </td>

                            @if (($canDeleteRows() || $isReorderable()) && $isEnabled())
                                <td class="whitespace-nowrap">
                                    <div
                                        class="flex items-center justify-center gap-2"
                                    >
                                        @if ($isReorderable())
                                            <button
                                                x-sortable-handle
                                                type="button"
                                                class="text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                            >
                                                <x-heroicon-o-switch-vertical
                                                    class="h-4 w-4"
                                                />

                                                <span class="sr-only">
                                                    {{ $getReorderButtonLabel() }}
                                                </span>
                                            </button>
                                        @endif

                                        @if ($canDeleteRows())
                                            <button
                                                x-on:click="deleteRow(index)"
                                                type="button"
                                                class="text-danger-600 hover:text-danger-700"
                                            >
                                                <x-heroicon-o-trash
                                                    class="h-4 w-4"
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

            @if ($canAddRows() && $isEnabled())
                <button
                    x-on:click="addRow"
                    type="button"
                    @class([
                        'flex w-full items-center space-x-1 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-50 focus:bg-gray-50 rtl:space-x-reverse',
                        'dark:bg-gray-800/60 dark:text-white dark:hover:bg-gray-800/30' => config('forms.dark_mode'),
                    ])
                >
                    <x-heroicon-s-plus class="h-4 w-4" />

                    <span>
                        {{ $getAddButtonLabel() }}
                    </span>
                </button>
            @endif
        </div>
    </div>
</x-dynamic-component>
