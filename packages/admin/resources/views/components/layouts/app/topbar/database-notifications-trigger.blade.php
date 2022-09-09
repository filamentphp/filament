<x-filament::icon-button
    :label="__('filament::layout.database_notifications')"
    icon="heroicon-o-bell"
    :color="$unreadNotificationsCount ? 'primary' : 'secondary'"
    :indicator="$unreadNotificationsCount"
    class="ml-4 -mr-1"
/>
