@php
    use Filament\Support\Enums\GridDirection;
    use Illuminate\View\ComponentAttributeBag;

    $fieldWrapperView = $getFieldWrapperView();
    $extraInputAttributeBag = $getExtraInputAttributeBag();
    $gridDirection = $getGridDirection() ?? GridDirection::Column;
    $id = $getId();
    $isDisabled = $isDisabled();
    $isInline = $isInline();
    $statePath = $getStatePath();
    $wireModelAttribute = $applyStateBindingModifiers('wire:model');
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        {{
            $getExtraAttributeBag()
                ->when(! $isInline, fn (ComponentAttributeBag $attributes) => $attributes->grid($getColumns(), $gridDirection))
                ->class([
                    'fi-fo-radio',
                    'fi-inline' => $isInline,
                ])
        }}
    >
        @foreach ($getOptions() as $value => $label)
            @php
                $inputAttributes = $extraInputAttributeBag
                    ->merge([
                        'disabled' => $isDisabled || $isOptionDisabled($value, $label),
                        'id' => $id . '-' . $value,
                        'name' => $id,
                        'value' => $value,
                        $wireModelAttribute => $statePath,
                    ], escape: false);
            @endphp

            <label class="fi-fo-radio-label">
                <input
                    type="radio"
                    {{
                        $inputAttributes->class([
                            'fi-radio-input',
                            'fi-valid' => ! $errors->has($statePath),
                            'fi-invalid' => $errors->has($statePath),
                        ])
                    }}
                />

                <div class="fi-fo-radio-label-text">
                    <p>
                        {{ $label }}
                    </p>

                    @if ($hasDescription($value))
                        <p class="fi-fo-radio-label-description">
                            {{ $getDescription($value) }}
                        </p>
                    @endif
                </div>
            </label>
        @endforeach
    </div>
</x-dynamic-component>
