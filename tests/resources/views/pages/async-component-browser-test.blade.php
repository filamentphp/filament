<x-filament-panels::page>
    <div
        x-data="{ events: [] }"
        x-on:async-control-event.window="events.push($event.detail)"
    >
        <x-filament::button wire:click="$toggle('isShown')">
            Remove control
        </x-filament::button>
        <output
            data-testid="lifecycle-events"
            x-text="events.join(',')"
        ></output>

        @if ($isShown)
            <div data-testid="async-region">
                {{ $this->form }}
            </div>
        @endif
    </div>

    @if ($control !== '')
        <script>
            document
                .querySelector('[data-testid="async-region"] [x-load-src]')
                .setAttribute('x-load-src', @js(url('/async-component/' . $control . '.js')))
        </script>
    @endif
</x-filament-panels::page>
