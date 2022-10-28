{{ filament()->renderHook('tenant-switcher.before') }}

<x-filament::dropdown placement="bottom-start">
    <x-slot name="trigger">
        @php
            $currentTenant = filament()->getTenant();
            $currentTenantName = filament()->getTenantName($currentTenant);
        @endphp

        <div
            class="flex items-center space-x-3 -m-4 p-3 rounded-lg transition rtl:space-x-reverse hover:bg-gray-100 focus:bg-gray-100"
            @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
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
            <x-filament::tenant-avatar
                :tenant="$currentTenant"
                size="sm"
                class="shrink-0"
            />

            <div
                @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
                    x-data="{}"
                    x-show="$store.sidebar.isOpen"
                @endif
                class="font-medium tracking-tight"
            >
                {{ $currentTenantName }}
            </div>
        </div>
    </x-slot>

    <x-filament::dropdown.list>
        @php
            $tenants = array_filter(
                filament()->getUserTenants(filament()->auth()->user()),
                fn (\Illuminate\Database\Eloquent\Model $tenant): bool => ! $tenant->is($currentTenant),
            );
        @endphp

        @foreach ($tenants as $tenant)
            <x-filament::dropdown.list.item
                :href="filament()->getUrl($tenant)"
                :image="filament()->getTenantAvatarUrl($tenant)"
                tag="a"
            >
                {{ filament()->getTenantName($tenant) }}
            </x-filament::dropdown.list.item>
        @endforeach

        @if (count($tenants))
            <x-filament::dropdown.list.separator />
        @endif

        <x-filament::dropdown.list.item icon="heroicon-m-plus">
            New team
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>

{{ filament()->renderHook('tenant-switcher.after') }}
