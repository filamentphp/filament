@php
    use Filament\Support\Enums\GridDirection;
    use Illuminate\View\ComponentAttributeBag;

    $fieldWrapperView = $getFieldWrapperView();
    $gridDirection = $getGridDirection() ?? GridDirection::Column;
    $hasInlineLabel = $hasInlineLabel();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isInline = $isInline();
    $isMultiple = $isMultiple();
    $statePath = $getStatePath();
    $areButtonLabelsHidden = $areButtonLabelsHidden();
    $wireModelAttribute = $applyStateBindingModifiers('wire:model');
    $extraInputAttributeBag = $getExtraInputAttributeBag()->class(['fi-fo-toggle-buttons-input']);
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    :has-inline-label="$hasInlineLabel"
    class="fi-fo-toggle-buttons-wrp"
>
    <div
        {{
            $getExtraAttributeBag()
                ->when(! $isInline, fn (ComponentAttributeBag $attributes) => $attributes->grid($getColumns(), $gridDirection))
                ->class([
                    'fi-fo-toggle-buttons',
                    'fi-inline' => $isInline,
                ])
        }}
    >
        @foreach ($getOptions() as $value => $label)
            @php
                $inputId = "{$id}-{$value}";
                $shouldOptionBeDisabled = $isDisabled || $isOptionDisabled($value, $label);
                $color = $getColor($value);
                $icon = $getIcon($value);
            @endphp

            <div class="fi-fo-toggle-buttons-btn-ctn">
                <input
                    @disabled($shouldOptionBeDisabled)
                    id="{{ $inputId }}"
                    @if (! $isMultiple)
                        name="{{ $id }}"
                    @endif
                    type="{{ $isMultiple ? 'checkbox' : 'radio' }}"
                    value="{{ $value }}"
                    wire:loading.attr="disabled"
                    {{ $wireModelAttribute }}="{{ $statePath }}"
                    {{ $extraInputAttributeBag }}
                />

                <x-filament::button
                    :color="$color"
                    :disabled="$shouldOptionBeDisabled"
                    :for="$inputId"
                    :icon="$icon"
                    :label-sr-only="$areButtonLabelsHidden"
                    tag="label"
                >
                    {{ $label }}
                </x-filament::button>
            </div>
        @endforeach
    </div>
</x-dynamic-component>
