@php
    $currentTenant = filament()->getTenant();
    $currentTenantName = filament()->getTenantName($currentTenant);
    $items = filament()->getTenantMenuItems();

    $billingItem = $items['billing'] ?? null;
    $billingItemUrl = $billingItem?->getUrl();
    $hasTenantBilling = filament()->hasTenantBilling() || $billingItemUrl;

    $registrationItem = $items['register'] ?? null;
    $registrationItemUrl = $registrationItem?->getUrl();
    $hasTenantRegistration = filament()->hasTenantRegistration() || $registrationItemUrl;

    $canSwitchTenants = filament()->hasRoutableTenancy() && count($tenants = array_filter(
        filament()->getUserTenants(filament()->auth()->user()),
        fn (\Illuminate\Database\Eloquent\Model $tenant): bool => ! $tenant->is($currentTenant),
    ));

    $items = \Illuminate\Support\Arr::except($items, ['billing', 'register']);
@endphp

{{ filament()->renderHook('tenant-menu.before') }}

<x-filament::dropdown placement="bottom-start">
    <x-slot name="trigger">
        <div
            class="flex items-center space-x-3 -m-3 p-2 rounded-lg transition rtl:space-x-reverse hover:bg-gray-500/5 dark:hover:bg-gray-900/50"
            @if (filament()->isSidebarCollapsibleOnDesktop())
                x-data="{ tooltip: {} }"
                x-init="
                    Alpine.effect(() => {
                        if (Alpine.store('sidebar').isOpen) {
                            tooltip = false
                        } else {
                            tooltip = {
                                content: @js($currentTenantName),
                                theme: Alpine.store('theme') === 'light' ? 'dark' : 'light',
                                placement: document.dir === 'rtl' ? 'left' : 'right',
                            }
                        }
                    })
                "
            x-tooltip.html="tooltip"
            x-bind:class="{
                    'justify-center': ! $store.sidebar.isOpen,
                }"
            @endif
        >
            <x-filament::avatar.tenant
                :tenant="$currentTenant"
                class="shrink-0"
            />

            <div
                @if (filament()->isSidebarCollapsibleOnDesktop())
                    x-data="{}"
                    x-show="$store.sidebar.isOpen"
                @endif
            >
                @if ($currentTenant instanceof \Filament\Models\Contracts\HasCurrentTenantLabel)
                    <p class="text-[.625rem] font-bold uppercase tracking-wider text-gray-500 -mb-0.5 dark:text-gray-400">
                        {{ $currentTenant->getCurrentTenantLabel() }}
                    </p>
                @endif

                <p class="font-medium tracking-tight">
                    {{ $currentTenantName }}
                </p>
            </div>
        </div>
    </x-slot>

    @if (count($items) || $hasTenantBilling || $canSwitchTenants || $hasTenantRegistration)
        <x-filament::dropdown.list>
            @foreach ($items as $key => $item)
                <x-filament::dropdown.list.item
                    :color="$item->getColor() ?? 'gray'"
                    :icon="$item->getIcon()"
                    :href="$item->getUrl()"
                    tag="a"
                >
                    {{ $item->getLabel() }}
                </x-filament::dropdown.list.item>
            @endforeach

            @if ($hasTenantBilling)
                <x-filament::dropdown.list.item
                    :href="$billingItemUrl ?? filament()->getTenantBillingUrl($currentTenant)"
                    :icon="$billingItem?->getIcon() ?? 'heroicon-m-credit-card'"
                    :color="$billingItem?->getColor() ?? 'gray'"
                    tag="a"
                >
                    {{ $billingItem?->getLabel() ?? __('filament::layout.buttons.billing.label') }}
                </x-filament::dropdown.list.item>
            @endif

            @if ((count($items) || $hasTenantBilling) && $canSwitchTenants)
                <x-filament::dropdown.list.separator />
            @endif

            @if ($canSwitchTenants)
                @foreach ($tenants as $tenant)
                    <x-filament::dropdown.list.item
                        :href="filament()->getUrl($tenant)"
                        :image="filament()->getTenantAvatarUrl($tenant)"
                        tag="a"
                    >
                        {{ filament()->getTenantName($tenant) }}
                    </x-filament::dropdown.list.item>
                @endforeach
            @endif

            @if ($canSwitchTenants && $hasTenantRegistration)
                <x-filament::dropdown.list.separator />
            @endif

            @if ($hasTenantRegistration)
                <x-filament::dropdown.list.item
                    :href="$registrationItemUrl ?? filament()->getTenantRegistrationUrl()"
                    :icon="$registrationItem?->getIcon() ?? 'heroicon-m-plus'"
                    :color="$registrationItem?->getColor() ?? 'primary'"
                    tag="a"
                >
                    {{ $registrationItem?->getLabel() ?? filament()->getTenantRegistrationPage()::getLabel() }}
                </x-filament::dropdown.list.item>
            @endif
        </x-filament::dropdown.list>
    @endif
</x-filament::dropdown>

{{ filament()->renderHook('tenant-menu.after') }}
