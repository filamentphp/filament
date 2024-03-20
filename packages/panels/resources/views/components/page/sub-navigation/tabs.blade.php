@props([
    'navigation',
])

<x-filament::tabs
    wire:ignore
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-page-sub-navigation-tabs hidden md:flex'])
    "
>
    @foreach ($navigation as $navigationGroup)
        @if ($navigationGroupLabel = $navigationGroup->getLabel())
            <x-filament::dropdown placement="bottom-start">
                <x-slot name="trigger">
                    <x-filament::tabs.item
                        :active="$navigationGroup->isActive()"
                        :icon="$navigationGroup->getIcon()"
                    >
                        {{ $navigationGroupLabel }}
                    </x-filament::tabs.item>
                </x-slot>

                <x-filament::dropdown.list>
                    @foreach ($navigationGroup->getItems() as $navigationItem)
                        @php
                            $navigationItemIcon = $navigationItem->getIcon();
                            $navigationItemIcon = $navigationItem->isActive() ? ($navigationItem->getActiveIcon() ?? $navigationItemIcon) : $navigationItemIcon;
                        @endphp

                        <x-filament::dropdown.list.item
                            :badge="$navigationItem->getBadge()"
                            :badge-color="$navigationItem->getBadgeColor()"
                            :href="$navigationItem->getUrl()"
                            :icon="$navigationItemIcon"
                            tag="a"
                            :target="$navigationItem->shouldOpenUrlInNewTab() ? '_blank' : null"
                        >
                            {{ $navigationItem->getLabel() }}

                            @if ($navigationItemIcon instanceof \Illuminate\Contracts\Support\Htmlable)
                                <x-slot name="icon">
                                    {{ $navigationItemIcon }}
                                </x-slot>
                            @endif
                        </x-filament::dropdown.list.item>
                    @endforeach
                </x-filament::dropdown.list>
            </x-filament::dropdown>
        @else
            @foreach ($navigationGroup->getItems() as $navigationItem)
                @php
                    $navigationItemIcon = $navigationItem->getIcon();
                    $navigationItemIcon = $navigationItem->isActive() ? ($navigationItem->getActiveIcon() ?? $navigationItemIcon) : $navigationItemIcon;
                @endphp

                <x-filament::tabs.item
                    :active="$navigationItem->isActive()"
                    :badge="$navigationItem->getBadge()"
                    :badge-color="$navigationItem->getBadgeColor()"
                    :href="$navigationItem->getUrl()"
                    :icon="$navigationItemIcon"
                    tag="a"
                    :target="$navigationItem->shouldOpenUrlInNewTab() ? '_blank' : null"
                >
                    {{ $navigationItem->getLabel() }}

                    @if ($navigationItemIcon instanceof \Illuminate\Contracts\Support\Htmlable)
                        <x-slot name="icon">
                            {{ $navigationItemIcon }}
                        </x-slot>
                    @endif
                </x-filament::tabs.item>
            @endforeach
        @endif
    @endforeach
</x-filament::tabs>
