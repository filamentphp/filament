@props([
    'blocks',
    'createAfterItem' => null,
    'statePath',
])

<div
    x-show="isCreateButtonDropdownOpen"
    x-on:click.away="isCreateButtonDropdownOpen = false"
    x-transition
    x-cloak
    {{ $attributes->class([
        'absolute z-20 mt-10 shadow-xl ring-1 ring-gray-900/10 overflow-hidden rounded-xl w-52 filament-forms-builder-component-block-picker',
        'dark:ring-white/20' => config('forms.dark_mode'),
    ]) }}
>
    <ul @class([
        'py-1 space-y-1 bg-white shadow rounded-xl',
        'dark:bg-gray-700 dark:divide-gray-600' => config('forms.dark_mode'),
    ])>
        @foreach ($blocks as $block)
            <x-forms::dropdown.item
                :wire:click="'dispatchFormEvent(\'builder::createItem\', \'' . $statePath . '\', \'' . $block->getName() . '\'' . ($createAfterItem ? ', \'' . $createAfterItem . '\'' : '') . ')'"
                x-on:click="isCreateButtonDropdownOpen = false"
                :icon="$block->getIcon()"
            >
                {{ $block->getLabel() }}
            </x-forms::dropdown.item>
        @endforeach
    </ul>
</div>
