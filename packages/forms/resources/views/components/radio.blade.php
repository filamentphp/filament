@php
    use Filament\Support\Enums\GridDirection;
    use Illuminate\View\ComponentAttributeBag;

    $gridDirection = $getGridDirection() ?? GridDirection::Column;
    $id = $getId();
    $isDisabled = $isDisabled();
    $isInline = $isInline();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        {{
            $getExtraAttributeBag()
                ->when(! $isInline, fn (ComponentAttributeBag $attributes) => $attributes->grid($getColumns(), $gridDirection))
                ->class([
                    'fi-fo-radio gap-4',
                    '-mt-4' => (! $isInline) && ($gridDirection === GridDirection::Column),
                    'flex flex-wrap' => $isInline,
                ])
        }}
    >
        @foreach ($getOptions() as $value => $label)
            <div
                @class([
                    'break-inside-avoid pt-4' => (! $isInline) && ($gridDirection === GridDirection::Column),
                ])
            >
                <label class="flex gap-x-3">
                    <x-filament::input.radio
                        :valid="! $errors->has($statePath)"
                        :attributes="
                            \Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag())
                                ->merge([
                                    'disabled' => $isDisabled || $isOptionDisabled($value, $label),
                                    'id' => $id . '-' . $value,
                                    'name' => $id,
                                    'value' => $value,
                                    'wire:loading.attr' => 'disabled',
                                    $applyStateBindingModifiers('wire:model') => $statePath,
                                ], escape: false)
                                ->class(['mt-1'])
                        "
                    />

                    <div class="grid text-sm leading-6">
                        <span class="font-medium text-gray-950 dark:text-white">
                            {{ $label }}
                        </span>

                        @if ($hasDescription($value))
                            <p class="text-gray-500 dark:text-gray-400">
                                {{ $getDescription($value) }}
                            </p>
                        @endif
                    </div>
                </label>
            </div>
        @endforeach
    </div>
</x-dynamic-component>
