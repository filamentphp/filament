<x-filament::icon-button
    :label="__('filament::layout.buttons.database_notifications.label')"
    icon="heroicon-o-bell"
    icon-alias="panels::layout.database-notifications.trigger"
    icon-size="lg"
    color="gray"
    :indicator="$unreadNotificationsCount"
    class="-me-1 ms-4"
/>
