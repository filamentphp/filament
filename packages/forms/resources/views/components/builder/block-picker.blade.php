@props([
    'action',
    'afterItem' => null,
    'blocks',
    'statePath',
    'trigger',
])

<x-filament::dropdown {{ $attributes->class(['fi-fo-builder-block-picker']) }}>
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
                :icon="$block->getIcon()"
                x-on:click="close"
                :wire:click="$wireClickAction"
            >
                {{ $block->getLabel() }}
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
