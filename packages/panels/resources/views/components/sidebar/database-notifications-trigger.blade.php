@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Icons\Heroicon;
    use Filament\View\PanelsIconAlias;
    use Illuminate\Support\Number;

    $isSidebarCollapsibleOnDesktop = filament()->isSidebarCollapsibleOnDesktop();

    $databaseNotificationsLabel = $unreadNotificationsCount
        ? trans_choice('filament-panels::layout.actions.open_database_notifications.label_with_unread_count', $unreadNotificationsCount, ['count' => Number::format($unreadNotificationsCount, locale: app()->getLocale())])
        : __('filament-panels::layout.actions.open_database_notifications.label');
@endphp

<button
    @if ($isSidebarCollapsibleOnDesktop)
        x-bind:aria-label="$store.sidebar.isOpen ? null : @js($databaseNotificationsLabel)"
    @endif
    class="fi-sidebar-database-notifications-btn"
>
    {{ \Filament\Support\generate_icon_html(Heroicon::OutlinedBell, alias: PanelsIconAlias::SIDEBAR_OPEN_DATABASE_NOTIFICATIONS_BUTTON, size: IconSize::Large) }}

    <span
        @if ($isSidebarCollapsibleOnDesktop)
            x-show="$store.sidebar.isOpen"
            x-transition:enter="fi-transition-enter"
            x-transition:enter-start="fi-transition-enter-start"
            x-transition:enter-end="fi-transition-enter-end"
        @endif
        class="fi-sidebar-database-notifications-btn-label"
    >
        {{ __('filament-panels::layout.actions.open_database_notifications.label') }}
    </span>

    @if ($unreadNotificationsCount)
        <span
            @if ($isSidebarCollapsibleOnDesktop)
                x-show="$store.sidebar.isOpen"
                x-transition:enter="fi-transition-enter"
                x-transition:enter-start="fi-transition-enter-start"
                x-transition:enter-end="fi-transition-enter-end"
            @endif
            class="fi-sidebar-database-notifications-btn-badge-ctn"
        >
            <x-filament::badge>
                {{ $unreadNotificationsCount }}
            </x-filament::badge>
        </span>
    @endif
</button>
