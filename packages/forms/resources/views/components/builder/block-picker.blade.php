@props([
    'blocks',
    'createAfterItem' => null,
    'statePath',
])

<x-forms::dropdown.list {{ $attributes->class(['filament-forms-builder-component-block-picker']) }}>
    @foreach ($blocks as $block)
        <x-forms::dropdown.list.item
            :wire:click="'dispatchFormEvent(\'builder::createItem\', \'' . $statePath . '\', \'' . $block->getName() . '\'' . ($createAfterItem ? ', \'' . $createAfterItem . '\'' : '') . ')'"
            :icon="$block->getIcon()"
            x-on:click="close"
        >
            {{ $block->getLabel() }}
        </x-forms::dropdown.list.item>
    @endforeach
</x-forms::dropdown.list>
