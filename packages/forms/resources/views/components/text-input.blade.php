@php
    $datalistOptions = $getDatalistOptions();

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
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div {{ $attributes->merge($getExtraAttributes())->class(['filament-forms-text-input-component flex relative group']) }}>
        @if (($prefixAction = $getPrefixAction()) && (! $prefixAction->isHidden()))
            {{ $prefixAction }}
        @endif

        @if ($icon = $getPrefixIcon())
            <div class="flex absolute inset-y-0 left-0 items-center pl-2 pointer-events-none">
                 <x-dynamic-component :component="$icon" class="w-5 h-5" />
            </div>
        @endif

        @if ($label = $getPrefixLabel())
            <span @class(array_merge($affixLabelClasses , ['inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 px-3 text-gray-500']))>
                {{ $label }}
            </span>
        @endif

        <div class="flex-1">
            <input
                @unless ($hasMask())
                    x-data="{}"
                    {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                    type="{{ $getType() }}"
                @else
                    x-data="textInputFormComponent({
                        {{ $hasMask() ? "getMaskOptionsUsing: (IMask) => ({$getJsonMaskConfiguration()})," : null }}
                        state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')', lazilyEntangledModifiers: ['defer']) }},
                    })"
                    type="text"
                    wire:ignore
                    {!! $isLazy() ? "x-on:blur=\"\$wire.\$refresh\"" : null !!}
                    {!! $isDebounced() ? "x-on:input.debounce.{$getDebounce()}=\"\$wire.\$refresh\"" : null !!}
                    {{ $getExtraAlpineAttributeBag() }}
                @endunless
                dusk="filament.forms.{{ $getStatePath() }}"
                {!! ($autocapitalize = $getAutocapitalize()) ? "autocapitalize=\"{$autocapitalize}\"" : null !!}
                {!! ($autocomplete = $getAutocomplete()) ? "autocomplete=\"{$autocomplete}\"" : null !!}
                {!! $isAutofocused() ? 'autofocus' : null !!}
                {!! $isDisabled() ? 'disabled' : null !!}
                id="{{ $getId() }}"
                {!! ($inputMode = $getInputMode()) ? "inputmode=\"{$inputMode}\"" : null !!}
                {!! $datalistOptions ? "list=\"{$getId()}-list\"" : null !!}
                {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
                {!! ($interval = $getStep()) ? "step=\"{$interval}\"" : null !!}
                @if (! $isConcealed())
                    {!! filled($length = $getMaxLength()) ? "maxlength=\"{$length}\"" : null !!}
                    {!! filled($value = $getMaxValue()) ? "max=\"{$value}\"" : null !!}
                    {!! filled($length = $getMinLength()) ? "minlength=\"{$length}\"" : null !!}
                    {!! filled($value = $getMinValue()) ? "min=\"{$value}\"" : null !!}
                    {!! $isRequired() ? 'required' : null !!}
                @endif
                {{ $getExtraInputAttributeBag()->class([
                    'block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70',
                    'dark:bg-gray-700 dark:text-white dark:focus:border-primary-500' => config('forms.dark_mode'),
                    $getPrefixIcon() ? 'ltr:pl-10 rtl:pr-10' : '',
                    $getSuffixIcon() ? 'ltr:pr-10 rtl:pl-10' : '',
                    $getPrefixLabel() && !$getSuffixLabel() ? 'rounded-r-lg' : '',
                    $getSuffixLabel() && !$getPrefixLabel() ? 'rounded-l-lg' : '',
                    !$getPrefixLabel() && !$getSuffixLabel() ? 'rounded-lg' : '',
                ]) }}
                x-bind:class="{
                    'border-gray-300': ! (@js($getStatePath()) in $wire.__instance.serverMemo.errors),
                    'dark:border-gray-600': ! (@js($getStatePath()) in $wire.__instance.serverMemo.errors) && @js(config('forms.dark_mode')),
                    'border-danger-600 ring-danger-600': (@js($getStatePath()) in $wire.__instance.serverMemo.errors),
                }"
            />
        </div>

        @if ($label = $getSuffixLabel())
            <span @class(array_merge($affixLabelClasses, ['inline-flex items-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-500']))>
                {{ $label }}
            </span>
        @endif

        @if ($icon = $getSuffixIcon())
            <div class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
                <x-dynamic-component :component="$icon" class="w-5 h-5" />
            </div>
        @endif

        @if (($suffixAction = $getSuffixAction()) && (! $suffixAction->isHidden()))
            {{ $suffixAction }}
        @endif
    </div>

    @if ($datalistOptions)
        <datalist id="{{ $getId() }}-list">
            @foreach ($datalistOptions as $option)
                <option value="{{ $option }}" />
            @endforeach
        </datalist>
    @endif
</x-dynamic-component>
