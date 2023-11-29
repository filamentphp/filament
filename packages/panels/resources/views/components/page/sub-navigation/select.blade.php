@props([
    'navigation',
])

<x-filament::input.wrapper
    wire:ignore
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['md:hidden'])
    "
>
    <x-filament::input.select
        x-data="{}"
        x-on:change="window.location = $event.target.value"
    >
        @foreach ($navigation as $navigationGroup)
            @php
                $navigationGroupLabel = $navigationGroup->getLabel();
            @endphp

            @if (filled($navigationGroupLabel))
                <optgroup
                    label="{{ $navigationGroupLabel }}"
                >
            @endif

            @foreach ($navigationGroup->getItems() as $navigationItem)
                @foreach ([$navigationItem, ...$navigationItem->getChildItems()] as $navigationItemChild)
                    <option
                        @if ($navigationItem->isActive())
                            selected
                        @endif
                        value="{{ $navigationItem->getUrl() }}"
                    >
                        @if ($loop->index)&ensp;&ensp;@endif{{ $navigationItem->getLabel() }}
                    </option>
                @endforeach
            @endforeach

            @if (filled($navigationGroupLabel))
                </optgroup>
            @endif
        @endforeach
    </x-filament::input.select>
</x-filament::input.wrapper>
