@props([
    'navigation',
])

<x-filament::input.wrapper
    wire:ignore
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-page-sub-navigation-select'])
    "
>
    <select
        x-on:change="
            if ($event.target.options[$event.target.selectedIndex].dataset.spaMode) {
                Livewire.navigate($event.target.value)
            } else {
                window.location.href = $event.target.value
            }
        "
        class="fi-select-input"
    >
        @foreach ($navigation as $navigationGroup)
            @capture($options)
                @foreach ($navigationGroup->getItems() as $navigationItem)
                    @foreach ([$navigationItem, ...$navigationItem->getChildItems()] as $navigationItemChild)
                        <option
                            @selected($navigationItemChild->isActive())
                            value="{{ $url = $navigationItemChild->getUrl() }}"
                            @if (\Filament\Support\Facades\FilamentView::hasSpaMode($url))
                                data-spa-mode="1"
                            @endif
                        >
                            @if ($loop->index)
                                &ensp;&ensp;
                            @endif

                            {{ $navigationItemChild->getLabel() }}
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
    </select>
</x-filament::input.wrapper>
