@php
    use Filament\Support\Icons\Heroicon;
    use Filament\View\PanelsIconAlias;
    use Illuminate\Support\Number;
@endphp

<x-filament::icon-button
    :badge="$unreadNotificationsCount ?: null"
    color="gray"
    :icon="Heroicon::OutlinedBell"
    :icon-alias="PanelsIconAlias::TOPBAR_OPEN_DATABASE_NOTIFICATIONS_BUTTON"
    icon-size="lg"
    :label="
        $unreadNotificationsCount
        ? trans_choice('filament-panels::layout.actions.open_database_notifications.label_with_unread_count', $unreadNotificationsCount, ['count' => Number::format($unreadNotificationsCount, locale: app()->getLocale())])
        : __('filament-panels::layout.actions.open_database_notifications.label')
    "
    class="fi-topbar-database-notifications-btn"
/>
