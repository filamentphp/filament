@php
    $items = filament()->getGuestMenuItems();

    $hasLogin = filament()->hasLogin();
    $hasRegistration = filament()->hasRegistration();

    $loginItem = $items['login'] ?? null;
    $registerItem = $items['register'] ?? null;

    $items = \Illuminate\Support\Arr::except($items, ['login', 'register']);
@endphp

{{ \Filament\Support\Facades\FilamentView::renderHook('panels::guest-menu.before') }}

<x-filament::dropdown
    placement="bottom-end"
    teleport
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-guest-menu'])
    "
>
    <x-slot name="trigger">
        <button
            aria-label="{{ __('filament-panels::layout.actions.open_guest_menu.label') }}"
            type="button"
        >
            <x-filament-panels::avatar.guest />
        </button>
    </x-slot>

    @if (filament()->hasDarkMode() && (! filament()->hasDarkModeForced()))
        <x-filament::dropdown.list>
            <x-filament-panels::theme-switcher />
        </x-filament::dropdown.list>
    @endif

    @if ($hasLogin || $hasRegister || count($items) > 0)
        <x-filament::dropdown.list>
            @foreach ($items as $key => $item)
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

            @if ($hasLogin)
                <x-filament::dropdown.list.item
                    :action="$loginItem?->getUrl() ?? filament()->getLoginUrl()"
                    :color="$loginItem?->getColor()"
                    :icon="$loginItem?->getIcon() ?? 'heroicon-m-arrow-right-on-rectangle'"
                    icon-alias="panels::guest-menu.login-button"
                    method="get"
                    tag="form"
                >
                    {{ $loginItem?->getLabel() ?? __('filament-panels::layout.actions.login.label') }}
                </x-filament::dropdown.list.item>
            @endif

            @if ($hasRegistration)
                <x-filament::dropdown.list.item
                    :action="$registerItem?->getUrl() ?? filament()->getRegistrationUrl()"
                    :color="$registerItem?->getColor()"
                    :icon="$registerItem?->getIcon() ?? 'heroicon-m-user-plus'"
                    icon-alias="panels::guest-menu.register-button"
                    method="get"
                    tag="form"
                >
                    {{ $loginItem?->getLabel() ?? __('filament-panels::layout.actions.register.label') }}
                </x-filament::dropdown.list.item>
            @endif
        </x-filament::dropdown.list>
    @endif
</x-filament::dropdown>

{{ \Filament\Support\Facades\FilamentView::renderHook('panels::guest-menu.after') }}