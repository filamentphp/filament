@php
    $user = filament()->auth()->user();
    $items = filament()->getUserMenuItems();

    $profileItem = $items['profile'] ?? $items['account'] ?? null;
    $profileItemUrl = $profileItem?->getUrl();
    $profilePage = filament()->getProfilePage();
    $hasProfile = filament()->hasProfile() || filled($profileItemUrl);

    $logoutItem = $items['logout'] ?? null;

    $items = \Illuminate\Support\Arr::except($items, ['account', 'logout', 'profile']);
@endphp

{{ \Filament\Support\Facades\FilamentView::renderHook('user-menu.start') }}

<x-filament::dropdown placement="bottom-end" class="fi-user-menu">
    <x-slot name="trigger">
        <button
            aria-label="{{ __('filament::layout.actions.open_user_menu.label') }}"
            type="button"
            class="flex"
        >
            <x-filament::avatar.user :user="$user" />
        </button>
    </x-slot>

    {{ \Filament\Support\Facades\FilamentView::renderHook('user-menu.profile.before') }}

    @if ($hasProfile)
        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item
                :color="$profileItem?->getColor()"
                :icon="$profileItem?->getIcon() ?? 'heroicon-m-user-circle'"
                :href="$profileItemUrl ?? filament()->getProfileUrl()"
                icon-alias="panels::user-menu.profile-item"
                tag="a"
            >
                {{ $profileItem?->getLabel() ?? ($profilePage ? $profilePage::getLabel() : null) ?? filament()->getUserName($user) }}
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    @else
        <x-filament::dropdown.header
            :color="$profileItem?->getColor()"
            :icon="$profileItem?->getIcon() ?? 'heroicon-m-user-circle'"
            icon-alias="panels::user-menu.profile-item"
        >
            {{ $profileItem?->getLabel() ?? filament()->getUserName($user) }}
        </x-filament::dropdown.header>
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook('user-menu.profile.after') }}

    @if (filament()->hasDarkMode() && (! filament()->hasDarkModeForced()))
        <x-filament::dropdown.list>
            <x-filament::theme-switcher />
        </x-filament::dropdown.list>
    @endif

    <x-filament::dropdown.list>
        @foreach ($items as $key => $item)
            <x-filament::dropdown.list.item
                :color="$item->getColor()"
                :href="$item->getUrl()"
                :icon="$item->getIcon()"
                tag="a"
            >
                {{ $item->getLabel() }}
            </x-filament::dropdown.list.item>
        @endforeach

        <x-filament::dropdown.list.item
            :action="$logoutItem?->getUrl() ?? filament()->getLogoutUrl()"
            :color="$logoutItem?->getColor()"
            :icon="$logoutItem?->getIcon() ?? 'heroicon-m-arrow-left-on-rectangle'"
            icon-alias="panels::user-menu.logout-button"
            method="post"
            tag="form"
        >
            {{ $logoutItem?->getLabel() ?? __('filament::layout.actions.logout.label') }}
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>

{{ \Filament\Support\Facades\FilamentView::renderHook('user-menu.end') }}
