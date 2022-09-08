<x-filament::icon-button
    label="Database notifications"
    icon="heroicon-o-bell"
    color="secondary"
    :indicator="auth()->user()->unreadNotifications()->count()"
    class="ml-4 -mr-1"
/>
