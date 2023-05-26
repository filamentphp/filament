@props([
    'blocks',
    'createAfterItem' => null,
    'statePath',
    'trigger',
    'grid_column'=> null,
    'width_column'=> null,
])

<x-forms::dropdown {{ $attributes->class(['filament-forms-builder-component-block-picker']) }}
  :width="$width_column">
    <x-slot name="trigger">
        {{ $trigger }}
    </x-slot>

    <x-forms::dropdown.list :class="'grid grid-cols-'.$grid_column">
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
</x-forms::dropdown>
