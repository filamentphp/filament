@php
    $affixLabelClasses = [
        'whitespace-nowrap group-focus-within:text-primary-500',
        'text-gray-400' => ! $errors->has($getStatePath()),
        'text-danger-400' => $errors->has($getStatePath()),
    ];
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    has-nested-recursive-validation-rules
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div {{ $attributes->merge($getExtraAttributes())->class(['filament-forms-select-component flex items-center space-x-1 rtl:space-x-reverse group']) }}>
        @if (($prefixAction = $getPrefixAction()) && (! $prefixAction->isHidden()))
            {{ $prefixAction }}
        @endif

        @if ($icon = $getPrefixIcon())
            <x-dynamic-component :component="$icon" class="w-5 h-5" />
        @endif

        @if ($label = $getPrefixLabel())
            <span @class($affixLabelClasses)>
                {{ $label }}
            </span>
        @endif

        <div class="flex-1 min-w-0">
            @unless ($isSearchable() || $isMultiple())
                <select
                    {!! $isAutofocused() ? 'autofocus' : null !!}
                    {!! $isDisabled() ? 'disabled' : null !!}
                    id="{{ $getId() }}"
                    {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                    dusk="filament.forms.{{ $getStatePath() }}"
                    @if (! $isConcealed())
                        {!! $isRequired() ? 'required' : null !!}
                    @endif
                    {{ $attributes->merge($getExtraInputAttributes())->merge($getExtraAttributes())->class([
                        'filament-forms-input text-gray-900 block w-full transition duration-75 rounded-lg shadow-sm outline-none focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70',
                        'dark:bg-gray-700 dark:text-white dark:focus:border-primary-500' => config('forms.dark_mode'),
                        'border-gray-300' => ! $errors->has($getStatePath()),
                        'dark:border-gray-600' => (! $errors->has($getStatePath())) && config('forms.dark_mode'),
                        'border-danger-600 ring-danger-600' => $errors->has($getStatePath()),
                        'dark:border-danger-400 dark:ring-danger-400' => $errors->has($getStatePath()) && config('forms.dark_mode'),
                    ]) }}
                >
                    @unless ($isPlaceholderSelectionDisabled())
                        <option value="">{{ $getPlaceholder() }}</option>
                    @endif

                    @foreach ($getOptions() as $value => $label)
                        <option
                            value="{{ $value }}"
                            {!! $isOptionDisabled($value, $label) ? 'disabled' : null !!}
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            @else
                <div
                    x-data="selectFormComponent({
                        isHtmlAllowed: @js($isHtmlAllowed()),
                        getOptionLabelUsing: async () => {
                            return await $wire.getSelectOptionLabel(@js($getStatePath()))
                        },
                        getOptionLabelsUsing: async () => {
                            return await $wire.getSelectOptionLabels(@js($getStatePath()))
                        },
                        getOptionsUsing: async () => {
                            return await $wire.getSelectOptions(@js($getStatePath()))
                        },
                        getSearchResultsUsing: async (search) => {
                            return await $wire.getSelectSearchResults(@js($getStatePath()), search)
                        },
                        isAutofocused: @js($isAutofocused()),
                        isMultiple: @js($isMultiple()),
                        hasDynamicOptions: @js($hasDynamicOptions()),
                        hasDynamicSearchResults: @js($hasDynamicSearchResults()),
                        loadingMessage: @js($getLoadingMessage()),
                        maxItems: @js($getMaxItems()),
                        maxItemsMessage: @js($getMaxItemsMessage()),
                        noSearchResultsMessage: @js($getNoSearchResultsMessage()),
                        options: @js($getOptionsForJs()),
                        optionsLimit: @js($getOptionsLimit()),
                        placeholder: @js($getPlaceholder()),
                        position: @js($getPosition()),
                        searchDebounce: @js($getSearchDebounce()),
                        searchingMessage: @js($getSearchingMessage()),
                        searchPrompt: @js($getSearchPrompt()),
                        state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
                    })"
                    x-on:keydown.esc="select.dropdown.isActive && $event.stopPropagation()"
                    wire:ignore
                    {{
                        $attributes
                            ->merge($getExtraAttributes())
                            ->merge($getExtraAlpineAttributes())
                            ->class(['filament-forms-input'])
                    }}
                    x-bind:class="{
                        'choices--error': (@js($getStatePath()) in $wire.__instance.serverMemo.errors),
                    }"
                >
                    <select
                        x-ref="input"
                        id="{{ $getId() }}"
                        {!! $isDisabled() ? 'disabled' : null !!}
                        {!! $isMultiple() ? 'multiple' : null !!}
                        {{ $getExtraInputAttributeBag() }}
                    ></select>
                </div>
            @endif
        </div>

        @if ($label = $getSuffixLabel())
            <span @class($affixLabelClasses)>
                {{ $label }}
            </span>
        @endif

        @if ($icon = $getSuffixIcon())
            <x-dynamic-component :component="$icon" class="w-5 h-5" />
        @endif

        @if (($suffixAction = $getSuffixAction()) && (! $suffixAction->isHidden()))
            {{ $suffixAction }}
        @endif
    </div>
</x-dynamic-component>
