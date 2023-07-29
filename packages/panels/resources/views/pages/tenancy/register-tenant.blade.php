<x-filament-panels::page.simple>
    <x-filament-panels::form wire:submit="register">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    @if (count($tenants = filament()->getUserTenants(filament()->auth()->user())))
        <ul
            class="mt-8 divide-y overflow-hidden rounded-xl bg-white/50 shadow-sm ring-1 ring-gray-950/5 backdrop-blur-xl dark:divide-gray-700 dark:bg-gray-900/50 dark:ring-white/10"
        >
            @foreach ($tenants as $tenant)
                <li>
                    <a
                        href="{{ filament()->getUrl($tenant) }}"
                        class="flex items-center gap-4 px-4 py-3 transition hover:bg-gray-500/5 dark:hover:bg-gray-900/50"
                    >
                        <x-filament-panels::avatar.tenant :tenant="$tenant" />

                        <div class="flex-1">
                            {{ filament()->getTenantName($tenant) }}
                        </div>

                        <x-filament::icon-button
                            icon="heroicon-m-chevron-right"
                            icon-alias="panels::pages.tenancy.register-tenant.open-tenant-button"
                        />
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</x-filament-panels::page.simple>
