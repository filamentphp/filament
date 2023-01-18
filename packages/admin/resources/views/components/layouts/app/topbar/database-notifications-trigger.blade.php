<x-filament::icon-button
    :label="__('filament::layout.buttons.database_notifications.label')"
    icon="heroicon-o-bell"
    :color="$unreadNotificationsCount ? 'primary' : 'secondary'"
    :indicator="$unreadNotificationsCount"
    class="ltr:ml-4 ltr:-mr-1 rtl:mr-4 rtl:-ml-1"
/>
