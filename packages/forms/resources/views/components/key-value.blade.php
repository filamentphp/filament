<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
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
        <div class="border border-gray-300 divide-y shadow-sm bg-white rounded-xl overflow-hidden">
            <table class="w-full text-left divide-y table-auto">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 whitespace-nowrap font-medium text-sm text-gray-600" scope="col">
                            {{ $getKeyLabel() }}
                        </th>

                        <th class="px-4 py-2 whitespace-nowrap font-medium text-sm text-gray-600" scope="col">
                            {{ $getValueLabel() }}
                        </th>

                        @if ($canDeleteRows() && $getDeleteButtonLabel() && (! $isDisabled()))
                            <th class="w-12" scope="col" x-show="rows.length > 1">
                                <span class="sr-only">
                                    {{ $getDeleteButtonLabel() }}
                                </span>
                            </th>
                        @endif
                    </tr>
                </thead>

                <tbody
                    x-ref="tableBody"
                    class="divide-y whitespace-nowrap"
                >
                    <template x-for="(row, index) in rows" x-bind:key="index" x-ref="rowTemplate">
                        <tr class="divide-x">
                            <td>
                                <input
                                    type="text"
                                    x-model="row.key"
                                    x-on:input="updateState"
                                    {!! ($placeholder = $getKeyPlaceholder()) ? "placeholder=\"{$placeholder}\"" : '' !!}
                                    @if ((! $canEditKeys()) || $isDisabled())
                                        disabled
                                    @endif
                                    class="w-full px-4 py-3 font-mono text-sm bg-transparent border-0 focus:ring-0"
                                >
                            </td>

                            <td class="whitespace-nowrap">
                                <input
                                    type="text"
                                    x-model="row.value"
                                    x-on:input="updateState"
                                    {!! ($placeholder = $getValuePlaceholder()) ? "placeholder=\"{$placeholder}\"" : '' !!}
                                    @if ((! $canEditValues()) || $isDisabled())
                                        disabled
                                    @endif
                                    class="w-full px-4 py-3 font-mono text-sm bg-transparent border-0 focus:ring-0"
                                >
                            </td>

                            @if ($canDeleteRows() && (! $isDisabled()))
                                <td x-show="rows.length > 1" class="whitespace-nowrap">
                                    <div class="flex items-center justify-center">
                                        <button
                                            x-on:click="deleteRow(index)"
                                            type="button"
                                            class="text-danger-600 hover:text-danger-700"
                                        >
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    </template>
                </tbody>
            </table>

            @if ($canAddRows() && (! $isDisabled()))
                <button
                    x-on:click="addRow"
                    type="button"
                    class="w-full px-4 py-2 flex items-center space-x-1 text-sm font-medium text-gray-800 hover:bg-gray-50 focus:bg-gray-50"
                >
                    <x-heroicon-s-plus class="w-4 h-4" />

                    <span>
                        {{ $getAddButtonLabel() }}
                    </span>
                </button>
            @endif
        </div>
    </div>
</x-forms::field-wrapper>
