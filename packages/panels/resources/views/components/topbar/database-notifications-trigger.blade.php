<x-filament::icon-button
    :badge="$unreadNotificationsCount ?: null"
    color="gray"
    :icon="\Filament\Support\Icons\Heroicon::OutlinedBell"
    icon-alias="panels::topbar.open-database-notifications-button"
    icon-size="lg"
    :label="__('filament-panels::layout.actions.open_database_notifications.label')"
    class="fi-topbar-database-notifications-btn"
/>
