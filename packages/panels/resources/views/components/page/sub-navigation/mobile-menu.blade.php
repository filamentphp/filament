@props([
    'navigation',
])

<x-filament::dropdown
    placement="bottom-start"
    width="xs"
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-page-sub-navigation-dropdown'])
    "
>
    <x-slot name="trigger">
        @php
            $activeItem = null;

            foreach ($navigation as $navigationGroup) {
                foreach ($navigationGroup->getItems() as $navigationItem) {
                    foreach ([$navigationItem, ...$navigationItem->getChildItems()] as $navigationItemChild) {
                        if ($navigationItemChild->isActive()) {
                            $activeItem = $navigationItemChild;

                            break 3;
                        }
                    }
                }
            }
        @endphp

        <x-filament::button
            color="gray"
            :icon="\Filament\Support\Icons\Heroicon::ChevronDown"
            :icon-alias="\Filament\View\PanelsIconAlias::SUB_NAVIGATION_MOBILE_MENU_BUTTON"
            icon-position="after"
        >
            {{ $activeItem?->getLabel() }}
        </x-filament::button>
    </x-slot>

    @foreach ($navigation as $navigationGroup)
        @if (filled($navigationGroupLabel = $navigationGroup->getLabel()))
            <x-filament::dropdown.header>
                {{ $navigationGroupLabel }}
            </x-filament::dropdown.header>
        @endif

        <x-filament::dropdown.list>
            @foreach ($navigationGroup->getItems() as $navigationItem)
                @foreach ([$navigationItem, ...$navigationItem->getChildItems()] as $navigationItemChild)
                    @php
                        $navigationItemBadge = $navigationItem->getBadge();
                        $navigationItemBadgeColor = $navigationItem->getBadgeColor();
                        $navigationItemIcon = $navigationItem->isActive() ? ($navigationItem->getActiveIcon() ?? $navigationItem->getIcon()) : $navigationItem->getIcon();
                        $navigationItemUrl = $navigationItem->getUrl();
                        $shouldNavigationItemOpenUrlInNewTab = $navigationItem->shouldOpenUrlInNewTab();
                    @endphp

                    <x-filament::dropdown.list.item
                        :badge="$navigationItemBadge"
                        :badge-color="$navigationItemBadgeColor"
                        :href="$navigationItemUrl"
                        :icon="$navigationItemIcon"
                        tag="a"
                        :target="$shouldNavigationItemOpenUrlInNewTab ? '_blank' : null"
                    >
                        {{ $navigationItemChild->getLabel() }}
                    </x-filament::dropdown.list.item>
                @endforeach
            @endforeach
        </x-filament::dropdown.list>
    @endforeach
</x-filament::dropdown>
