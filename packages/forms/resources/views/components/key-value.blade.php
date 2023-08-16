<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $debounce = $getLiveDebounce();
        $isAddable = $isAddable();
        $isDeletable = $isDeletable();
        $isDisabled = $isDisabled();
        $isReorderable = $isReorderable();
        $statePath = $getStatePath();
    @endphp

    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-fo-key-value rounded-lg shadow-sm ring-1 transition duration-75 focus-within:ring-2',
                    'bg-white dark:bg-white/5' => ! $isDisabled,
                    'bg-gray-50 dark:bg-transparent' => $isDisabled,
                    'ring-gray-950/10 focus-within:ring-primary-600 dark:focus-within:ring-primary-500' => ! $errors->has($statePath),
                    'dark:ring-white/20' => (! $errors->has($statePath)) && (! $isDisabled),
                    'dark:ring-white/10' => (! $errors->has($statePath)) && $isDisabled,
                    'ring-danger-600 focus-within:ring-danger-600 dark:ring-danger-500 dark:focus-within:ring-danger-500' => $errors->has($statePath),
                ])
        }}
    >
        <div
            ax-load
            ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('key-value', 'filament/forms') }}"
            wire:ignore
            x-data="keyValueFormComponent({
                        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                    })"
            x-ignore
            {{
                $attributes
                    ->merge($getExtraAlpineAttributes(), escape: false)
                    ->class(['divide-y divide-gray-200 dark:divide-white/10'])
            }}
        >
            <table
                class="w-full table-auto divide-y divide-gray-200 dark:divide-white/5"
            >
                <thead>
                    <tr>
                        @if ($isReorderable && (! $isDisabled))
                            <th
                                scope="col"
                                x-show="rows.length"
                                class="w-9"
                            ></th>
                        @endif

                        <th
                            scope="col"
                            class="px-3 py-2 text-start text-sm font-medium text-gray-700 dark:text-gray-200"
                        >
                            {{ $getKeyLabel() }}
                        </th>

                        <th
                            scope="col"
                            class="px-3 py-2 text-start text-sm font-medium text-gray-700 dark:text-gray-200"
                        >
                            {{ $getValueLabel() }}
                        </th>

                        @if ($isDeletable && (! $isDisabled))
                            <th
                                scope="col"
                                x-show="rows.length"
                                class="w-9"
                            ></th>
                        @endif
                    </tr>
                </thead>

                <tbody
                    @if ($isReorderable)
                        x-on:end="reorderRows($event)"
                        x-sortable
                    @endif
                    class="divide-y divide-gray-200 dark:divide-white/5"
                >
                    <template
                        x-bind:key="index"
                        x-for="(row, index) in rows"
                    >
                        <tr
                            @if ($isReorderable)
                                x-bind:x-sortable-item="row.key"
                            @endif
                            class="divide-x divide-gray-200 rtl:divide-x-reverse dark:divide-white/5"
                        >
                            @if ($isReorderable && (! $isDisabled))
                                <td class="p-0.5">
                                    <div x-sortable-handle class="flex">
                                        {{ $getAction('reorder') }}
                                    </div>
                                </td>
                            @endif

                            <td class="w-1/2 p-0">
                                <x-filament::input
                                    :disabled="(! $canEditKeys()) || $isDisabled"
                                    :placeholder="filled($placeholder = $getKeyPlaceholder()) ? $placeholder : null"
                                    type="text"
                                    x-model="row.key"
                                    :attributes="
                                        \Filament\Support\prepare_inherited_attributes(
                                            new \Illuminate\View\ComponentAttributeBag([
                                                'x-on:input.debounce.' . ($debounce ?? '500ms') => 'updateState',
                                            ])
                                        )
                                    "
                                    class="font-mono"
                                />
                            </td>

                            <td class="w-1/2 p-0">
                                <x-filament::input
                                    :disabled="(! $canEditValues()) || $isDisabled"
                                    :placeholder="filled($placeholder = $getValuePlaceholder()) ? $placeholder : null"
                                    type="text"
                                    x-model="row.value"
                                    :attributes="
                                        \Filament\Support\prepare_inherited_attributes(
                                            new \Illuminate\View\ComponentAttributeBag([
                                                'x-on:input.debounce.' . ($debounce ?? '500ms') => 'updateState',
                                            ])
                                        )
                                    "
                                    class="font-mono"
                                />
                            </td>

                            @if ($isDeletable && (! $isDisabled))
                                <td class="p-0.5">
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
                <div class="flex justify-center px-3 py-2">
                    <span x-on:click="addRow" class="flex">
                        {{ $getAction('add') }}
                    </span>
                </div>
            @endif
        </div>
    </div>
</x-dynamic-component>
