@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;
@endphp

<div>
    <div
        @class([
            'fi-no',
            'fi-align-' . static::$alignment->value,
            'fi-vertical-align-' . static::$verticalAlignment->value,
        ])
        role="status"
        aria-atomic="false"
    >
        @foreach ($notifications as $notification)
            {{ $notification }}
        @endforeach
    </div>

    @if ($broadcastChannel = $this->getBroadcastChannel())
        @script
            <script>
                setUpFilamentBroadcastNotifications({
                    $wire,
                    broadcastChannel: @js($broadcastChannel),
                    event: '.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated',
                    onNotification: (notification) =>
                        $wire.handleBroadcastNotification(notification),
                })
            </script>
        @endscript
    @endif
</div>
