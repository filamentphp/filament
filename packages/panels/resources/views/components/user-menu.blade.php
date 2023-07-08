@php
    $user = filament()->auth()->user();
    $items = filament()->getUserMenuItems();

    $accountItem = $items['account'] ?? null;
    $accountItemUrl = $accountItem?->getUrl();

    $logoutItem = $items['logout'] ?? null;

    $items = \Illuminate\Support\Arr::except($items, ['account', 'logout']);
@endphp

{{ filament()->renderHook('user-menu.start') }}

<x-filament::dropdown placement="bottom-end" class="filament-user-menu">
    <x-slot name="trigger" class="ms-4">
        <button
            class="block"
            aria-label="{{ __('filament::layout.buttons.user_menu.label') }}"
        >
            <x-filament::avatar.user :user="$user" />
        </button>
    </x-slot>

    {{ filament()->renderHook('user-menu.account.before') }}

    @if (filled($accountItemUrl))
        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item
                :color="$accountItem->getColor() ?? 'gray'"
                :icon="$accountItem->getIcon() ?? 'heroicon-m-user-circle'"
                :href="$accountItemUrl"
                tag="a"
            >
                {{ $accountItem->getLabel() ?? filament()->getUserName($user) }}
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    @else
        <x-filament::dropdown.header
            :color="$accountItem?->getColor() ?? 'gray'"
            :icon="$accountItem?->getIcon() ?? 'heroicon-m-user-circle'"
        >
            {{ $accountItem?->getLabel() ?? filament()->getUserName($user) }}
        </x-filament::dropdown.header>
    @endif

    {{ filament()->renderHook('user-menu.account.after') }}

    @if (filament()->hasDarkMode() && (! filament()->hasDarkModeForced()))
        <x-filament::theme-mode-switcher />
    @endif

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

        <x-filament::dropdown.list.item
            :color="$logoutItem?->getColor() ?? 'gray'"
            :icon="$logoutItem?->getIcon() ?? 'heroicon-m-arrow-left-on-rectangle'"
            :action="$logoutItem?->getUrl() ?? filament()->getLogoutUrl()"
            method="post"
            tag="form"
        >
            {{ $logoutItem?->getLabel() ?? __('filament::layout.buttons.logout.label') }}
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>

{{ filament()->renderHook('user-menu.end') }}
