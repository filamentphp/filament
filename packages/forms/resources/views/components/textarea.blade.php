@php
    $isConcealed = $isConcealed();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <textarea
        @if ($shouldAutosize())
            x-ignore
            ax-load
            ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('textarea', 'filament/forms') }}"
            x-data="textareaFormComponent()"
            x-on:input="render()"
            style="height: 150px"
            {{ $getExtraAlpineAttributeBag() }}
        @endif
        {{
            $attributes
                ->merge([
                    'autocapitalize' => $getAutocapitalize(),
                    'autocomplete' => $getAutocomplete(),
                    'autofocus' => $isAutofocused(),
                    'cols' => $getCols(),
                    'disabled' => $isDisabled(),
                    'dusk' => "filament.forms.{$statePath}",
                    'id' => $getId(),
                    'maxlength' => (! $isConcealed) ? $getMaxLength() : null,
                    'minlength' => (! $isConcealed) ? $getMinLength() : null,
                    'placeholder' => $getPlaceholder(),
                    'readonly' => $isReadOnly(),
                    'required' => $isRequired() && (! $isConcealed),
                    'rows' => $getRows(),
                    $applyStateBindingModifiers('wire:model') => $statePath,
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
                ->merge($getExtraInputAttributes(), escape: false)
                ->class([
                    'filament-forms-textarea-component block w-full transition duration-75 rounded-lg shadow-sm outline-none sm:text-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-primary-500',
                    'border-gray-300' => ! $errors->has($statePath),
                    'border-danger-600 ring-danger-600 dark:border-danger-400 dark:ring-danger-400' => $errors->has($statePath),
                ])
        }}
    ></textarea>
</x-dynamic-component>
