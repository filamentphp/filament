@php
    use Filament\Support\Enums\GridDirection;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'action',
    'afterItem' => null,
    'blocks',
    'columns' => null,
    'key',
    'trigger',
    'width' => null,
])

<x-filament::dropdown
    :width="$width"
    :attributes="
        \Filament\Support\prepare_inherited_attributes(
            $attributes->class(['fi-fo-builder-block-picker']),
        )
    "
>
    <x-slot name="trigger">
        {{ $trigger }}
    </x-slot>

    <x-filament::dropdown.list>
        <div
            {{ (new ComponentAttributeBag)->grid($columns, GridDirection::Column) }}
        >
            @foreach ($blocks as $block)
                @php
                    $blockIcon = $block->getIcon();

                    $wireClickActionArguments = ['block' => $block->getName()];

                    if (filled($afterItem)) {
                        $wireClickActionArguments['afterItem'] = $afterItem;
                    }

                    $wireClickActionArguments = \Illuminate\Support\Js::from($wireClickActionArguments);

                    $wireClickAction = "mountAction('{$action->getName()}', {$wireClickActionArguments}, { schemaComponent: '{$key}' })";
                @endphp

                <x-filament::dropdown.list.item
                    :icon="$blockIcon"
                    x-on:click="close"
                    :wire:click="$wireClickAction"
                >
                    {{ $block->getLabel() }}
                </x-filament::dropdown.list.item>
            @endforeach
        </div>
    </x-filament::dropdown.list>
</x-filament::dropdown>
