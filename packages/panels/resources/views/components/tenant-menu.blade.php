@php
    $currentTenant = filament()->getTenant();
    $currentTenantName = filament()->getTenantName($currentTenant);
    $items = filament()->getTenantMenuItems();

    $billingItem = $items['billing'] ?? null;
    $billingItemUrl = $billingItem?->getUrl();
    $isBillingItemVisible = $billingItem?->isVisible() ?? true;
    $hasBillingItem = (filament()->hasTenantBilling() || filled($billingItemUrl)) && $isBillingItemVisible;

    $registrationItem = $items['register'] ?? null;
    $registrationItemUrl = $registrationItem?->getUrl();
    $isRegistrationItemVisible = $registrationItem?->isVisible() ?? true;
    $hasRegistrationItem = ((filament()->hasTenantRegistration() && filament()->getTenantRegistrationPage()::canView()) || filled($registrationItemUrl)) && $isRegistrationItemVisible;

    $profileItem = $items['profile'] ?? null;
    $profileItemUrl = $profileItem?->getUrl();
    $isProfileItemVisible = $profileItem?->isVisible() ?? true;
    $hasProfileItem = ((filament()->hasTenantProfile() && filament()->getTenantProfilePage()::canView($currentTenant)) || filled($profileItemUrl)) && $isProfileItemVisible;

    $canSwitchTenants = count($tenants = array_filter(
        filament()->getUserTenants(filament()->auth()->user()),
        fn (\Illuminate\Database\Eloquent\Model $tenant): bool => ! $tenant->is($currentTenant),
    ));

    $items = \Illuminate\Support\Arr::except($items, ['billing', 'profile', 'register']);
@endphp

{{ \Filament\Support\Facades\FilamentView::renderHook('panels::tenant-menu.before') }}

<x-filament::dropdown
    placement="bottom-start"
    teleport
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-tenant-menu'])
    "
>
    <x-slot name="trigger">
        <button
            @if (filament()->isSidebarCollapsibleOnDesktop())
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
            class="fi-tenant-menu-trigger group flex w-full items-center justify-center gap-x-3 rounded-lg p-2 text-sm font-medium outline-none transition duration-75 hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5"
        >
            <x-filament-panels::avatar.tenant
                :tenant="$currentTenant"
                class="shrink-0"
            />

            <span
                @if (filament()->isSidebarCollapsibleOnDesktop())
                    x-show="$store.sidebar.isOpen"
                @endif
                class="grid justify-items-start text-start"
            >
                @if ($currentTenant instanceof \Filament\Models\Contracts\HasCurrentTenantLabel)
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $currentTenant->getCurrentTenantLabel() }}
                    </span>
                @endif

                <span class="text-gray-950 dark:text-white">
                    {{ $currentTenantName }}
                </span>
            </span>

            <x-filament::icon
                icon="heroicon-m-chevron-down"
                icon-alias="panels::tenant-menu.toggle-button"
                :x-show="filament()->isSidebarCollapsibleOnDesktop() ? '$store.sidebar.isOpen' : null"
                class="ms-auto h-5 w-5 shrink-0 text-gray-400 transition duration-75 group-hover:text-gray-500 group-focus-visible:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-400 dark:group-focus-visible:text-gray-400"
            />
        </button>
    </x-slot>

    @if ($hasProfileItem || $hasBillingItem)
        <x-filament::dropdown.list>
            @if ($hasProfileItem)
                <x-filament::dropdown.list.item
                    :color="$profileItem?->getColor()"
                    :href="$profileItemUrl ?? filament()->getTenantProfileUrl()"
                    :icon="$profileItem?->getIcon() ?? \Filament\Support\Facades\FilamentIcon::resolve('panels::tenant-menu.profile-button') ?? 'heroicon-m-cog-6-tooth'"
                    tag="a"
                    :target="($profileItem?->shouldOpenUrlInNewTab() ?? false) ? '_blank' : null"
                >
                    {{ $profileItem?->getLabel() ?? filament()->getTenantProfilePage()::getLabel() }}
                </x-filament::dropdown.list.item>
            @endif

            @if ($hasBillingItem)
                <x-filament::dropdown.list.item
                    :color="$billingItem?->getColor() ?? 'gray'"
                    :href="$billingItemUrl ?? filament()->getTenantBillingUrl()"
                    :icon="$billingItem?->getIcon() ?? \Filament\Support\Facades\FilamentIcon::resolve('panels::tenant-menu.billing-button') ?? 'heroicon-m-credit-card'"
                    tag="a"
                    :target="($billingItem?->shouldOpenUrlInNewTab() ?? false) ? '_blank' : null"
                >
                    {{ $billingItem?->getLabel() ?? __('filament-panels::layout.actions.billing.label') }}
                </x-filament::dropdown.list.item>
            @endif
        </x-filament::dropdown.list>
    @endif

    @if (count($items))
        <x-filament::dropdown.list>
            @foreach ($items as $item)
                <x-filament::dropdown.list.item
                    :color="$item->getColor()"
                    :href="$item->getUrl()"
                    :target="$item->shouldOpenUrlInNewTab() ? '_blank' : null"
                    :icon="$item->getIcon()"
                    tag="a"
                >
                    {{ $item->getLabel() }}
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    @endif

    @if ($canSwitchTenants)
        <x-filament::dropdown.list>
            @foreach ($tenants as $tenant)
                <x-filament::dropdown.list.item
                    :href="filament()->getUrl($tenant)"
                    :image="filament()->getTenantAvatarUrl($tenant)"
                    tag="a"
                >
                    {{ filament()->getTenantName($tenant) }}
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    @endif

    @if ($hasRegistrationItem)
        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item
                :color="$registrationItem?->getColor()"
                :href="$registrationItemUrl ?? filament()->getTenantRegistrationUrl()"
                :icon="$registrationItem?->getIcon() ?? \Filament\Support\Facades\FilamentIcon::resolve('panels::tenant-menu.registration-button') ?? 'heroicon-m-plus'"
                tag="a"
                :target="($registrationItem?->shouldOpenUrlInNewTab() ?? false) ? '_blank' : null"
            >
                {{ $registrationItem?->getLabel() ?? filament()->getTenantRegistrationPage()::getLabel() }}
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    @endif
</x-filament::dropdown>

{{ \Filament\Support\Facades\FilamentView::renderHook('panels::tenant-menu.after') }}
