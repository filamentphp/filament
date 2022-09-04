@props([
    'blocks',
    'createAfterItem' => null,
    'statePath',
])

<div {{ $attributes->class(['filament-forms-builder-component-block-picker']) }}>
    @foreach ($blocks as $block)
        <x-forms::dropdown.item
            :wire:click="'dispatchFormEvent(\'builder::createItem\', \'' . $statePath . '\', \'' . $block->getName() . '\'' . ($createAfterItem ? ', \'' . $createAfterItem . '\'' : '') . ')'"
            :icon="$block->getIcon()"
            x-on:click="close"
        >
            {{ $block->getLabel() }}
        </x-forms::dropdown.item>
    @endforeach
</div>
