@php
    $gridDirection = $getGridDirection() ?? 'column';
    $id = $getId();
    $isDisabled = $isDisabled();
    $isInline = $isInline();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-filament::grid
        :default="$getColumns('default')"
        :sm="$getColumns('sm')"
        :md="$getColumns('md')"
        :lg="$getColumns('lg')"
        :xl="$getColumns('xl')"
        :two-xl="$getColumns('2xl')"
        :is-grid="! $isInline"
        :direction="$gridDirection"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($attributes)
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-fo-radio gap-3',
                    '-mt-2' => (! $isInline) && ($gridDirection === 'column'),
                    'flex flex-wrap' => $isInline,
                ])
        "
    >
        @foreach ($getOptions() as $value => $label)
            @php
                $inputId = "{$id}-{$value}";
                $shouldOptionBeDisabled = $isDisabled || $isOptionDisabled($value, $label);
            @endphp

            <div
                @class([
                    'break-inside-avoid pt-3' => (! $isInline) && ($gridDirection === 'column'),
                ])
            >
                <input
                    @disabled($shouldOptionBeDisabled)
                    id="{{ $inputId }}"
                    name="{{ $id }}"
                    type="radio"
                    value="{{ $value }}"
                    wire:loading.attr="disabled"
                    {{ $applyStateBindingModifiers('wire:model') }}="{{ $statePath }}"
                    {{ $getExtraInputAttributeBag()->class(['peer pointer-events-none absolute opacity-0']) }}
                />

                <x-filament::button
                    :color="$getColor($value)"
                    :disabled="$shouldOptionBeDisabled"
                    :for="$inputId"
                    :icon="$getIcon($value)"
                    tag="label"
                    class="cursor-pointer"
                >
                    {{ $label }}
                </x-filament::button>
            </div>
        @endforeach
    </x-filament::grid>
</x-dynamic-component>
