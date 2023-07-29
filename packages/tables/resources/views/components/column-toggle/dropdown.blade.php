@props([
    'form',
    'maxHeight' => null,
    'triggerAction',
    'width' => 'xs',
])

<x-filament::dropdown
    :max-height="$maxHeight"
    placement="bottom-end"
    shift
    :width="$width"
    wire:key="{{ $this->getId() }}.table.column-toggle"
    {{ $attributes->class(['fi-ta-col-toggle']) }}
>
    <x-slot name="trigger">
        <span
            @class([
                'inline-flex',
                '-mx-2' => $triggerAction->isIconButton(),
            ])
        >
            {{ $triggerAction }}
        </span>
    </x-slot>

    <div class="grid gap-y-4 p-6">
        <h4
            class="text-base font-semibold leading-6 text-gray-950 dark:text-white"
        >
            {{ __('filament-tables::table.column_toggle.heading') }}
        </h4>

        {{ $form }}
    </div>
</x-filament::dropdown>
