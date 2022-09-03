<x-filament::icon-button
    label="Database notifications"
    icon="heroicon-o-bell"
    color="secondary"
    x-on:click="$dispatch('open-modal', { id: 'notifications' })"
    :indicator="auth()->user()->unreadNotifications()->count()"
/>
