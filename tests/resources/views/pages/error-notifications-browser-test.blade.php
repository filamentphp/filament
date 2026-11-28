<x-filament-panels::page>
    <button type="button" wire:click="save" data-testid="save">
        Save changes
    </button>

    <output data-testid="saved-count">{{ $savedCount }}</output>

    @foreach ([403, 404, 419, 500, 503] as $status)
        <button
            type="button"
            wire:click="fail({{ $status }})"
            data-testid="fail-{{ $status }}"
        >
            Fail with HTTP {{ $status }}
        </button>
    @endforeach

    <output data-testid="network-failures" wire:ignore>0</output>

    <output
        data-testid="error-notification-count"
        x-data="{ count: 0 }"
        x-on:notification-sent.camel.window="if ($event.detail.notification.status === 'danger') count++"
        x-text="count"
    >
        0
    </output>
</x-filament-panels::page>
