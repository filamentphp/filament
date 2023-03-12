@props([
    'action',
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
            @php
                $wireClickActionArguments = ['block' => $block->getName()];
                if ($afterItem) {
                    $wireClickActionArguments['afterItem'] = $afterItem;
                }
                $wireClickActionArguments = \Illuminate\Support\Js::from($wireClickActionArguments);

                $wireClickAction = "mountFormComponentAction('{$statePath}', '{$action->getName()}', {$wireClickActionArguments})";
            @endphp

            <x-filament::dropdown.list.item
                :wire:click="$wireClickAction"
                :icon="$block->getIcon()"
                x-on:click="close"
            >
                {{ $block->getLabel() }}
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
