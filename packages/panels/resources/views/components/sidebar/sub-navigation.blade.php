@props([
    'subNavigation',
])

<div wire:ignore class="col-span-1">
    <x-filament::input.wrapper class="md:hidden">
        <x-filament::input.select
            x-data="{}"
            x-on:change="window.location = $event.target.value"
        >
            @foreach ($subNavigation as $subNavigationGroup)
                @php
                    $subNavigationGroupLabel = $subNavigationGroup->getLabel();
                @endphp

                @if (filled($subNavigationGroupLabel))
                    <optgroup
                        label="{{ $subNavigationGroupLabel }}"
                    >
                        @foreach ($subNavigationGroup->getItems() as $subNavigationItem)
                            <option
                                @if ($subNavigationItem->isActive())
                                    selected
                                @endif
                                value="{{ $subNavigationItem->getUrl() }}"
                            >
                                {{ $subNavigationItem->getLabel() }}
                            </option>
                        @endforeach
                    </optgroup>
                @else
                    @foreach ($subNavigationGroup->getItems() as $subNavigationItem)
                        <option
                            @if ($subNavigationItem->isActive())
                                selected
                            @endif
                            value="{{ $subNavigationItem->getUrl() }}"
                        >
                            {{ $subNavigationItem->getLabel() }}
                        </option>
                    @endforeach
                @endif
            @endforeach
        </x-filament::input.select>
    </x-filament::input.wrapper>

    <div class="hidden md:block">
        @foreach ($subNavigation as $subNavigationGroup)
            <x-filament-panels::sidebar.group
                :collapsible="$subNavigationGroup->isCollapsible()"
                :icon="$subNavigationGroup->getIcon()"
                :items="$subNavigationGroup->getItems()"
                :label="$subNavigationGroup->getLabel()"
                :sidebar-collapsible="false"
            />
        @endforeach
    </div>
</div>
