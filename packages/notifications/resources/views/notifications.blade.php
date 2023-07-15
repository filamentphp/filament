<div>
    <div
        @class([
            'fi-no pointer-events-none fixed inset-4 z-50 mx-auto flex gap-3',
            match (static::$horizontalAlignment) {
                'left' => 'items-start',
                'center' => 'items-center',
                'right' => 'items-end',
            },
            match (static::$verticalAlignment) {
                'top' => 'flex-col-reverse justify-end',
                'bottom' => 'flex-col justify-end',
                'center' => 'flex-col justify-center'
            },
        ])
        role="status"
    >
        @foreach ($notifications as $notification)
            {{ $notification }}
        @endforeach
    </div>

    @if ($broadcastChannel = $this->getBroadcastChannel())
        <x-filament-notifications::echo :channel="$broadcastChannel" />
    @endif
</div>
