@props([
    'blocks',
    'createAfterItem' => null,
    'statePath',
])

<x-filament-support::dropdown.list {{ $attributes->class(['filament-forms-builder-component-block-picker']) }}>
    @foreach ($blocks as $block)
        <x-filament-support::dropdown.list.item
            :wire:click="'dispatchFormEvent(\'builder::createItem\', \'' . $statePath . '\', \'' . $block->getName() . '\'' . ($createAfterItem ? ', \'' . $createAfterItem . '\'' : '') . ')'"
            :icon="$block->getIcon()"
            x-on:click="close"
        >
            {{ $block->getLabel() }}
        </x-filament-support::dropdown.list.item>
    @endforeach
</x-filament-support::dropdown.list>
