@props([
    'blocks',
    'afterItem' => null,
    'statePath',
])

<x-filament::dropdown.list {{ $attributes->class(['filament-forms-builder-component-block-picker']) }}>
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
