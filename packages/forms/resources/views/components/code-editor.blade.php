@php
    $fieldWrapperView = $getFieldWrapperView();
    $extraAttributeBag = $getExtraAttributeBag();
    $isDisabled = $isDisabled();
    $key = $getKey();
    $language = $getLanguage();
    $statePath = $getStatePath();
    $livewireKey = $getLivewireKey();
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :valid="! $errors->has($statePath)"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($extraAttributeBag)
                ->class(['fi-fo-code-editor'])
        "
    >
        <div
            x-load
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('code-editor', 'filament/forms') }}"
            x-data="codeEditorFormComponent({
                        isDisabled: @js($isDisabled),
                        language: @js($language?->value),
                        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                    })"
            wire:ignore
            wire:key="{{ $livewireKey }}.{{
                substr(md5(serialize([
                    $isDisabled,
                    $language?->value,
                ])), 0, 64)
            }}"
            {{ $getExtraAlpineAttributeBag() }}
        >
            <div x-ref="editor" x-cloak></div>
        </div>
    </x-filament::input.wrapper>
</x-dynamic-component>
