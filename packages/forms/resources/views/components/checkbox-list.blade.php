<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $isBulkToggleable = $isBulkToggleable();
        $isDisabled = $isDisabled();
        $statePath = $getStatePath();
    @endphp

    @if ($isBulkToggleable)
    <div x-data="{
        checkboxes: $root.querySelectorAll('input[type=checkbox]'),

        isAllSelected: false,

        init: function () {
            this.updateIsAllSelected()
        },

        updateIsAllSelected: function () {
            this.isAllSelected = this.checkboxes.length === this.$root.querySelectorAll('input[type=checkbox]:checked').length
        },

        toggleAll: function () {
            state = !this.isAllSelected

            this.checkboxes.forEach((checkbox) => {
                checkbox.checked = state
                checkbox.dispatchEvent(new Event('change'))
            })
        },
    }">
        <div class="mb-2">
            <x-filament::link
                tag="button"
                size="sm"
                x-show="!isAllSelected"
                x-on:click="toggleAll"
            >
                {{ __('forms::components.checkbox_list.buttons.select_all.label') }}
            </x-filament::link>

            <x-filament::link
                tag="button"
                size="sm"
                x-show="isAllSelected"
                x-on:click="toggleAll"
            >
                {{ __('forms::components.checkbox_list.buttons.deselect_all.label') }}
            </x-filament::link>
        </div>
    @endif

        <x-filament::grid
            :default="$getColumns('default')"
            :sm="$getColumns('sm')"
            :md="$getColumns('md')"
            :lg="$getColumns('lg')"
            :xl="$getColumns('xl')"
            :two-xl="$getColumns('2xl')"
            direction="column"
            :attributes="$attributes->class(['filament-forms-checkbox-list-component gap-1 space-y-2'])"
        >
            @foreach ($getOptions() as $optionValue => $optionLabel)
                <label class="flex items-center space-x-3 rtl:space-x-reverse">
                    <input
                        @if ($isBulkToggleable)
                            x-on:change="updateIsAllSelected"
                        @endif
                        {{
                            $getExtraAttributeBag()
                                ->merge([
                                    'disabled' => $isDisabled,
                                    'dusk' => "filament.forms.{$statePath}",
                                    'type' => 'checkbox',
                                    'value' => $optionValue,
                                    'wire:loading.attr' => 'disabled',
                                    $applyStateBindingModifiers('wire:model') => $statePath,
                                ], escape: false)
                                ->class([
                                    'text-primary-600 transition duration-75 rounded shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:checked:bg-primary-500',
                                    'border-gray-300 dark:border-gray-600' => ! $errors->has($statePath),
                                    'border-danger-300 ring-danger-500 dark:border-danger-400 dark:ring-danger-400' => $errors->has($statePath),
                                ])
                        }}
                    />

                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ $optionLabel }}
                    </span>
                </label>
            @endforeach
        </x-filament::grid>

    @if ($isBulkToggleable)
    </div>
    @endif
</x-dynamic-component>
