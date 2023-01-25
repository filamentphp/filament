<x-filament::icon-button
    :label="__('filament::layout.buttons.database_notifications.label')"
    icon="heroicon-o-bell"
    :color="$unreadNotificationsCount ? 'primary' : 'secondary'"
    :indicator="$unreadNotificationsCount"
    class="ml-4 -mr-1 rtl:-ml-1 rtl:mr-4"
/>
