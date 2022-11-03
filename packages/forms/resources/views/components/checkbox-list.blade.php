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
    @if ($isBulkToggleable())
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
            <x-forms::link
                tag="button"
                size="sm"
                x-show="!isAllSelected"
                x-on:click="toggleAll"
            >
                {{ __('forms::components.checkbox_list.buttons.select_all.label') }}
            </x-forms::link>

            <x-forms::link
                tag="button"
                size="sm"
                x-show="isAllSelected"
                x-on:click="toggleAll"
            >
                {{ __('forms::components.checkbox_list.buttons.deselect_all.label') }}
            </x-forms::link>
        </div>
    @endif

        <x-filament-support::grid
            :default="$getColumns('default')"
            :sm="$getColumns('sm')"
            :md="$getColumns('md')"
            :lg="$getColumns('lg')"
            :xl="$getColumns('xl')"
            :two-xl="$getColumns('2xl')"
            direction="column"
            :attributes="$attributes->class(['filament-forms-checkbox-list-component gap-1 space-y-2'])"
        >
            @php
                $isDisabled = $isDisabled();
            @endphp

            @foreach ($getOptions() as $optionValue => $optionLabel)
                <label class="flex items-center space-x-3 rtl:space-x-reverse">
                    <input
                        {!! $isDisabled ? 'disabled' : null !!}
                        wire:loading.attr="disabled"
                        type="checkbox"
                        value="{{ $optionValue }}"
                        dusk="filament.forms.{{ $getStatePath() }}"
                        x-on:change="updateIsAllSelected"
                        {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                        {{ $getExtraAttributeBag()->class([
                            'text-primary-600 transition duration-75 rounded shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 disabled:opacity-70',
                            'dark:bg-gray-700 dark:checked:bg-primary-500' => config('forms.dark_mode'),
                            'border-gray-300' => ! $errors->has($getStatePath()),
                            'dark:border-gray-600' => (! $errors->has($getStatePath())) && config('forms.dark_mode'),
                            'border-danger-300 ring-danger-500' => $errors->has($getStatePath()),
                        ]) }}
                    />

                    <span @class([
                        'text-sm font-medium text-gray-700',
                        'dark:text-gray-200' => config('forms.dark_mode'),
                    ])>
                        {{ $optionLabel }}
                    </span>
                </label>
            @endforeach
        </x-filament-support::grid>

    @if ($isBulkToggleable())
    </div>
    @endif
</x-dynamic-component>
