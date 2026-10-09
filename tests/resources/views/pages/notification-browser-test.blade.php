@php
    use Filament\Notifications\Notification;
    use Filament\Pages\Dashboard;
@endphp

<x-filament-panels::page>
    <div>
        <button
            type="button"
            wire:click="sendTimedNotification"
            data-testid="send-short-timed-notification"
        >
            Send short timed notification
        </button>

        <button
            type="button"
            wire:click="sendPersistentNotification"
            data-testid="send-persistent-notification"
        >
            Send persistent notification
        </button>

        <button
            type="button"
            wire:click="sendGroupedActionNotification"
            data-testid="send-grouped-action-notification"
        >
            Send grouped action notification
        </button>

        <button
            type="button"
            wire:click="showInlineNotification"
            data-testid="show-inline-notification"
        >
            Show inline notification
        </button>

        @if ($this->isInlineNotificationShown)
            <div data-testid="inline-notification">
                {!!
                    Notification::make('inline-timed-notification')
                        ->title('Inline timed notification')
                        ->inline()
                        ->duration(300)
                        ->toEmbeddedHtml()
                !!}
            </div>
        @endif

        <button
            type="button"
            x-on:click="$dispatch('close-notification', { id: 'persistent-notification' })"
            data-testid="close-notification-externally"
        >
            Close notification externally
        </button>

        <a
            href="{{ Dashboard::getUrl() }}"
            wire:navigate
            data-testid="navigate-away"
        >
            Navigate away
        </a>

        {{ $this->getResponsiveActionGroup() }}
    </div>
</x-filament-panels::page>
