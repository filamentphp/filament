@props([
    'blocks',
    'createAfterItem' => null,
    'statePath',
])

<x-filament-forms::dropdown.list {{ $attributes->class(['filament-forms-builder-component-block-picker']) }}>
    @foreach ($blocks as $block)
        <x-filament-forms::dropdown.list.item
            :wire:click="'dispatchFormEvent(\'builder::createItem\', \'' . $statePath . '\', \'' . $block->getName() . '\'' . ($createAfterItem ? ', \'' . $createAfterItem . '\'' : '') . ')'"
            :icon="$block->getIcon()"
            x-on:click="close"
        >
            {{ $block->getLabel() }}
        </x-filament-forms::dropdown.list.item>
    @endforeach
</x-filament-forms::dropdown.list>
