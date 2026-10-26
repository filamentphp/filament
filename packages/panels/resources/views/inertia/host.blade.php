<div
    x-data="inertiaHost({
                renderer: @js($renderer),
                spa: @js($spa),
                spaUrlExceptions: @js($spaUrlExceptions),
                rememberScope: @js($rememberScope),
                rememberKey: @js($rememberKey),
            })"
    wire:key="inertia-host"
    wire:ignore.self
>
    <div wire:ignore x-ignore data-inertia-container>
        {{ $inertiaView }}
    </div>
</div>
