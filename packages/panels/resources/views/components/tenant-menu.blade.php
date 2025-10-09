@props([
    'teleport' => false,
])

@php
    use Filament\Actions\Action;
    use Illuminate\Support\Arr;

    $currentTenant = filament()->getTenant();
    $currentTenantName = filament()->getTenantName($currentTenant);

    $items = $this->getTenantMenuItems();

    $canSwitchTenants = count($tenants = array_filter(
        filament()->getUserTenants(filament()->auth()->user()),
        fn (\Illuminate\Database\Eloquent\Model $tenant): bool => ! $tenant->is($currentTenant),
    ));

    $isSearchable = filled($canSwitchTenants) ? (filament()->isTenantMenuSearchable() ?? (count($tenants) >= 10)) : false;

    $itemsBeforeAndAfterTenantSwitcher = collect($items)
        ->groupBy(fn (Action $item): bool => $canSwitchTenants && ($item->getSort() < 0), preserveKeys: true)
        ->all();
    $itemsBeforeTenantSwitcher = $itemsBeforeAndAfterTenantSwitcher[true] ?? collect();
    $itemsAfterTenantSwitcher = $itemsBeforeAndAfterTenantSwitcher[false] ?? collect();

    $isSidebarCollapsibleOnDesktop = filament()->isSidebarCollapsibleOnDesktop();
@endphp

{{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TENANT_MENU_BEFORE) }}

<x-filament::dropdown
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
                @if ($currentTenant instanceof \Filament\Models\Contracts\HasCurrentTenantLabel)
                    <span class="fi-tenant-menu-trigger-current-tenant-label">
                        {{ $currentTenant->getCurrentTenantLabel() }}
                    </span>
                @endif

                <span class="fi-tenant-menu-trigger-tenant-name">
                    {{ $currentTenantName }}
                </span>
            </span>

            {{
                \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::ChevronDown, alias: \Filament\View\PanelsIconAlias::TENANT_MENU_TOGGLE_BUTTON, attributes: new \Illuminate\View\ComponentAttributeBag([
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
                            placeholder="{{ __('filament-panels::layout.tenant_menu.search_field.placeholder') }}"
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
                        x-show="
                            search === '' ||
                                @js($tenantName).replace(/ /g, '')
                                    .toLowerCase()
                                    .includes(search.replace(/ /g, '').toLowerCase())
                        "
                    >
                        <x-filament::dropdown.list.item
                            :href="$tenantUrl"
                            :image="$tenantImage"
                            tag="a"
                        >
                            {{ $tenantName }}
                        </x-filament::dropdown.list.item>
                    </div>
                @endforeach
            </x-filament::dropdown.list>
        </div>
    @endif

    @if ($itemsAfterTenantSwitcher->isNotEmpty())
        <x-filament::dropdown.list>
            @foreach ($itemsAfterTenantSwitcher as $item)
                {{ $item }}
            @endforeach
        </x-filament::dropdown.list>
    @endif
</x-filament::dropdown>

{{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TENANT_MENU_AFTER) }}
