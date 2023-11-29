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
            @capture($options)
                @foreach ($navigationGroup->getItems() as $navigationItem)
                    @foreach ([$navigationItem, ...$navigationItem->getChildItems()] as $navigationItemChild)
                        <option
                            @if ($navigationItem->isActive())
                                selected
                            @endif
                            value="{{ $navigationItem->getUrl() }}"
                        >
                            {{ $loop->index ? '&ensp;&ensp;' : null }}
                            {{ $navigationItem->getLabel() }}
                        </option>
                    @endforeach
                @endforeach
            @endcapture

            @if (filled($navigationGroupLabel = $navigationGroup->getLabel()))
                <optgroup label="{{ $navigationGroupLabel }}">
                    {{ $options() }}
                </optgroup>
            @else
                {{ $options() }}
            @endif
        @endforeach
    </x-filament::input.select>
</x-filament::input.wrapper>
