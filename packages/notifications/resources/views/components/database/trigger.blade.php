<div
    x-data="{}"
    x-on:click="$dispatch('open-modal', { id: 'database-notifications' })"
    {{ $attributes->class(['inline-block']) }}
>
    {{ $slot }}
</div>
