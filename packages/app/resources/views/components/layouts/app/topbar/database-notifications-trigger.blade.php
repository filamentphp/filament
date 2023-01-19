<x-filament::icon-button
    :label="__('filament::layout.buttons.database_notifications.label')"
    icon="heroicon-o-bell"
    icon-alias="app::layout.database-notifications.trigger"
    icon-size="lg"
    color="gray"
    :indicator="$unreadNotificationsCount"
    class="ml-4 -mr-1 rtl:-ml-1 rtl:mr-4"
/>
