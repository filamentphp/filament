<x-filament::icon-button
    :label="__('filament::layout.actions.open_database_notifications.label')"
    icon="heroicon-o-bell"
    icon-alias="panels::topbar.open-database-notifications-button"
    icon-size="lg"
    color="gray"
    :indicator="$unreadNotificationsCount"
/>
