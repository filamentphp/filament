@php
    $fieldWrapperView = $getFieldWrapperView();
    $extraAttributeBag = $getExtraAttributeBag();
    $canEditKeys = $canEditKeys();
    $canEditValues = $canEditValues();
    $keyPlaceholder = $getKeyPlaceholder();
    $valuePlaceholder = $getValuePlaceholder();
    $debounce = $getLiveDebounce();
    $hasInlineLabel = $hasInlineLabel();
    $isAddable = $isAddable();
    $isDeletable = $isDeletable();
    $isDisabled = $isDisabled();
    $isReorderable = $isReorderable();
    $statePath = $getStatePath();
    $livewireKey = $getLivewireKey();
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    :has-inline-label="$hasInlineLabel"
    class="fi-fo-key-value-wrp"
>
    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :valid="! $errors->has($statePath)"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($extraAttributeBag)
                ->class(['fi-fo-key-value'])
        "
    >
        <div
            x-load
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('key-value', 'filament/forms') }}"
            x-data="keyValueFormComponent({
                        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                    })"
            wire:ignore
            wire:key="{{ $livewireKey }}.{{
                substr(md5(serialize([
                    $isDisabled,
                ])), 0, 64)
            }}"
            {{
                $attributes
                    ->merge($getExtraAlpineAttributes(), escape: false)
                    ->class(['fi-fo-key-value-table-ctn'])
            }}
        >
            <table class="fi-fo-key-value-table">
                <thead>
                    <tr>
                        @if ($isReorderable && (! $isDisabled))
                            <th
                                scope="col"
                                x-show="rows.length"
                                class="fi-has-action"
                            ></th>
                        @endif

                        <th scope="col">
                            {{ $getKeyLabel() }}
                        </th>

                        <th scope="col">
                            {{ $getValueLabel() }}
                        </th>

                        @if ($isDeletable && (! $isDisabled))
                            <th
                                scope="col"
                                x-show="rows.length"
                                class="fi-has-action"
                            ></th>
                        @endif
                    </tr>
                </thead>

                <tbody
                    @if ($isReorderable)
                        x-on:end.stop="reorderRows($event)"
                        x-sortable
                        data-sortable-animation-duration="{{ $getReorderAnimationDuration() }}"
                    @endif
                >
                    <template
                        x-bind:key="index"
                        x-for="(row, index) in rows"
                    >
                        <tr
                            @if ($isReorderable)
                                x-bind:x-sortable-item="row.key"
                            @endif
                        >
                            @if ($isReorderable && (! $isDisabled))
                                <td class="fi-has-action">
                                    <div
                                        x-sortable-handle
                                        class="fi-fo-key-value-table-row-sortable-handle"
                                    >
                                        {{ $getAction('reorder') }}
                                    </div>
                                </td>
                            @endif

                            <td>
                                <input
                                    @disabled((! $canEditKeys) || $isDisabled)
                                    placeholder="{{ $keyPlaceholder }}"
                                    type="text"
                                    x-model="row.key"
                                    x-on:input.debounce.{{ $debounce ?? '500ms' }}="updateState"
                                    class="fi-input"
                                />
                            </td>

                            <td>
                                <input
                                    @disabled((! $canEditValues) || $isDisabled)
                                    placeholder="{{ $valuePlaceholder }}"
                                    type="text"
                                    x-model="row.value"
                                    x-on:input.debounce.{{ $debounce ?? '500ms' }}="updateState"
                                    class="fi-input"
                                />
                            </td>

                            @if ($isDeletable && (! $isDisabled))
                                <td class="fi-has-action">
                                    <div x-on:click="deleteRow(index)">
                                        {{ $getAction('delete') }}
                                    </div>
                                </td>
                            @endif
                        </tr>
                    </template>
                </tbody>
            </table>

            @if ($isAddable && (! $isDisabled))
                <div
                    x-on:click="addRow"
                    class="fi-fo-key-value-add-action-ctn"
                >
                    {{ $getAction('add') }}
                </div>
            @endif
        </div>
    </x-filament::input.wrapper>
</x-dynamic-component>
