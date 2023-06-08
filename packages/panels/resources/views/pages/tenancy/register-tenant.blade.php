<div>
    <form wire:submit.prevent="register" class="grid gap-y-8">
        {{ $this->form }}

        {{ $this->registerAction }}
    </form>

    @if (count($tenants = filament()->getUserTenants(filament()->auth()->user())))
        <x-slot name="after">
            <ul
                class="mt-8 divide-y overflow-hidden rounded-xl bg-white/50 shadow-sm ring-1 ring-gray-950/5 backdrop-blur-xl dark:divide-gray-700 dark:bg-gray-900/50 dark:ring-white/20"
            >
                @foreach ($tenants as $tenant)
                    <li>
                        <a
                            href="{{ filament()->getUrl($tenant) }}"
                            class="flex items-center gap-4 px-4 py-3 transition hover:bg-gray-500/5 dark:hover:bg-gray-900/50"
                        >
                            <x-filament::avatar.tenant :tenant="$tenant" />

                            <div class="flex-1">
                                {{ filament()->getTenantName($tenant) }}
                            </div>

                            <x-filament::icon-button
                                icon="heroicon-m-chevron-right"
                                icon-alias="panels::pages.tenancy.register-tenant.tenant"
                            />
                        </a>
                    </li>
                @endforeach
            </ul>
        </x-slot>
    @endif
</div>
