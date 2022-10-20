<div>
    <div
        @class([
            'filament-notifications pointer-events-none fixed inset-4 z-50 mx-auto flex justify-end gap-3',
            match (config('filament-notifications.layout.alignment.horizontal')) {
                'left' => 'items-start',
                'center' => 'items-center',
                'right' => 'items-end',
            },
            match (config('filament-notifications.layout.alignment.vertical')) {
                'top' => 'flex-col-reverse',
                'bottom' => 'flex-col',
            },
        ])
        role="status"
    >
        @foreach ($notifications as $notification)
            {{ $notification }}
        @endforeach
    </div>

    @if ($this->hasDatabaseNotifications())
        <x-filament-notifications::database />
    @endif

    @if ($broadcastChannel = $this->getBroadcastChannel())
        <x-filament-notifications::echo :channel="$broadcastChannel" />
    @endif
</div>
