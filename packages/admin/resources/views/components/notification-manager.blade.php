<div
    x-data="{
        notifications: {{ \Illuminate\Support\Js::from(session()->pull('filament.notifications', [])) }},
        add: function (event) {
            this.notifications = this.notifications.concat(event.detail)
        },
        remove: function (notification) {
            this.notifications = this.notifications.filter(i => i.id !== notification.id)
        },
    }"
    x-on:notify.window="add($event)"
    @class([
        'flex fixed inset-0 z-50 p-3 pointer-events-none filament-notifications',
        'justify-start' => config('filament.layout.notifications.alignment') === 'left',
        'justify-center' => config('filament.layout.notifications.alignment') === 'center',
        'justify-end' => config('filament.layout.notifications.alignment') === 'right',
        'items-start' => config('filament.layout.notifications.vertical_alignment') === 'top',
        'items-center' => config('filament.layout.notifications.vertical_alignment') === 'center',
        'items-end' => config('filament.layout.notifications.vertical_alignment') === 'bottom',
    ])
    role="status"
    aria-live="polite"
    wire:ignore
>
    <div class="space-y-4">
        <template x-for="notification in notifications" :key="notification.id">
            <x-filament::notification />
        </template>
    </div>
</div>
