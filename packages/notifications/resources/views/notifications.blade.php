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
    >
        @foreach ($notifications as $notification)
            {{ $notification }}
        @endforeach
    </div>

    @if ($broadcastChannel = $this->getBroadcastChannel())
        @script
            <script>
                window.addEventListener('EchoLoaded', () => {
                    window.Echo.private(@js($broadcastChannel)).notification(
                        (notification) => {
                            setTimeout(
                                () =>
                                    $wire.handleBroadcastNotification(
                                        notification,
                                    ),
                                500,
                            )
                        },
                    )
                })

                if (window.Echo) {
                    window.dispatchEvent(new CustomEvent('EchoLoaded'))
                }
            </script>
        @endscript
    @endif
</div>
