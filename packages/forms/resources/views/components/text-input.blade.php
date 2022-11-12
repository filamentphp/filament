@php
    $datalistOptions = $getDatalistOptions();
    $id = $getId();
    $statePath = $getStatePath();
    $inputClasses = [
        'block w-full transition duration-75 shadow-sm focus:border-primary-500 focus:relative focus:z-[1] focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500',
        'rounded-l-lg' => ! ($getPrefixLabel() || $getPrefixIcon()),
        'rounded-r-lg' => ! ($getSuffixLabel() || $getSuffixIcon()),
    ];
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <x-filament::input.affixes
        :state-path="$statePath"
        :prefix="$getPrefixLabel()"
        :prefix-action="$getPrefixAction()"
        :prefix-icon="$getPrefixIcon()"
        :suffix="$getSuffixLabel()"
        :suffix-action="$getSuffixAction()"
        :suffix-icon="$getSuffixIcon()"
        class="filament-forms-text-input-component"
        :attributes="$getExtraAttributeBag()"
    >
        <input
            @unless ($hasMask())
                x-data="{}"
                {{ $applyStateBindingModifiers('wire:model') }}="{{ $statePath }}"
                type="{{ $getType() }}"
            @else
                x-ignore
                ax-load
                ax-load-src="/js/filament/forms/components/text-input.js?v={{ \Composer\InstalledVersions::getVersion('filament/support') }}"
                x-data="textInputFormComponent({
                    {{ $hasMask() ? "getMaskOptionsUsing: (IMask) => ({$getJsonMaskConfiguration()})," : null }}
                    state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $statePath . '\')', lazilyEntangledModifiers: ['defer']) }},
                })"
                type="text"
                wire:ignore
                {!! $isLazy() ? "x-on:blur=\"\$wire.\$refresh\"" : null !!}
                {!! $isDebounced() ? "x-on:input.debounce.{$getDebounce()}=\"\$wire.\$refresh\"" : null !!}
                {{ $getExtraAlpineAttributeBag() }}
            @endunless
            dusk="filament.forms.{{ $statePath }}"
            {!! ($autocapitalize = $getAutocapitalize()) ? "autocapitalize=\"{$autocapitalize}\"" : null !!}
            {!! ($autocomplete = $getAutocomplete()) ? "autocomplete=\"{$autocomplete}\"" : null !!}
            {!! $isAutofocused() ? 'autofocus' : null !!}
            {!! $isDisabled() ? 'disabled' : null !!}
            id="{{ $id }}"
            {!! ($inputMode = $getInputMode()) ? "inputmode=\"{$inputMode}\"" : null !!}
            {!! $datalistOptions ? "list=\"{$id}-list\"" : null !!}
            {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
            {!! ($interval = $getStep()) ? "step=\"{$interval}\"" : null !!}
            @if (! $isConcealed())
                {!! filled($length = $getMaxLength()) ? "maxlength=\"{$length}\"" : null !!}
                {!! filled($value = $getMaxValue()) ? "max=\"{$value}\"" : null !!}
                {!! filled($length = $getMinLength()) ? "minlength=\"{$length}\"" : null !!}
                {!! filled($value = $getMinValue()) ? "min=\"{$value}\"" : null !!}
                {!! $isRequired() ? 'required' : null !!}
            @endif
            {{ $getExtraInputAttributeBag()->class($inputClasses) }}
            x-bind:class="{
                'border-gray-300 dark:border-gray-600': ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                'border-danger-600 ring-danger-600': (@js($statePath) in $wire.__instance.serverMemo.errors),
            }"
        />
    </x-filament::input.affixes>

    @if ($datalistOptions)
        <datalist id="{{ $id }}-list">
            @foreach ($datalistOptions as $option)
                <option value="{{ $option }}" />
            @endforeach
        </datalist>
    @endif
</x-dynamic-component>
