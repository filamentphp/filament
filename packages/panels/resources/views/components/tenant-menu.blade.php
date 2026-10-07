@props([
    'teleport' => false,
])

@php
    use Filament\Actions\Action;
    use Filament\Models\Contracts\HasCurrentTenantLabel;
    use Filament\Support\Facades\FilamentView;
    use Filament\Support\Icons\Heroicon;
    use Filament\Support\View\ComponentAttributeBag;
    use Filament\View\PanelsIconAlias;
    use Filament\View\PanelsRenderHook;
    use Illuminate\Support\Arr;

    $currentTenant = filament()->getTenant();
    $currentTenantName = filament()->getTenantName($currentTenant);

    $items = $this->getTenantMenuItems();

    $tenants = $this->getSwitchableTenants();
    $canSwitchTenants = filled($tenants);

    $isSearchable = $canSwitchTenants && (filament()->isTenantMenuSearchable() ?? (count($tenants) >= 10));

    $itemsBeforeAndAfterTenantSwitcher = collect($items)
        ->groupBy(fn (Action $item): bool => $canSwitchTenants && ($item->getSort() < 0), preserveKeys: true)
        ->all();
    $itemsBeforeTenantSwitcher = $itemsBeforeAndAfterTenantSwitcher[true] ?? collect();
    $itemsAfterTenantSwitcher = $itemsBeforeAndAfterTenantSwitcher[false] ?? collect();

    $multiGroupAfterSwitcher = $this->hasMultipleTenantMenuItemGroups();
    $afterSwitcherItemGroups = $multiGroupAfterSwitcher ? $this->getTenantMenuItemGroupsAfterSwitcher() : [];

    $isSidebarCollapsibleOnDesktop = filament()->isSidebarCollapsibleOnDesktop();

    if ($isSearchable) {
        foreach ($items as $item) {
            $item->extraAttributes([
                'role' => ($item->getUrl() && (! $item->shouldPostToUrl())) ? 'link' : 'button',
                'tabindex' => '0',
            ], merge: true);
        }
    }
@endphp

{{ FilamentView::renderHook(PanelsRenderHook::TENANT_MENU_BEFORE) }}

<x-filament::dropdown
    :menu="! $isSearchable"
    placement="bottom-start"
    size
    :teleport="$teleport"
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-tenant-menu'])
    "
>
    <x-slot name="trigger">
        <button
            @if ($isSidebarCollapsibleOnDesktop)
                x-data="{ tooltip: false }"
                x-effect="
                    tooltip = $store.sidebar.isOpen
                        ? false
                        : {
                              content: @js($currentTenantName),
                              placement: document.dir === 'rtl' ? 'left' : 'right',
                              theme: $store.theme,
                          }
                "
                x-tooltip.html="tooltip"
            @endif
            type="button"
            class="fi-tenant-menu-trigger"
        >
            <x-filament-panels::avatar.tenant
                :tenant="$currentTenant"
                loading="lazy"
            />

            <span
                @if ($isSidebarCollapsibleOnDesktop)
                    x-show="$store.sidebar.isOpen"
                @endif
                class="fi-tenant-menu-trigger-text"
            >
                @if ($currentTenant instanceof HasCurrentTenantLabel)
                    <span class="fi-tenant-menu-trigger-current-tenant-label">
                        {{ $currentTenant->getCurrentTenantLabel() }}
                    </span>
                @endif

                <span class="fi-tenant-menu-trigger-tenant-name">
                    {{ $currentTenantName }}
                </span>
            </span>

            {{
                \Filament\Support\generate_icon_html(Heroicon::ChevronDown, alias: PanelsIconAlias::TENANT_MENU_TOGGLE_BUTTON, attributes: new ComponentAttributeBag([
                    'x-show' => $isSidebarCollapsibleOnDesktop ? '$store.sidebar.isOpen' : null,
                ]))
            }}
        </button>
    </x-slot>

    @if ($itemsBeforeTenantSwitcher->isNotEmpty())
        <x-filament::dropdown.list>
            @foreach ($itemsBeforeTenantSwitcher as $item)
                {{ $item }}
            @endforeach
        </x-filament::dropdown.list>
    @endif

    @if ($canSwitchTenants)
        <div x-data="{ search: '' }">
            <x-filament::dropdown.list>
                @if ($isSearchable)
                    <div x-id="['input']">
                        <label x-bind:for="$id('input')" class="fi-sr-only">
                            {{ __('filament-panels::layout.tenant_menu.search_field.label') }}
                        </label>

                        <x-filament::input
                            x-bind:id="$id('input')"
                            x-model="search"
                            :placeholder="__('filament-panels::layout.tenant_menu.search_field.placeholder')"
                            type="search"
                        />
                    </div>
                @endif

                @foreach ($tenants as $tenant)
                    @php
                        $tenantImage = filament()->getTenantAvatarUrl($tenant);
                        $tenantName = filament()->getTenantName($tenant);
                        $tenantUrl = filament()->getUrl($tenant);
                    @endphp

                    <div
                        class="fi-tenant-menu-item"
                        x-show="
                            search === '' ||
                                @js($tenantName).replace(/ /g, '')
                                    .toLowerCase()
                                    .includes(search.replace(/ /g, '').toLowerCase())
                        "
                    >
                        @if ($isSearchable)
                            <a
                                {{ \Filament\Support\generate_href_html($tenantUrl) }}
                                class="fi-dropdown-list-item"
                            >
                                @if ($tenantImage)
                                    <div
                                        class="fi-dropdown-list-item-image"
                                        style="
                                            background-image: url('{{ $tenantImage }}');
                                        "
                                    ></div>
                                @endif

                                <span class="fi-dropdown-list-item-label">
                                    {{ $tenantName }}
                                </span>
                            </a>
                        @else
                            <x-filament::dropdown.list.item
                                :href="$tenantUrl"
                                :image="$tenantImage"
                                :image-circular="false"
                                tag="a"
                            >
                                {{ $tenantName }}
                            </x-filament::dropdown.list.item>
                        @endif
                    </div>
                @endforeach
            </x-filament::dropdown.list>
        </div>
    @endif

    @if ($multiGroupAfterSwitcher && $afterSwitcherItemGroups !== [])
        @foreach ($afterSwitcherItemGroups as $afterSwitcherGroup)
            <x-filament::dropdown.list>
                @foreach ($afterSwitcherGroup as $item)
                    {{ $item }}
                @endforeach
            </x-filament::dropdown.list>
        @endforeach
    @elseif ($itemsAfterTenantSwitcher->isNotEmpty())
        <x-filament::dropdown.list>
            @foreach ($itemsAfterTenantSwitcher as $item)
                {{ $item }}
            @endforeach
        </x-filament::dropdown.list>
    @endif
</x-filament::dropdown>

{{ FilamentView::renderHook(PanelsRenderHook::TENANT_MENU_AFTER) }}
