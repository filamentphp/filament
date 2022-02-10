<div
    x-data="{
        notifications: {{ json_encode(session()->pull('notifications', [])) }},
        add: function (e) {
            this.notifications.push({
                id: e.timeStamp,
                status: e.detail.status,
                message: e.detail.message,
            })
        },
        remove: function (notification) {
            this.notifications = this.notifications.filter(i => i.id !== notification.id)
        },
    }"
    @notify.window="add($event)"
    class="fixed inset-x-0 top-0 z-10 p-3 pointer-events-none filament-notifications space-y-4"
    role="status"
    aria-live="polite"
    wire:ignore
>
    <template x-for="notification in notifications" :key="notification.id">
        <x-filament::notification />
    </template>
</div>
