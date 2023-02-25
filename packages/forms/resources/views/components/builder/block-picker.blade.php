@props([
    'blocks',
    'afterItem' => null,
    'statePath',
    'trigger',
])

<x-filament::dropdown {{ $attributes->class(['filament-forms-builder-component-block-picker']) }}>
    <x-slot name="trigger">
        {{ $trigger }}
    </x-slot>

    <x-filament::dropdown.list>
        @foreach ($blocks as $block)
            <x-filament::dropdown.list.item
                :wire:click="'dispatchFormEvent(\'builder::add\', \'' . $statePath . '\', \'' . $block->getName() . '\'' . ($afterItem ? ', \'' . $afterItem . '\'' : '') . ')'"
                :icon="$block->getIcon()"
                x-on:click="close"
            >
                {{ $block->getLabel() }}
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
