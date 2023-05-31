<x-filament::icon-button
    :label="__('filament::layout.buttons.database_notifications.label')"
    icon="heroicon-o-bell"
    icon-alias="app::layout.database-notifications.trigger"
    icon-size="lg"
    color="gray"
    :indicator="$unreadNotificationsCount"
    class="ms-4 -me-1"
/>
