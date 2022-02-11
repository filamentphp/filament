<div
    x-data="{
        notifications: {{ json_encode(session()->pull('notifications', [])) }},
        add: function (event) {
            this.notifications.push({
                id: event.timeStamp,
                status: event.detail.status,
                message: event.detail.message,
            })
        },
        remove: function (notification) {
            this.notifications = this.notifications.filter(i => i.id !== notification.id)
        },
    }"
    @notify.window="add($event)"
    class="fixed inset-x-0 top-0 z-50 p-3 pointer-events-none filament-notifications space-y-4"
    role="status"
    aria-live="polite"
    wire:ignore
>
    <template x-for="notification in notifications" :key="notification.id">
        <x-filament::notification />
    </template>
</div>
