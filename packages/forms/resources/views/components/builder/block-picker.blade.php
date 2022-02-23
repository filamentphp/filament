@props([
    'blocks',
    'createAfterItem' => null,
    'statePath',
])

<div
    x-show="isCreateButtonDropdownOpen"
    x-on:click.away="isCreateButtonDropdownOpen = false"
    x-transition
    {{ $attributes->class([
        'absolute z-20 mt-10 shadow-xl border overflow-hidden rounded-xl w-52 filament-forms-builder-component-block-picker',
        'dark:border-gray-600' => config('forms.dark_mode'),
    ]) }}
>
    <ul @class([
        'py-1 space-y-1 bg-white shadow rounded-xl',
        'dark:bg-gray-700 dark:divide-gray-600' => config('forms.dark_mode'),
    ])>
        @foreach ($blocks as $block)
            <li>
                <button
                    wire:click="dispatchFormEvent('builder::createItem', '{{ $statePath }}', '{{ $block->getName() }}' {{ $createAfterItem ? ", '{$createAfterItem}'" : '' }})"
                    x-on:click="isCreateButtonDropdownOpen = false"
                    type="button"
                    class="flex items-center w-full h-8 px-3 text-sm font-medium focus:outline-none hover:text-white hover:bg-primary-600 focus:bg-primary-700 focus:text-white group"
                >
                    @if ($icon = $block->getIcon())
                        <x-dynamic-component :component="$icon" class="mr-2 -ml-1 text-primary-500 w-5 h-5 group-hover:text-white group-focus:text-white" />
                    @endif

                    {{ $block->getLabel() }}
                </button>
            </li>
        @endforeach
    </ul>
</div>
