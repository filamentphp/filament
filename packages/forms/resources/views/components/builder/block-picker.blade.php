@props([
    'action',
    'afterItem' => null,
    'blocks',
    'columns' => null,
    'statePath',
    'trigger',
    'width' => null,
])

<x-filament::dropdown
    :width="$width"
    {{ $attributes->class(['fi-fo-builder-block-picker']) }}
>
    <x-slot name="trigger">
        {{ $trigger }}
    </x-slot>

    <x-filament::dropdown.list>
        <x-filament::grid
            :default="$columns['default'] ?? 1"
            :sm="$columns['sm'] ?? null"
            :md="$columns['md'] ?? null"
            :lg="$columns['lg'] ?? null"
            :xl="$columns['xl'] ?? null"
            :two-xl="$columns['2xl'] ?? null"
            direction="column"
        >
            @foreach ($blocks as $block)
                @php
                    $wireClickActionArguments = ['block' => $block->getName()];

                    if (filled($afterItem)) {
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
        </x-filament::grid>
    </x-filament::dropdown.list>
</x-filament::dropdown>
